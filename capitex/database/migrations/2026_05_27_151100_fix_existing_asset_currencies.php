<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('assets')
            ->where('ticker', 'like', '%.WA')
            ->update(['currency' => 'PLN']);

        DB::table('assets')
            ->where('ticker', 'like', '%.DE')
            ->update(['currency' => 'EUR']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intencjonalnie bez rollbacku - nie odtwarzamy błędnych walut.
    }
};
