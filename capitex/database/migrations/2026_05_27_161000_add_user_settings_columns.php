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
        if (!Schema::hasColumn('users', 'theme') || !Schema::hasColumn('users', 'default_chart_range')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'theme')) {
                    $table->string('theme', 10)->default('dark')->after('currency');
                }
                if (!Schema::hasColumn('users', 'default_chart_range')) {
                    $table->string('default_chart_range', 5)->default('ALL')->after('theme');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'theme')) {
                $table->dropColumn('theme');
            }
            if (Schema::hasColumn('users', 'default_chart_range')) {
                $table->dropColumn('default_chart_range');
            }
        });
    }
};
