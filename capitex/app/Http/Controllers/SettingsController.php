<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsUpdateRequest;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Kontroler ustawien uzytkownika (zamiast domyslnego ProfileController z Breeze)
// Trasy w web.php (middleware auth):
//   GET   /settings -> index   (widok user/settings.blade.php)
//   PATCH /settings -> update
//   GET   /profile  -> redirect na settings.index (kompatybilnosc ze starym linkiem Breeze)
//   PATCH /profile  -> update (ten sam zapis co /settings)
// Walidacja w SettingsUpdateRequest, zapis w SettingsService

class SettingsController extends Controller
{
    protected SettingsService $settingsService;

    // DI - logika zapisu do users (name, email, currency) jest w serwisie
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    // GET /settings (name: settings.index)

    public function index(Request $request): View
    {
        return view('user.settings', [
            // formularz w blade czyta $user->name, $user->currency itd.
            'user' => $request->user(),
        ]);
    }

    // PATCH /settings lub PATCH /profile (name: settings.update / profile.update)
    // SettingsUpdateRequest odpala sie automatycznie przed wejsciem do metody (Form Request)

    public function update(SettingsUpdateRequest $request): RedirectResponse
    {
        // validated() = tylko pola ktore przeszly reguly z SettingsUpdateRequest
        $this->settingsService->updateUserSettings($request->user(), $request->validated());

        // status settings-updated - w blade @if (session('status') === 'settings-updated')
        return back()->with('status', 'settings-updated');
    }
}
