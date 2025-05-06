<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserServiceService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class UserServiceController extends Controller
{
    protected $userServiceService;

    public function __construct(UserServiceService $userServiceService)
    {
        $this->userServiceService = $userServiceService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['service_name', 'status', 'start_date', 'end_date', 'user_id']);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        
        if (isset($filters['user_id'])) {
            $userId = $filters['user_id'];
            unset($filters['user_id']);
            return response()->json($this->userServiceService->getAllUserServices($userId, $filters, true, $perPage));
        } else {
            return response()->json($this->userServiceService->getAllServicesFromAllUsers($filters, $perPage));
        }
    }

    public function show($id)
    {
        return response()->json($this->userServiceService->getUserServiceById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'period' => 'required|in:monthly,quarterly,semiannual,annual,biennial,once',
            'status' => 'required|exists:statuses,id',
            'metadata' => 'nullable|array',
        ]);

        return response()->json($this->userServiceService->createUserService($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'period' => 'required|in:monthly,quarterly,semiannual,annual,biennial,once',
            'status' => 'required|exists:statuses,id',
            'metadata' => 'nullable|array',
        ]);

        return response()->json($this->userServiceService->updateUserService($id, $data));
    }

    public function destroy($id)
    {
        $this->userServiceService->deleteUserService($id);
        return response()->json(['message' => 'Serviço do usuário excluído com sucesso']);
    }

    public function generateInvoice(Request $request, $serviceId, InvoiceService $invoiceService)
    {
        $userService = $this->userServiceService->getUserServiceById($serviceId);
        
        $data = $request->validate([
            'due_date' => 'nullable|date',
            'custom_description' => 'nullable|string',
            'custom_amount' => 'nullable|numeric|min:0',
        ]);
        
        $userId = $userService->user_id;
        $dueDate = $data['due_date'] ?? now()->addDays(3)->format('Y-m-d');
        $description = $data['custom_description'] ?? null;
        $amount = $data['custom_amount'] ?? null;
        
        $invoice = $invoiceService->generateInvoiceFromUserService($userService, $dueDate, $description, $amount);
        
        return response()->json($invoice, 201);
    }
} 