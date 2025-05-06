<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Jobs\ProcessWebhookJob;
use App\Jobs\QueueJob;
use App\Jobs\ProcessProductQueueJob;
use App\Jobs\ProcessProductImageGenerateJob;
use App\Jobs\GenerateDailyInvoiceJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Geração de faturas diárias às 11h da manhã
        // $schedule->job(new GenerateDailyInvoiceJob())->everyMinute();
        $schedule->job(new GenerateDailyInvoiceJob())->dailyAt('11:00');

        // Processamento dos WebHooks Pendentes (table: webhooks where status = 23)
        $schedule->job(new ProcessWebhookJob())->everyMinute();

        $schedule->job(new ProcessProductQueueJob())
            ->everyThreeHours()
            // ->everyFifteenMinutes()
            ->between('08:00', '22:00');

        $schedule->job(new ProcessProductImageGenerateJob())
            ->everyFourHours()
            // ->everyMinute()
            ->between('08:00', '21:00');
        
        // Inicia Fila de Envios em Segundo-Plano
        $schedule->job(new QueueJob())->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
