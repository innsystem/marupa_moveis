<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFacebookCatalogJob;
use Illuminate\Console\Command;

class SyncFacebookCatalogCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-catalog {--batch=10 : Tamanho do lote a ser processado}';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Sincroniza todos os produtos ativos com o catálogo do Facebook';

    /**
     * Execute o comando do console.
     */
    public function handle()
    {
        $batchSize = (int) $this->option('batch');
        
        if ($batchSize <= 0) {
            $this->error('O tamanho do lote deve ser maior que zero.');
            return 1;
        }
        
        $this->info('Iniciando sincronização do catálogo Facebook...');
        
        // Inicia o job com o primeiro lote (offset 0)
        ProcessFacebookCatalogJob::dispatch($batchSize, 0);
        
        $this->info('Job de sincronização adicionado à fila!');
        $this->info("Os produtos serão processados em lotes de {$batchSize}.");
        $this->info('Você pode monitorar o progresso nos logs.');
        
        return 0;
    }
} 