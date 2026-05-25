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

        // Schema dla rol uzytkownikow
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();  // admin lub user
            $table->timestamps();
        });

        // Schema dla uzytkownikow (tabela users po prostu)
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')->default(2)->constrained('roles');  // relacja do roli gdzie dwojka to zwykly uzytokwnik
            $table->string('name');
            $table->string('email')->unique();

            $table->string('currency', 3)->default('PLN');  // domyslna waluta do wyswietlania czegos tam

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Laravel default schemas
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        // zeby nie rozpierdalac kluczy obcych usuwamy w odwrotnej kolejnosci !!!

        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
