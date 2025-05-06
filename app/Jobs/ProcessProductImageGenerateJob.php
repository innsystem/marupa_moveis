<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductImageGenerate;
use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessProductImageGenerateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 60;

    public function handle()
    {
        // Busca um produto aleatório que ainda não teve imagem gerada
        $product = Product::whereDoesntHave('imageGenerates')->inRandomOrder()->first();

        if (!$product) {
            Log::info('Nenhum produto disponível para geração de imagem.');
            return;
        }

        try {
            $service = new ProductService();
            $service->generateProductStory($product->id);
            $service->publishProductGroup($product->id);
            // Registra na tabela para não repetir
            ProductImageGenerate::create(['product_id' => $product->id]);
            Log::info('Imagem gerada e produto publicado no grupo para o produto ID: ' . $product->id);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar imagem/publicar produto: ' . $e->getMessage());
        }
    }
} 