<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserGroupService;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    protected $user_groupService;

    public function __construct(UserGroupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->user_groupService->getAllUserGroups($filters));
    }

    public function show($id)
    {
        return response()->json($this->user_groupService->getUserGroupById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array (
  'name' => 'required|unique:user_groups,name|string',
));
        return response()->json($this->user_groupService->createUserGroup($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array (
  'name' => 'required|unique:user_groups,name|string',
));
        return response()->json($this->user_groupService->updateUserGroup($id, $data));
    }

    public function destroy($id)
    {
        $this->user_groupService->deleteUserGroup($id);
        return response()->json(['message' => 'UserGroup deleted']);
    }
}
