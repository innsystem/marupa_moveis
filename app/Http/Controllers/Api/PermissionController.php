<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->permissionService->getAllPermissions($filters));
    }

    public function show($id)
    {
        return response()->json($this->permissionService->getPermissionById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array (
  'title' => 'required|unique:permissions,title|string',
  'key' => 'required|unique:permissions,key|string',
));
        return response()->json($this->permissionService->createPermission($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array (
  'title' => 'required|unique:permissions,title|string',
  'key' => 'required|unique:permissions,key|string',
));
        return response()->json($this->permissionService->updatePermission($id, $data));
    }

    public function destroy($id)
    {
        $this->permissionService->deletePermission($id);
        return response()->json(['message' => 'Permission deleted']);
    }
}
