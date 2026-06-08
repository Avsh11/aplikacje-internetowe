<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// 100 kont testowych (rola user) – rozne nazwy i maile z Faker pl_PL
// Bez portfeli i transakcji – do panelu admina, filtrow i statystyk na stronie startowej
// Haslo wszystkich: password (tylko srodowisko deweloperskie / zdjecia)

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('pl_PL');

        for ($i = 0; $i < 100; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role_id' => 2,
                'currency' => $faker->randomElement(['PLN', 'USD', 'EUR']),
                'email_verified_at' => now(),
            ]);
        }
    }
}
