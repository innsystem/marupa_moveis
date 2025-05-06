<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_group_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('password_code')->nullable();
            $table->string('document')->nullable();
            $table->string('phone')->nullable();
            $table->rememberToken();
            $table->timestamps();
        
            $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
