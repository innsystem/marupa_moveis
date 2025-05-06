<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->transactionService->getAllTransactions($filters));
    }

    public function show($id)
    {
        return response()->json($this->transactionService->getTransactionById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array (
  'invoice_id' => 'string',
  'integration_id' => 'string',
  'type' => 'required|string',
  'amount' => 'required|string',
  'gateway_fee' => 'required|string',
  'description' => 'required|string',
  'date' => 'required|string',
));
        return response()->json($this->transactionService->createTransaction($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array (
  'invoice_id' => 'string',
  'integration_id' => 'string',
  'type' => 'required|string',
  'amount' => 'required|string',
  'gateway_fee' => 'required|string',
  'description' => 'required|string',
  'date' => 'required|string',
));
        return response()->json($this->transactionService->updateTransaction($id, $data));
    }

    public function destroy($id)
    {
        $this->transactionService->deleteTransaction($id);
        return response()->json(['message' => 'Transaction deleted']);
    }
}
