<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\ProductsController;
use App\Models\ProductListJob;
use App\Http\Controllers\Admin\IntegrationsPlaygroundController;
use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessProductQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número máximo de tentativas antes de falhar.
     */
    public int $tries = 3;

    /**
     * Tempo máximo antes de falhar.
     */
    public int $timeout = 60;

    /**
     * Executa o job.
     */
    public function handle()
    {
        // Buscar o primeiro produto pendente agendado para o momento atual ou anterior
        $productJob = ProductListJob::where('status', 'pendente')
            // ->where('scheduled_at', '<=', now())
            // ->orderBy('scheduled_at', 'DESC')
            ->inRandomOrder()
            ->first();

        if (!$productJob) {
            // Log::info('Nenhum produto na fila para processamento.');
            return;
        }

        // Atualiza status para "processando"
        $productJob->update(['status' => 'processando']);

        try {
            // Decodifica os dados armazenados em JSON
            $data = json_decode($productJob->product_data, true);

            $productService = new ProductService;

            // Chama o método de processamento do produto no Controller
            $productService->processProductNow($data);

            // Atualiza o status para "concluído"
            $productJob->update(['status' => 'concluído']);
        } catch (\Exception $e) {
            // Em caso de erro, atualiza o status e registra no log
            Log::error('Erro ao processar produto: ' . $e->getMessage());
            $productJob->update(['status' => 'erro']);
        }
    }
}
