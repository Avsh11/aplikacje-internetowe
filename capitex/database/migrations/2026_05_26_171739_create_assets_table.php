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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            // np. BTC, AAPL itd. I wiadomo unikatowy jest ticker
            $table->string('ticker')->unique(); 
            // np. Bitcoin, Apple
            $table->string('name'); 
            // crypto, stock, etf
            $table->string('type'); 
            // USD, PLN, EUR
            $table->string('currency', 3); 
            // binance, yahoo
            $table->string('price_source'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
