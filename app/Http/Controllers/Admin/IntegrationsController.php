<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\Integration;
use Carbon\Carbon;
use App\Services\IntegrationService;
use Illuminate\Support\Facades\Cache;

class IntegrationsController extends Controller
{
    public $name = 'Integração'; //  singular
    public $folder = 'admin.pages.integrations';

    protected $integrationService;

    public function __construct(IntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'type']);

        if (!empty($filters['name'])) {
            $query['name'] = $filters['name'];
        }

        if (!empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }

        if (!empty($filters['type'])) {
            $query['type'] = $filters['type'];
        }        

        $results = $this->integrationService->getAllIntegrationsGroupedByType($filters);

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
            'name' => 'required|unique:integrations,name',
            'slug' => 'required|unique:integrations,slug',
            'status' => 'required',
        );
        $messages = array(
            'name.required' => 'Nome é obrigatório',
            'name.unique' => 'Nome já existe',
            'slug.required' => 'Nome amigável é obrigatório',
            'slug.unique' => 'Nome amigável já existe',
            'status.required' => 'Status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $integration = $this->integrationService->createIntegration($result);

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->integrationService->getIntegrationById($id);
        $statuses = Status::default();

        return view($this->folder . '.form_' . str_replace('-', '_', $result->slug), compact('result', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        $rules = array(
            'status' => 'required',
        );
        $messages = array(
            'status.required' => 'Status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $result['settings'] = $request->except(['status','description']);

        $integration = $this->integrationService->updateIntegration($id, $result);

        try {
            Cache::forget('integrations');
        } catch (\Exception $e) {
            \Log::error('IntegrationsController :: update' . $e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        $this->integrationService->deleteIntegration($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }
}
