<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

// UWAGA: ten kontroler pochodzi z Laravel Breeze (szablon auth)
// W Capitex NIE jest podlaczony w routes/web.php - nie uzywamy go w aplikacji
// Zamiast tego:
//   GET  /settings  -> SettingsController@index
//   PATCH /settings i /profile -> SettingsController@update (profile to alias w web.php)
// Plik zostawiamy jako referencje Breeze; na obronie mowimy ze ustawienia sa w SettingsController

class ProfileController extends Controller
{
    // GET profil (w Breeze: /profile) - u nas nieaktywne
    // Gdyby trasa istniala, pokazalby widok profile/edit z danymi zalogowanego usera

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            // $request->user() to to samo co Auth::user() - model User z sesji
            'user' => $request->user(),
        ]);
    }

    // PATCH profil - aktualizacja name/email (Breeze)
    // ProfileUpdateRequest trzyma reguly walidacji w osobnym pliku (Form Request)

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // fill() uzupelnia tylko pola z validated() ktore sa w $fillable modelu User
        $request->user()->fill($request->validated());

        // Zmiana emaila = trzeba ponownie zweryfikowac (Breeze standard)
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // DELETE konto - usuwa usera po podaniu aktualnego hasla
    // Tez nieuzywane w Capitex (nie ma trasy), ale pokazuje typowy flow Breeze

    public function destroy(Request $request): RedirectResponse
    {
        // validateWithBag - bledy trafiaja do osobnej "torby" userDeletion w blade
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        // uniewazniamy sesje i token CSRF po usunieciu konta
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
