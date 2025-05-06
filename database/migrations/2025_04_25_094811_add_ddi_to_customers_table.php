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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ddi', 10)->default('+55')->after('document');
            
            // Ajustar a coluna phone para não conter mais o DDI
            // Isso ajudará na transição, mas depende de como seus dados estão estruturados
            // Você pode precisar de uma migração de dados separada para mover o DDI
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ddi');
        });
    }
};
