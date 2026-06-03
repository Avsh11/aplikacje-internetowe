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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();

            // Wlasciciel portfela jaki? no to pytamy tabeli users kluczem obcym
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Nazwa brokera np xtb, binance itd
            $table->string('name'); 
            // broker, exchange, alternative czyli kategoria 
            $table->string('category'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
