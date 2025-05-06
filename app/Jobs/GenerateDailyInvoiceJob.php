<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Integration;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GenerateDailyInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Log::info('Iniciando job de geração de faturas diárias');

        $dateToday = Carbon::now();

        // Instancia o serviço via service container
        $invoiceService = app(\App\Services\InvoiceService::class);

        // Busca todos os usuários que possuem serviços ativos e recorrentes que vencem hoje
        $userIds = UserService::where('status', 3)
            ->where('period', '!=', 'once')
            ->whereDate('end_date', $dateToday->format('Y-m-d'))
            ->pluck('user_id')
            ->unique();

        foreach ($userIds as $userId) {
            try {
                // Passar notifyUser como true para que sempre notifique via job
                $invoiceService->generateRecurringInvoiceForUserServices($userId, ['notifyUser' => true]);
                Log::info("Fatura gerada com sucesso para o usuário ID {$userId}");
            } catch (\Exception $e) {
                Log::error("Erro ao gerar fatura para o usuário ID {$userId}: " . $e->getMessage());
            }
        }
    }
}
