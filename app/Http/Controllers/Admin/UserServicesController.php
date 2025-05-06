<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Service;
use App\Models\Status;
use App\Models\UserService;
use Carbon\Carbon;
use App\Services\UserServiceService;
use App\Services\InvoiceService;

class UserServicesController extends Controller
{
    public $name = 'Serviço de Cliente'; // singular
    public $folder = 'admin.pages.user_services';

    protected $userServiceService;

    public function __construct(UserServiceService $userServiceService)
    {
        $this->userServiceService = $userServiceService;
    }

    public function index($userId)
    {
        $user = User::findOrFail($userId);
        return view($this->folder . '.index', compact('user'));
    }

    public function load(Request $request, $userId)
    {
        $query = [];
        $filters = $request->only(['service_name', 'status', 'date_range']);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        if (!empty($filters['service_name'])) {
            $query['service_name'] = $filters['service_name'];
        }

        if (!empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }

        if (!empty($filters['date_range'])) {
            [$startDate, $endDate] = explode(' até ', $filters['date_range']);
            $query['start_date'] = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $query['end_date'] = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        }

        $user = User::findOrFail($userId);
        $results = $this->userServiceService->getAllUserServices($userId, $query, true, $perPage);

        if ($request->ajax()) {
            return view($this->folder . '.index_load', compact('results', 'user'));
        }

        return view($this->folder . '.index_load', compact('results', 'user'));
    }

    public function create($userId)
    {
        $user = User::findOrFail($userId);
        $services = Service::where('status', 1)->get(); // Apenas serviços ativos
        $statuses = Status::where('type', 'general')->get();

        return view($this->folder . '.form', compact('user', 'services', 'statuses'));
    }

    public function store(Request $request, $userId)
    {
        $data = $request->all();
        
        $data['user_id'] = $userId;

        $rules = array(
            'service_id' => 'required|exists:services,id',
            'start_date' => 'required',
            'end_date' => 'required',
            'price' => 'required|numeric|min:0',
            'period' => 'required|in:monthly,quarterly,semiannual,annual,biennial,once',
            'status' => 'required|exists:statuses,id',
        );
        
        $messages = array(
            'service_id.required' => 'serviço é obrigatório',
            'service_id.exists' => 'serviço não existe',
            'start_date.required' => 'data de início é obrigatória',
            'price.required' => 'preço é obrigatório',
            'price.numeric' => 'preço deve ser um número',
            'price.min' => 'preço deve ser maior ou igual a zero',
            'period.required' => 'período é obrigatório',
            'period.in' => 'período deve ser mensal, trimestral, semestral, anual, bienal ou único',
            'status.required' => 'status é obrigatório',
            'status.exists' => 'status não existe',
        );

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        // Metadata para título e valor adicionais
        if ($request->has('metadata_title')) {
            $metadataTitles = $request->input('metadata_title');
            $metadataValues = $request->input('metadata_value');
            
            if (!empty($metadataTitles)) {
                $metadata = [];
                foreach ($metadataTitles as $key => $title) {
                    if (!empty($title) && isset($metadataValues[$key])) {
                        $metadata[$title] = $metadataValues[$key];
                    }
                }
                $data['metadata'] = $metadata;
            }
        }

        $userService = $this->userServiceService->createUserService($data);

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($userId, $id)
    {
        $user = User::findOrFail($userId);
        $result = $this->userServiceService->getUserServiceById($id);
        $services = Service::where('status', 1)->get(); // Apenas serviços ativos
        $statuses = Status::where('type', 'general')->get();

        return view($this->folder . '.form', compact('user', 'result', 'services', 'statuses'));
    }

    public function update(Request $request, $userId, $id)
    {
        $data = $request->all();
        
        $rules = array(
            'service_id' => 'required|exists:services,id',
            'start_date' => 'required',
            'end_date' => 'required',
            'price' => 'required|numeric|min:0',
            'period' => 'required|in:monthly,quarterly,semiannual,annual,biennial,once',
            'status' => 'required|exists:statuses,id',
        );
        
        $messages = array(
            'service_id.required' => 'serviço é obrigatório',
            'service_id.exists' => 'serviço não existe',
            'start_date.required' => 'data de início é obrigatória',
            'price.required' => 'preço é obrigatório',
            'price.numeric' => 'preço deve ser um número',
            'price.min' => 'preço deve ser maior ou igual a zero',
            'period.required' => 'período é obrigatório',
            'period.in' => 'período deve ser mensal, trimestral, semestral, anual, bienal ou único',
            'status.required' => 'status é obrigatório',
            'status.exists' => 'status não existe',
        );

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        // Metadata para título e valor adicionais
        if ($request->has('metadata_title')) {
            $metadataTitles = $request->input('metadata_title');
            $metadataValues = $request->input('metadata_value');
            
            if (!empty($metadataTitles)) {
                $metadata = [];
                foreach ($metadataTitles as $key => $title) {
                    if (!empty($title) && isset($metadataValues[$key])) {
                        $metadata[$title] = $metadataValues[$key];
                    }
                }
                $data['metadata'] = $metadata;
            }
        }

        $userService = $this->userServiceService->updateUserService($id, $data);

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($userId, $id)
    {
        $this->userServiceService->deleteUserService($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }

    public function generateInvoice(Request $request, $userId, $serviceId, InvoiceService $invoiceService)
    {
        try {
            // Obtem do request se deve notificar o usuário
            $notifyUser = $request->input('notify_user', false);
            
            $invoice = $invoiceService->generateRecurringInvoiceForUserServices($userId, [
                'anticipate' => true,
                'serviceId' => $serviceId,
                'notifyUser' =>  $notifyUser
            ]);
            
            return response()->json(['message' => 'Fatura gerada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // Nova função para exibir todos os serviços de todos os clientes
    public function indexAll(Request $request)
    {
        // Apenas para manter os filtros preenchidos no formulário
        $filters = $request->only(['service_name', 'status', 'date_range', 'user_name']);
        return view('admin.pages.user_services.index_all', compact('filters'));
    }

    // Função para carregar os serviços de todos os clientes via AJAX
    public function loadAll(Request $request)
    {
        $filters = $request->only(['service_name', 'status', 'date_range', 'user_name']);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $query = UserService::query()->with(['user', 'service', 'statusRelation']);

        if (!empty($filters['service_name'])) {
            $query->whereHas('service', function($q) use ($filters) {
                $q->where('title', 'LIKE', '%' . $filters['service_name'] . '%');
            });
        }
        if (!empty($filters['user_name'])) {
            $query->whereHas('user', function($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['user_name'] . '%');
            });
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['date_range'])) {
            [$startDate, $endDate] = explode(' até ', $filters['date_range']);
            $query->whereBetween('created_at', [
                Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d'),
                Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d')
            ]);
        }

        $results = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('admin.pages.user_services.index_load', compact('results'));
    }
} 