<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Form Request = osobna klasa tylko pod walidacje (Laravel Breeze tez tak robi dla profilu)
// Po co: zamiast duzego $request->validate([...]) w SettingsController trzymamy reguly tutaj
// Kiedy dziala: PATCH /settings -> SettingsController@update(SettingsUpdateRequest $request)
// Laravel PRZED metoda update() odpala rules() - jak blad, wraca do formularza z @error w blade
// Jak OK - w kontrolerze uzywamy $request->validated() (tylko poprawne pola)

class SettingsUpdateRequest extends FormRequest
{
    // authorize() domyslnie true - kazdy zalogowany moze edytowac swoje ustawienia (middleware auth na trasie)

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase', // normalizacja przed zapisem do bazy
                'email',
                'max:255',
                // ten sam email co teraz jest OK; inny user z tym emailem = blad
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            // waluta wyswietlania na dashboardzie (PLN/USD/EUR)
            'currency' => ['required', 'string', Rule::in(['PLN', 'USD', 'EUR'])],
        ];
    }
}
