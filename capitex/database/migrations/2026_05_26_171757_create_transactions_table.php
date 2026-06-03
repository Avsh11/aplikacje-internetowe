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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            // Jakie aktywo
            $table->foreignId('asset_id')->constrained(); 
            // Kupno czy sprzedaz
            // DEMO v1: sprzedaż wyłączona w aplikacji (UI/walidacja); enum z 'sell' zostaje – bez psucia istniejącej bazy / v2
            $table->enum('type', ['buy', 'sell']);
            // $table->enum('type', ['buy']); // tylko przy świeżej instalacji bez legacy sell 
            // Ilosc (16 cyfr, 8 po przecinku dla krypto np.)
            $table->decimal('quantity', 16, 8); 
            // cena jednostkowa zakupu
            $table->decimal('price_per_unit', 16, 4); 
            // Kiedy kupiono
            $table->timestamp('transaction_date'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
