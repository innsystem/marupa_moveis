<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use Carbon\Carbon;
use App\Services\StatusService;

class StatusesController extends Controller
{
    public $name = 'Status'; //  singular
    public $folder = 'admin.pages.statuses';

    protected $statusService;

    public function __construct(StatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'date_range']);

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

        $results = $this->statusService->getAllStatusesGroups($filters);

        return view($this->folder . '.index_load', compact('results'));
    }

    public function create()
    {
        return view($this->folder . '.form');
    }

    public function store(Request $request)
    {
        $result = $request->all();

        $rules = array(
            'name' => 'required',
            'type' => 'required',
            'description' => 'nullable',
            'color' => 'required',
            'icon' => 'nullable',
        );
        $messages = array(
            'name.required' => 'name é obrigatório',
            'type.required' => 'type é obrigatório',
            'description.required' => 'description é obrigatório',
            'description.nullable' => 'description pode ser nulo',
            'color.required' => 'color é obrigatório',
            'icon.required' => 'icon é obrigatório',
            'icon.nullable' => 'icon pode ser nulo',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $status = $this->statusService->createStatus($result);

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->statusService->getStatusById($id);
        return view($this->folder . '.form', compact('result'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        // 'email'         => "unique:statuses,email,$id,id",
        $rules = array(
            'name' => 'required',
            'type' => 'required',
            'description' => 'nullable',
            'color' => 'required',
            'icon' => 'nullable',
        );
        $messages = array(
            'name.required' => 'name é obrigatório',
            'type.required' => 'type é obrigatório',
            'description.required' => 'description é obrigatório',
            'description.nullable' => 'description pode ser nulo',
            'color.required' => 'color é obrigatório',
            'icon.required' => 'icon é obrigatório',
            'icon.nullable' => 'icon pode ser nulo',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $status = $this->statusService->updateStatus($id, $result);

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        $this->statusService->deleteStatus($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }
}
