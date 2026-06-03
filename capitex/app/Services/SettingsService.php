<?php

namespace App\Services;

use App\Models\User;

// Serwis ustawien konta - logika zapisu z formularza /settings
// Wywolywany z: SettingsController@update (po przejsciu SettingsUpdateRequest)
// Kontroler nie zapisuje do bazy sam - tylko przekazuje user + validated() tutaj

class SettingsService
{
    // PATCH /settings - aktualizacja name, email, currency

    public function updateUserSettings(User $user, array $data): User
    {
        // fill() tylko na polach z $data (reszta usera bez zmian)
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'currency' => strtoupper($data['currency']),
        ]);

        // isDirty('email') = email sie zmienil wzgledem tego co bylo w bazie
        // zerujemy weryfikacje (standard Breeze) - user musialby kliknac link z maila
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
}
