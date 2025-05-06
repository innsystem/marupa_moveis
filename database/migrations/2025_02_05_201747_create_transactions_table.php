<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->foreignId('integration_id')->nullable()->constrained('integrations')->onDelete('cascade');
            $table->string('type'); // income (Entrada) - expense (Despesa)
            $table->decimal('amount', 10, 2);
            $table->decimal('gateway_fee', 10, 2)->default(0);
            $table->longText('description')->nullable();
            $table->datetime('date');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
