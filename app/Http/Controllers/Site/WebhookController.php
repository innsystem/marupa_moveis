<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Page;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    public function mercadoPago(Request $request)
    {
        \Log::info('Webhook MP :: ' . json_encode($request));

        return true;
    }

    public function checkPaymentStatus($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);

        if ($invoice->latestPaidWebhook) {
            // Recupera o webhook pendente mais recente
            $latestWebhook = $invoice->latestPaidWebhook;

            if (!$latestWebhook) {
                return response()->json(['status' => 'no_webhook'], 200);
            }

            // Verifica se o status mudou (24 = Pago)
            if ($latestWebhook->status == 24) {
                return response()->json(['status' => 'paid'], 200);
            }
        }

        return response()->json(['status' => 'pending'], 200);
    }

    public function shopeeWebhook(Request $request)
    {
        \Log::info('WebHook Shopee :: ' . json_encode($request));

        return true;
    }

    public function mercadoLivreWebhook(Request $request)
    {
        \Log::info('WebHook ML :: ' . json_encode($request));

        return true;
    }
}
