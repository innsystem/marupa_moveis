<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use App\Services\InvoiceService;
use App\Services\WebhookService;
use App\Integrations\MercadoPagoIntegration;


class InvoicesController extends Controller
{
    public $name = 'Fatura'; //  singular
    public $folder = 'admin.pages.invoices';

    protected $invoiceService;
    protected $webhookService;
    protected $mercadoPagoIntegration;

    public function __construct(InvoiceService $invoiceService, WebhookService $webhookService, MercadoPagoIntegration $mercadoPagoIntegration)
    {
        $this->invoiceService = $invoiceService;
        $this->webhookService = $webhookService;
        $this->mercadoPagoIntegration = $mercadoPagoIntegration;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $filters = $request->only(['name', 'invoice_id', 'status', 'date_range']);
        $filters['per_page'] = $request->input('per_page', 10);

        $data = $this->invoiceService->getAllInvoices($filters);

        return view($this->folder . '.index_load', $data);
    }

    public function create()
    {
        $statuses = Status::forInvoices();
        $users = User::all();
        $integrations = Integration::where('type', 'payments')->get();

        return view($this->folder . '.form', compact('statuses', 'users', 'integrations'));
    }

    public function store(Request $request)
    {
        $result = $request->all();

        $rules = [
            'user_id' => 'required|exists:users,id',
            'integration_id' => 'required|exists:integrations,id',
            'method_type' => 'nullable|in:pix,boleto,credit_card',
            'status' => 'required|integer',
            'due_at' => 'required',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_unit' => 'required|numeric|min:0',
            'items.*.price_total' => 'required|numeric|min:0',
        ];

        $messages = [
            'user_id.required' => 'O cliente é obrigatório.',
            'integration_id.required' => 'A integração de pagamento é obrigatória.',
            'due_at.required' => 'A data de vencimento é obrigatória.',
            'items.required' => 'A fatura precisa ter pelo menos um item.',
        ];

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $invoice = $this->invoiceService->createInvoice($result);

        return response()->json($this->name . ' adicionada com sucesso', 200);
    }

    public function show($id)
    {
        $result = $this->invoiceService->getInvoiceById($id);

        if (isset($result->latestWebhook)) {
            $webhookCheckStatus = $this->webhookService->checkPaymentStatus($result->latestWebhook);
        }

        $settingsMercadoPago = $this->mercadoPagoIntegration->getSettings();

        return view($this->folder . '.show', compact('result', 'settingsMercadoPago'));
    }

    public function edit($id)
    {
        $result = $this->invoiceService->getInvoiceById($id);
        $statuses = Status::forInvoices();
        $users = User::all();
        $integrations = Integration::where('type', 'payments')->get();

        return view($this->folder . '.form', compact('result', 'statuses', 'users', 'integrations'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        $rules = [
            'integration_id' => 'required|exists:integrations,id',
            'method_type' => 'nullable|in:pix,boleto,credit_card',
            'status' => 'required|integer',
            'due_at' => 'required',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_unit' => 'required|numeric|min:0',
            'items.*.price_total' => 'required|numeric|min:0',
        ];

        $messages = [
            'integration_id.required' => 'A integração de pagamento é obrigatória.',
            'due_at.required' => 'A data de vencimento é obrigatória.',
            'items.required' => 'A fatura precisa ter pelo menos um item.',
        ];

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $invoice = $this->invoiceService->updateInvoice($id, $result);

        return response()->json($this->name . ' atualizada com sucesso', 200);
    }

    public function delete($id)
    {
        $this->invoiceService->deleteInvoice($id);

        return response()->json($this->name . ' excluída com sucesso', 200);
    }

    public function cancel($id)
    {
        $result = $this->invoiceService->cancelInvoice($id);
        if (!$result) {
            return response()->json('Fatura não encontrada ou não foi possível cancelar.', 404);
        }
        return response()->json($this->name . ' cancelada com sucesso', 200);
    }

    public function confirmPayment(Request $request, $id)
    {
        $notifyClient = $request->input('notify', 0);
        $this->invoiceService->confirmPayment($id, $notifyClient);

        return response()->json('Pagamento confirmado com sucesso', 200);
    }

    public function sendReminder($id)
    {
        $this->invoiceService->sendReminder($id);

        return response()->json('Lembrete enviado com sucesso', 200);
    }

    public function generatePayment(Request $request, $id)
    {
        $result = $request->all();
        $invoice = $this->invoiceService->getInvoiceById($id);

        $paymentMethod = $request->input('payment_method') ?? $invoice->method_type;

        if (!in_array($paymentMethod, ['pix', 'boleto', 'credit_card'])) {
            return response()->json('Método de pagamento inválido.', 422);
        }

        $responseData = $this->mercadoPagoIntegration->processPayment($invoice, $paymentMethod, $result);
        
        if($paymentMethod == 'credit_card' && isset($invoice->latestWebhook) && $invoice->latestWebhook->payment_code != '') {
            $this->webhookService->checkPaymentStatus($invoice->latestWebhook);
        }

        return $responseData;
    }
    
    public function loadInstallments(Request $request, $id)
    {
        $result = $this->invoiceService->getInvoiceById($id);
        $card_number = $request->input('card_number');
        $card_brand = $this->mercadoPagoIntegration->getBinCard($card_number);

        if ($card_brand == null) {
            return response()->json('Não foi possível identificar o cartão.', 422);
        }

        $installments = $this->mercadoPagoIntegration->calcularParcelas($result->total, $card_brand);

        return response()->json([
            'card_brand' => $card_brand,
            'installments' => $installments
        ]);
    }

    public function formatDocument($document)
    {
        return preg_replace('/\D/', '', $document);
    }

    public function getLatestWebhook($id)
    {
        $invoice = $this->invoiceService->getInvoiceById($id);
        $webhook = $invoice->latestWebhook;
        
        if (!$webhook) {
            return response()->json(['success' => false, 'message' => 'Nenhum webhook encontrado'], 404);
        }
        
        return response()->json([
            'success' => true, 
            'data' => $webhook->response_json
        ]);
    }
}
