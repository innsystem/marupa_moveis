<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_list_jobs', function (Blueprint $table) {
            $table->id();
            $table->json('product_data'); // Guarda os dados do produto
            $table->enum('status', ['pendente', 'processando', 'concluído', 'erro'])->default('pendente');
            $table->timestamp('scheduled_at')->nullable(); // Agendamento do processamento
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_list_jobs');
    }
};
