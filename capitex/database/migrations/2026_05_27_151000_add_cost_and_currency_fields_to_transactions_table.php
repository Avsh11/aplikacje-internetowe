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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->after('price_per_unit');
            $table->decimal('exchange_rate_pln', 12, 6)->default(1)->after('currency');
            $table->decimal('total_cost_pln', 18, 4)->default(0)->after('exchange_rate_pln');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate_pln', 'total_cost_pln']);
        });
    }
};
