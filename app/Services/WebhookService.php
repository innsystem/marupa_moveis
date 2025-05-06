<?php

namespace App\Services;

use App\Integrations\MercadoPagoIntegration;
use App\Jobs\ProcessNotificationJob;
use App\Models\Webhook;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    protected $transactionService;
    protected $paymentIntegrations;
    protected $invoiceService;

    public function __construct(
        TransactionService $transactionService,
        InvoiceService $invoiceService
    )
    {
        // Aqui podemos registrar múltiplas integrações dinamicamente
        $this->paymentIntegrations = [
            'mercadopago' => new MercadoPagoIntegration(),
            // 'paypal' => new PayPalService(),
            // 'stripe' => new StripeService(),
        ];

        $this->transactionService = $transactionService;
        $this->invoiceService = $invoiceService;
    }

    public function createWebhook($data)
    {
        return Webhook::create($data);
    }

    public function checkPaymentStatus(Webhook $webhook)
    {
        $integration = $webhook->integration->slug; // Exemplo: 'mercadopago'

        if (!isset($this->paymentIntegrations[$integration])) {
            Log::error("Integração {$integration} não implementada!");
            return;
        }

        // Inicia o service de Integração com Meio de Pagamento
        $service = $this->paymentIntegrations[$integration];

        // Usa a funcao de getOrder para obter informações do pagamento
        $getPayment = $service->getOrder($webhook->payment_code);

        if (!$getPayment) {
            Log::error("Erro ao obter status do pagamento para o webhook ID #{$webhook->id}");
            return;
        }

        // Traduz a mensagem de resposta do Pagamento
        $paymentResult = $service->handlePaymentResponse($getPayment, $webhook->invoice);

        // ATENÇÃO - ATENÇÃO - ATENÇÃO
        // $paymentResult['status'] = 'paid';

        if (isset($paymentResult) && isset($paymentResult['status'])) {
            if ($paymentResult['status'] === 'paid' || $paymentResult['status'] === 'approved') {
                $this->confirmPayment($webhook);
            } elseif ($paymentResult['status'] === 'rejected' || $paymentResult['status'] === 'cancelled') {
                $webhook->status = 26;
                $webhook->response_json = $paymentResult['response_json'];
                $webhook->save();
            }
        } else {
            $webhook->response_json = $paymentResult['response_json'];
            $webhook->save();
        }
    }

    protected function confirmPayment(Webhook $webhook)
    {
        $invoice = Invoice::find($webhook->invoice_id);
        if (!$invoice) return;

        // Atualizar status do webhook
        $webhook->status = 24; // Pago
        $webhook->save();

        // Calcular taxa do gateway (específica do WebhookService)
        $gatewayFee = 0;
        $integration = $invoice->integration;
        
        if ($integration) {
            // Define dinamicamente o nome do campo de taxa com base no método de pagamento
            $feeKey = 'fee_' . $invoice->method_type; // Exemplo: 'fee_pix', 'fee_boleto', 'fee_credit_card'

            // Se a taxa existir na configuração, calcula a taxa do gateway
            if (isset($integration->settings[$feeKey])) {
                $percentage = floatval($integration->settings[$feeKey]); // Exemplo: 2.99 para 2.99%
                $gatewayFee = ($invoice->total * $percentage) / 100;
            }
        }
        
        // Usar InvoiceService para confirmar pagamento
        // Passar 0 para não notificar cliente pois faremos isso manualmente
        // Passar a taxa do gateway calculada
        $this->invoiceService->confirmPayment($invoice->id, 0, $gatewayFee);
        
        // Enviar notificações aqui para garantir que sejam enviadas quando o pagamento vier por webhook
        $notificationData = [
            'name' => $invoice->user->name,
            'invoice_id' => $invoice->id,
        ];

        dispatch(new ProcessNotificationJob('email', $invoice->user->email, 'Invoice', 'email', 'payment_confirmed', $notificationData));
        if(isset($invoice->user->phone_ddi) && !empty($invoice->user->phone_ddi)) {
            dispatch(new ProcessNotificationJob('whatsapp', $invoice->user->phone_ddi, 'Invoice', 'whatsapp', 'payment_confirmed', $notificationData));
        }
    }

}
