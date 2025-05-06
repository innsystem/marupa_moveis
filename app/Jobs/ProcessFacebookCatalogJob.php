<?php

namespace App\Jobs;

use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessFacebookCatalogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $batchSize;
    protected $offset;
    protected $totalToProcess;

    /**
     * Cria uma nova instância do job.
     *
     * @param int $batchSize Tamanho do lote a ser processado
     * @param int $offset Offset para paginação
     * @param int|null $totalToProcess Número total de produtos a serem processados
     */
    public function __construct($batchSize = 10, $offset = 0, $totalToProcess = null)
    {
        $this->batchSize = $batchSize;
        $this->offset = $offset;
        $this->totalToProcess = $totalToProcess;
    }

    /**
     * Executa o job.
     */
    public function handle()
    {
        $productService = new ProductService();
        
        // Processa o lote atual
        $result = $productService->syncFacebookCatalog($this->batchSize, $this->offset);
        
        Log::info("Processamento de catálogo Facebook: Lote {$this->offset} a " . ($this->offset + $this->batchSize) . 
            " | Processados: {$result['processed']} | Sucesso: {$result['success']} | Falhas: {$result['failed']}");
        
        // Se ainda existem produtos a serem processados, dispara o próximo job
        if ($result['remaining'] > 0) {
            // Adiciona um delay de 5 segundos entre os lotes para evitar sobrecarga na API do Facebook
            ProcessFacebookCatalogJob::dispatch(
                $this->batchSize, 
                $result['offset_next'],
                $result['total']
            )->delay(now()->addSeconds(5));
            
            Log::info("Próximo lote agendado: Offset {$result['offset_next']} | Restantes: {$result['remaining']}");
        } else {
            Log::info("Processamento de catálogo Facebook concluído. Total processado: " . ($result['total'] - $result['remaining']));
        }
    }
} 