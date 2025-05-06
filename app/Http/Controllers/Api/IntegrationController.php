<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IntegrationService;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    protected $integrationService;

    public function __construct(IntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->integrationService->getAllIntegrations($filters));
    }

    public function show($id)
    {
        return response()->json($this->integrationService->getIntegrationById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array (
  'name' => 'required|unique:integrations,name|string',
  'slug' => 'required|unique:integrations,slug|string',
  'settings' => 'required|string',
  'status' => 'required|string',
));
        return response()->json($this->integrationService->createIntegration($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array (
  'name' => 'required|unique:integrations,name|string',
  'slug' => 'required|unique:integrations,slug|string',
  'settings' => 'required|string',
  'status' => 'required|string',
));
        return response()->json($this->integrationService->updateIntegration($id, $data));
    }

    public function destroy($id)
    {
        $this->integrationService->deleteIntegration($id);
        return response()->json(['message' => 'Integration deleted']);
    }
}
