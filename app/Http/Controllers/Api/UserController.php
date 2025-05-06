<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->userService->getAllUsers($filters));
    }

    public function show($id)
    {
        return response()->json($this->userService->getUserById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array(
            'name' => 'required|string',
            'email' => 'required|unique:users,email|string',
            'password' => 'string',
            'password_code' => 'string',
            'type' => 'required|string',
            'document' => 'string',
            'phone' => 'string',
            'remember_token' => 'required|string',
        ));
        return response()->json($this->userService->createUser($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array(
            'name' => 'required|string',
            'email' => 'required|unique:users,email|string',
            'password' => 'string',
            'password_code' => 'string',
            'type' => 'required|string',
            'document' => 'string',
            'phone' => 'string',
            'remember_token' => 'required|string',
        ));
        return response()->json($this->userService->updateUser($id, $data));
    }

    public function destroy($id)
    {
        $this->userService->deleteUser($id);
        return response()->json(['message' => 'User deleted']);
    }
}
