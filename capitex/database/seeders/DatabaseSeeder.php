<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tworzymy role słownikowe
        Role::create(['id' => 1, 'name' => 'admin']);
        Role::create(['id' => 2, 'name' => 'user']);

        // Domyslny admin (logowanie z maila admin@capitex.pl, haslo: admin)
        User::create([
            'name' => 'Glowny Administrator',
            'email' => 'admin@capitex.pl',
            'password' => Hash::make('admin'),  // haslo zahashowane
            'role_id' => 1,
            'currency' => 'PLN',
        ]);

        $this->call ([
            AssetSeeder::class, 
        ]);
    }
}