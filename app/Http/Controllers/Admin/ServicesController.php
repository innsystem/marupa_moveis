<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\Service;
use Carbon\Carbon;
use App\Services\ServiceService;

class ServicesController extends Controller
{
    public $name = 'Serviço'; //  singular
    public $folder = 'admin.pages.services';

    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'date_range']);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        if (!empty($filters['name'])) {
            $query['name'] = $filters['name'];
        }

        if (!empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }

        if (!empty($filters['date_range'])) {
            [$startDate, $endDate] = explode(' até ', $filters['date_range']);
            $query['start_date'] = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $query['end_date'] = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        }

        $results = $this->serviceService->getAllServices($query, true, $perPage);

        if ($request->ajax()) {
            return view($this->folder . '.index_load', compact('results'));
        }

        return view($this->folder . '.index_load', compact('results'));
    }

    public function create()
    {
        $statuses = Status::default();

        return view($this->folder . '.form', compact('statuses'));
    }

    public function store(Request $request)
    {
        $result = $request->all();

        $rules = array(
            'title' => 'required',
            'slug' => 'required',
            'description' => 'nullable',
            'status' => 'required',
            'sort_order' => 'required',
            'is_recurring' => 'boolean',
            'single_payment_price' => 'nullable|numeric|min:0',
            'monthly_price' => 'nullable|numeric|min:0',
            'quarterly_price' => 'nullable|numeric|min:0',
            'semiannual_price' => 'nullable|numeric|min:0',
            'annual_price' => 'nullable|numeric|min:0',
            'biennial_price' => 'nullable|numeric|min:0',
        );
        $messages = array(
            'title.required' => 'Título é obrigatório',
            'slug.required' => 'Slug é obrigatório',
            'description.required' => 'Descrição é obrigatória',
            'description.nullable' => 'Descrição pode ser nula',
            'status.required' => 'Status é obrigatório',
            'sort_order.required' => 'Ordem de exibição é obrigatória',
            'is_recurring.boolean' => 'Recorrência deve ser um valor booleano',
            'single_payment_price.numeric' => 'Preço de pagamento único deve ser um número',
            'single_payment_price.min' => 'Preço de pagamento único deve ser maior ou igual a zero',
            'monthly_price.numeric' => 'Preço mensal deve ser um número',
            'monthly_price.min' => 'Preço mensal deve ser maior ou igual a zero',
            'quarterly_price.numeric' => 'Preço trimestral deve ser um número',
            'quarterly_price.min' => 'Preço trimestral deve ser maior ou igual a zero',
            'semiannual_price.numeric' => 'Preço semestral deve ser um número',
            'semiannual_price.min' => 'Preço semestral deve ser maior ou igual a zero',
            'annual_price.numeric' => 'Preço anual deve ser um número',
            'annual_price.min' => 'Preço anual deve ser maior ou igual a zero',
            'biennial_price.numeric' => 'Preço bienal deve ser um número',
            'biennial_price.min' => 'Preço bienal deve ser maior ou igual a zero',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $result['single_payment_price'] = $result['single_payment_price'] ?? 0;
        $result['monthly_price'] = $result['monthly_price'] ?? 0;
        $result['quarterly_price'] = $result['quarterly_price'] ?? 0;
        $result['semiannual_price'] = $result['semiannual_price'] ?? 0;
        $result['annual_price'] = $result['annual_price'] ?? 0;
        $result['biennial_price'] = $result['biennial_price'] ?? 0;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('services', 'public');
            $result['image'] = $imagePath;
        }

        $service = $this->serviceService->createService($result);

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->serviceService->getServiceById($id);
        $statuses = Status::default();

        return view($this->folder . '.form', compact('result', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        // 'email'         => "unique:services,email,$id,id",
        $rules = array(
            'title' => 'required',
            'slug' => 'required',
            'description' => 'nullable',
            'status' => 'required',
            'sort_order' => 'required',
            'is_recurring' => 'boolean',
            'single_payment_price' => 'nullable|numeric|min:0',
            'monthly_price' => 'nullable|numeric|min:0',
            'quarterly_price' => 'nullable|numeric|min:0',
            'semiannual_price' => 'nullable|numeric|min:0',
            'annual_price' => 'nullable|numeric|min:0',
            'biennial_price' => 'nullable|numeric|min:0',
        );
        $messages = array(
            'title.required' => 'Título é obrigatório',
            'slug.required' => 'Slug é obrigatório',
            'description.required' => 'Descrição é obrigatória',
            'description.nullable' => 'Descrição pode ser nula',
            'status.required' => 'Status é obrigatório',
            'sort_order.required' => 'Ordem de exibição é obrigatória',
            'is_recurring.boolean' => 'Recorrência deve ser um valor booleano',
            'single_payment_price.numeric' => 'Preço de pagamento único deve ser um número',
            'single_payment_price.min' => 'Preço de pagamento único deve ser maior ou igual a zero',
            'monthly_price.numeric' => 'Preço mensal deve ser um número',
            'monthly_price.min' => 'Preço mensal deve ser maior ou igual a zero',
            'quarterly_price.numeric' => 'Preço trimestral deve ser um número',
            'quarterly_price.min' => 'Preço trimestral deve ser maior ou igual a zero',
            'semiannual_price.numeric' => 'Preço semestral deve ser um número',
            'semiannual_price.min' => 'Preço semestral deve ser maior ou igual a zero',
            'annual_price.numeric' => 'Preço anual deve ser um número',
            'annual_price.min' => 'Preço anual deve ser maior ou igual a zero',
            'biennial_price.numeric' => 'Preço bienal deve ser um número',
            'biennial_price.min' => 'Preço bienal deve ser maior ou igual a zero',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $result['single_payment_price'] = $result['single_payment_price'] ?? 0;
        $result['monthly_price'] = $result['monthly_price'] ?? 0;
        $result['quarterly_price'] = $result['quarterly_price'] ?? 0;
        $result['semiannual_price'] = $result['semiannual_price'] ?? 0;
        $result['annual_price'] = $result['annual_price'] ?? 0;
        $result['biennial_price'] = $result['biennial_price'] ?? 0;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('services', 'public');
            $result['image'] = $imagePath;
        }

        $service = $this->serviceService->updateService($id, $result);

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        $this->serviceService->deleteService($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }
}
