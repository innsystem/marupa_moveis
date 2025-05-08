<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('portfolio_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
        });

        Schema::create('portfolio_category_portfolio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->onDelete('cascade');
            $table->foreignId('portfolio_category_id')->constrained()->onDelete('cascade');
            $table->unique(['portfolio_id', 'portfolio_category_id'], 'portfcat_portf_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('portfolio_category_portfolio');
        Schema::dropIfExists('portfolio_categories');
    }
}; 