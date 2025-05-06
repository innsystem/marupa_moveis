<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->serviceService->getAllServices($filters));
    }

    public function show($id)
    {
        return response()->json($this->serviceService->getServiceById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array (
  'title' => 'required|string',
  'slug' => 'required|string',
  'description' => 'string',
  'status' => 'required|string',
  'sort_order' => 'required|integer',
  'is_recurring' => 'boolean',
  'single_payment_price' => 'nullable|numeric|min:0',
  'monthly_price' => 'nullable|numeric|min:0',
  'quarterly_price' => 'nullable|numeric|min:0',
  'semiannual_price' => 'nullable|numeric|min:0',
  'annual_price' => 'nullable|numeric|min:0',
  'biennial_price' => 'nullable|numeric|min:0',
));
        return response()->json($this->serviceService->createService($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array (
  'title' => 'required|string',
  'slug' => 'required|string',
  'description' => 'string',
  'status' => 'required|string',
  'sort_order' => 'required|integer',
  'is_recurring' => 'boolean',
  'single_payment_price' => 'nullable|numeric|min:0',
  'monthly_price' => 'nullable|numeric|min:0',
  'quarterly_price' => 'nullable|numeric|min:0',
  'semiannual_price' => 'nullable|numeric|min:0',
  'annual_price' => 'nullable|numeric|min:0',
  'biennial_price' => 'nullable|numeric|min:0',
));
        return response()->json($this->serviceService->updateService($id, $data));
    }

    public function destroy($id)
    {
        $this->serviceService->deleteService($id);
        return response()->json(['message' => 'Service deleted']);
    }
}
