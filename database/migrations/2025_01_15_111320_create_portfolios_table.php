<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->index('slug');
            $table->longText('description')->nullable();
            $table->foreignId('status')->constrained('statuses')->cascadeOnDelete();
            $table->string('location')->nullable();
            $table->string('type')->nullable();
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('portfolios');
    }
};
