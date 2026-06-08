<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// 100 kont testowych (rola user) – imiona / nazwiska / nicki (bez tytulow naukowych)
// Bez portfeli i transakcji – do panelu admina, filtrow i statystyk na stronie startowej
// Haslo wszystkich: password (tylko srodowisko deweloperskie / zdjecia)

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('pl_PL');

        for ($i = 0; $i < 100; $i++) {
            User::create([
                'name' => $this->randomDisplayName($faker),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role_id' => 2,
                'currency' => $faker->randomElement(['PLN', 'USD', 'EUR']),
                'email_verified_at' => now(),
            ]);
        }
    }

    private function randomDisplayName(Generator $faker): string
    {
        $first = $faker->firstName();

        return match ($faker->numberBetween(1, 10)) {
            // samo imie
            1, 2 => $first,
            // nickname: Janek42, ania88
            3, 4 => strtolower($first) . $faker->numberBetween(1, 99),
            // imie + inicjal nazwiska: Anna K.
            5 => $first . ' ' . mb_substr($faker->lastName(), 0, 1) . '.',
            // klasycznie: imie i nazwisko (bez tytulow – firstName/lastName zamiast name())
            default => $first . ' ' . $faker->lastName(),
        };
    }
}
