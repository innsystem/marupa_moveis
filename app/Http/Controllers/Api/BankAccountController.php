<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BankAccountService;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    protected $bank_accountService;

    public function __construct(BankAccountService $bank_accountService)
    {
        $this->bank_accountService = $bank_accountService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->bank_accountService->getAllBankAccount($filters));
    }

    public function show($id)
    {
        return response()->json($this->bank_accountService->getBankAccountById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array (
  'user_id' => 'required|integer',
  'bank_name' => 'required|string',
  'saldo' => 'required|string',
  'account_type' => 'required|string',
));
        return response()->json($this->bank_accountService->createBankAccount($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array (
  'user_id' => 'required|integer',
  'bank_name' => 'required|string',
  'saldo' => 'required|string',
  'account_type' => 'required|string',
));
        return response()->json($this->bank_accountService->updateBankAccount($id, $data));
    }

    public function destroy($id)
    {
        $this->bank_accountService->deleteBankAccount($id);
        return response()->json(['message' => 'BankAccount deleted']);
    }
}
