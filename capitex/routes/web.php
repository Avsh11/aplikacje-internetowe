<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WelcomeController;

// Plik tras aplikacji Capitex (warstwa web, nie API REST)
// Kazda Route::... laczy URL -> kontroler@metoda
// middleware auth = trzeba byc zalogowanym (sesja)
// middleware verified = email zweryfikowany (Breeze) - wiekszosc tras po logowaniu
// name('...') = nazwa trasy do route('dashboard') w blade i redirectach
// Na koncie pliku dolaczamy auth.php (login, register, logout)

// Strona startowa (landing) - bez logowania, statystyki z bazy
Route::get('/', [WelcomeController::class, 'index']);

// Stary szablon Breeze - nieuzywany, dashboard jest w DashboardController
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// --- DASHBOARD (user + przekierowanie admina) ---

// GET /dashboard -> po logowaniu panel portfela (user i admin)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- PORTFELE ---

// POST /portfolios - formularz "dodaj portfel" z dashboardu
Route::post('/portfolios', [PortfolioController::class, 'store'])
    ->middleware(['auth'])
    ->name('portfolios.store');

// GET /portfolios/{id} - ten sam widok dashboard ale dane tylko z jednego portfela
Route::get('/portfolios/{id}', [PortfolioController::class, 'show'])
    ->middleware(['auth'])
    ->name('portfolios.show');

// DELETE /portfolios/{portfolio} - {portfolio} = Route Model Binding (id z URL)
Route::delete('/portfolios/{portfolio}', [PortfolioController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('portfolios.destroy');

// --- API pod formularz transakcji (JSON, wywolania fetch z JS) ---

// GET /api/search?q=XTB&category=broker - autocomplete tickerow
Route::get('/api/search', [PortfolioController::class, 'searchAssets'])
    ->middleware(['auth']);

// GET /api/price/{ticker}?date=...&source=... - cena na wybrany dzien
Route::get('/api/price/{ticker}', [PortfolioController::class, 'getAssetPrice'])
    ->middleware(['auth']);

// --- USTAWIENIA KONTA ---

Route::middleware('auth')->group(function () {
    // GET /settings - strona ustawien (waluta, motyw, zakres wykresu)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    // PATCH /settings - zapis formularza (SettingsUpdateRequest)
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Stare linki Breeze /profile -> przekierowanie na /settings (nie uzywamy ProfileController)
    Route::get('/profile', fn () => redirect()->route('settings.index'))->name('profile.edit');
    Route::patch('/profile', [SettingsController::class, 'update'])->name('profile.update');
});

// --- TRANSAKCJE ---

// GET /transactions - historia transakcji (osobna strona)
Route::get('/transactions', [TransactionController::class, 'index'])
    ->middleware(['auth'])
    ->name('transactions.index');

// POST /transactions - zapis buy z modala na dashboardzie
Route::post('/transactions', [TransactionController::class, 'store'])
    ->middleware(['auth'])
    ->name('transactions.store');

// DELETE /transactions/{transaction}
Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('transactions.destroy');

// --- PANEL ADMINISTRATORA ---
// prefix admin -> URL zaczyna sie od /admin/...
// name('admin.') -> nazwy tras: admin.dashboard, admin.users.edit itd.
// ochrona role_id=1 jest w AdminController::ensureAdmin(), nie w osobnym middleware

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
});

// Login, register, logout, reset hasla - trasy Breeze w osobnym pliku
require __DIR__.'/auth.php';
