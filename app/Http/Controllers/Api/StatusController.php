<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StatusService;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    protected $statusService;

    public function __construct(StatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->statusService->getAllStatuses($filters));
    }

    public function show($id)
    {
        return response()->json($this->statusService->getStatusById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array(
            'name' => 'required|string',
            'type' => 'required|string',
            'description' => 'string',
            'color' => 'required|string',
            'icon' => 'string',
        ));
        return response()->json($this->statusService->createStatus($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array(
            'name' => 'required|string',
            'type' => 'required|string',
            'description' => 'string',
            'color' => 'required|string',
            'icon' => 'string',
        ));
        return response()->json($this->statusService->updateStatus($id, $data));
    }

    public function destroy($id)
    {
        $this->statusService->deleteStatus($id);
        return response()->json(['message' => 'Status deleted']);
    }
}
