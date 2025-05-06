<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessNotificationJob;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\UserService;
use App\Services\CustomerService;
use App\Services\ServiceService;
use App\Services\InvoiceService;
use App\Services\TransactionService;
// use App\Integrations\GoogleAnalyticsIntegration;

class BaseAdminController extends Controller
{
    protected $userService;
    protected $customerService;
    protected $serviceService;
    protected $invoiceService;
    protected $transactionService;

    public function __construct(
        UserService $userService,
        CustomerService $customerService,
        ServiceService $serviceService,
        InvoiceService $invoiceService,
        TransactionService $transactionService
    ) {
        $this->userService = $userService;
        $this->customerService = $customerService;
        $this->serviceService = $serviceService;
        $this->invoiceService = $invoiceService;
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        $metrics = $this->dashboardMetrics();
        $chartFaturas = $this->chartFaturasUltimosMeses();
        return view('admin.pages.home', compact('metrics', 'chartFaturas'));
    }

    /**
     * Retorna as métricas principais do sistema para o dashboard
     */
    protected function dashboardMetrics()
    {
        $totalUsuarios = $this->userService->getAllUsers()->count();
        $totalClientes = $this->customerService->getAllCustomers()['totalCustomers'];
        $totalServicos = $this->serviceService->getAllServices()->count();
        $faturas = $this->invoiceService->getAllInvoices();
        $totalFaturas = $faturas['totalInvoices'] ?? 0;
        $faturasPagas = $faturas['paidInvoices'] ?? 0;
        $faturasPendentes = $faturas['unpaidInvoices'] ?? 0;
        $valorTotalFaturas = $faturas['totalAmount'] ?? 0;
        $valorFaturasPagas = $faturas['paidAmount'] ?? 0;
        $valorFaturasPendentes = $faturas['unpaidAmount'] ?? 0;
        $transacoes = $this->transactionService->getAllTransactions();
        $totalTransacoes = $transacoes['totalTransactions'] ?? 0;
        $valorTransacoes = $transacoes['totalAmount'] ?? 0;
        $valorRecebido = $transacoes['incomeAmount'] ?? 0;
        $valorPago = $transacoes['expenseAmount'] ?? 0;

        return [
            'total_usuarios' => $totalUsuarios,
            'total_clientes' => $totalClientes,
            'total_servicos' => $totalServicos,
            'total_faturas' => $totalFaturas,
            'faturas_pagas' => $faturasPagas,
            'faturas_pendentes' => $faturasPendentes,
            'valor_total_faturas' => $valorTotalFaturas,
            'valor_faturas_pagas' => $valorFaturasPagas,
            'valor_faturas_pendentes' => $valorFaturasPendentes,
            'total_transacoes' => $totalTransacoes,
            'valor_transacoes' => $valorTransacoes,
            'valor_recebido' => $valorRecebido,
            'valor_pago' => $valorPago,
        ];
    }

    /**
     * Retorna dados de faturas emitidas e pagas nos últimos 6 meses para o gráfico do dashboard
     */
    protected function chartFaturasUltimosMeses()
    {
        $faturas = $this->invoiceService->getAllInvoices();
        $emitidasPorMes = [];
        $pagasPorMes = [];
        $meses = [];
        $now = now();
        for ($i = 5; $i >= 0; $i--) {
            $mes = $now->copy()->subMonths($i);
            $label = $mes->format('M/Y');
            $meses[] = $label;
            $emitidasPorMes[$label] = 0;
            $pagasPorMes[$label] = 0;
        }
        if (isset($faturas['invoices'])) {
            foreach ($faturas['invoices'] as $fatura) {
                try {
                    $mesFatura = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $fatura->created_at)->format('M/Y');
                } catch (\Exception $e) {
                    $mesFatura = \Carbon\Carbon::parse($fatura->created_at)->format('M/Y');
                }
                if (isset($emitidasPorMes[$mesFatura])) {
                    $emitidasPorMes[$mesFatura]++;
                    if ($fatura->status == 24) { // 24 = Pago
                        $pagasPorMes[$mesFatura]++;
                    }
                }
            }
        }
        return [
            'meses' => array_values($meses),
            'emitidas' => array_values($emitidasPorMes),
            'pagas' => array_values($pagasPorMes),
        ];
    }

    public function settings()
    {
        $getSetting = new Setting;

        $result = [
            'logo' => $getSetting->getValue(('logo')),
            'favicon' => $getSetting->getValue(('favicon')),
            'meta_title' => $getSetting->getValue(('meta_title')),
            'meta_keywords' => $getSetting->getValue(('meta_keywords')),
            'meta_description' => $getSetting->getValue(('meta_description')),
            'script_head' => $getSetting->getValue(('script_head')),
            'script_body' => $getSetting->getValue(('script_body')),
            'site_name' => $getSetting->getValue(('site_name')),
            'site_proprietary' => $getSetting->getValue(('site_proprietary')),
            'site_document' => $getSetting->getValue(('site_document')),
            'site_email' => $getSetting->getValue(('site_email')),
            'telephone' => $getSetting->getValue(('telephone')),
            'cellphone' => $getSetting->getValue(('cellphone')),
            'address' => $getSetting->getValue(('address')),
            'hour_open' => $getSetting->getValue(('hour_open')),
            'client_id' => $getSetting->getValue(('client_id')),
            'client_secret' => $getSetting->getValue(('client_secret')),
        ];

        return view('admin.pages.settings', compact('result'));
    }

    public function settingsUpdate(Request $request)
    {
        $settings = $request->all();

        foreach ($settings as $key => $value) {
            // Exception Logo AND Favicon
            if ($key != 'logo' && $key != 'favicon') {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        try {
            Cache::forget('settings');
        } catch (\Exception $e) {
            Log::error('BaseAdminController :: settingsUpdate' . $e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Configurações atualizadas com sucesso', 200);
    }

    public function updateImages(Request $request)
    {
        $pathResponse = '';

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            Setting::updateOrCreate(
                ['key' => 'logo'],
                ['value' => $logoPath]
            );

            $pathResponse = $logoPath;
        }

        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('favicons', 'public');
            Setting::updateOrCreate(
                ['key' => 'favicon'],
                ['value' => $faviconPath]
            );

            $pathResponse = $faviconPath;
        }

        try {
            Cache::forget('settings');
        } catch (\Exception $e) {
            Log::error('BaseAdminController :: settingsUpdate' . $e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(asset('storage/' . $pathResponse), 200);
    }
}
