<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->invoiceService->getAllInvoices($filters));
    }

    public function show($id)
    {
        return response()->json($this->invoiceService->getInvoiceById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array (
  'user_id' => 'required|string',
  'integration_id' => 'required|string',
  'method_type' => 'string',
  'total' => 'required|string',
  'status' => 'required|string',
  'due_at' => 'required|string',
));
        return response()->json($this->invoiceService->createInvoice($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array (
  'user_id' => 'required|string',
  'integration_id' => 'required|string',
  'method_type' => 'string',
  'total' => 'required|string',
  'status' => 'required|string',
  'due_at' => 'required|string',
));
        return response()->json($this->invoiceService->updateInvoice($id, $data));
    }

    public function destroy($id)
    {
        $this->invoiceService->deleteInvoice($id);
        return response()->json(['message' => 'Invoice deleted']);
    }
}
