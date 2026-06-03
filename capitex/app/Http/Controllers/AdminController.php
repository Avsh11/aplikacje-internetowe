<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

// Kontroler admina aplikacji
// Odpowiada za trasy z prefiksem /admin gdzie te juz sa w web.php
// Kazda metoda ktora jest publiczna to jedna akcja HTTP np GET, DELETE itd.
// Dane z formularzy walidujemy w kontrolerze juz bez osobnego pliku request ($request->validate)

class AdminController extends Controller
{
    // Sprawdzamy czy zalogowany user ma role admina
    // role_id = 1 to admin a 2 to zwykly user
    // Na poczatku kazdej akcji wywolujemy metode bo w laravelu 13 bazowy kontoler nie udostepnia juz
    // $this->middleware w konstruktorze jak w wesjach starszych
    // Jak ktos nie ma uprawnien a bedzie chcial wejsc na np dashboard admina to mu wywali HTTP 403

    private function ensureAdmin(): void
    {
        // Auth::user() to model User z sesji jest czyli ciastko larabel_session
        if ((int) Auth::user()->role_id !== 1) {
            abort(403, 'Brak uprawnien administratora.');
        }
    }

    // GET /admin/dashboard
    // w widoku sa  statystyki + tabela wszystkich kont jakie sa w apce

    public function index()
    {
        $this->ensureAdmin();

        // przekazujemy dane do blade admin/dashboard.blade.php jako zmienne $usersCount, $users itd.
        return view('admin.dashboard', [
            // Liczymy tylko konta z rola usera czyli role 2
            'usersCount' => User::where('role_id', 2)->count(),

            // Portfolio::count() wszystkie portfele w aplikacji 
            'portfoliosCount' => Portfolio::count(),

            // Wszystkie transakcje buy w bazie lacznie wszystkich userow jakie sa 
            'transactionsCount' => Transaction::count(),

            // Lista userow
            // orderByDesc('id') czyli najnowsze konta leca kurwqana gorze tabeli.
            'users' => User::with('role')->orderByDesc('id')->get(),
        ]);
    }

    /**
     * GET /admin/users/{user}/edit
     * Formularz edycji wybranego uzytkownika.
     *
     * Parametr User $user to Route Model Binding: Laravel sam szuka usera po ID z URL.
     * Jesli nie ma takiego ID -> 404.
     */
    public function edit(User $user)
    {
        $this->ensureAdmin();

        return view('admin.edit', [
            // W widoku uzywamy $user (np. $user->name, $user->email).
            'user' => $user,
        ]);
    }

    // co robi metoda? 
    // zapis zmian formualrza edycji
    // request $request to dane post/patch z pol formualrza name email role currency itd
    // user $user to konto ktore edytujemy z url

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();

        // reguly walidacji jak nie przejda to laravel wraca do formularza z bledami 
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                // email ma byc unikalny w tabeli users ale ignorujemy biezacego usera (edycja tego samego maila okej)
                Rule::unique('users')->ignore($user->id),
            ],
            'currency' => ['required', 'in:PLN,USD,EUR'],
        ];

        // edytujac swoje konto admin nie moze zmienic roli na user zeby sie nie zlockowac
        // dla innych kont moze juz
        if ($user->id !== Auth::id()) {
            $rules['role_id'] = ['required', 'in:1,2'];
        }

        $data = $request->validate($rules);

        // nawet jesli ktos recznie wyslal role_id=2 przy edycji siebie to wymuszamy admina
        if ($user->id === Auth::id()) {
            $data['role_id'] = 1;
        }

        // update() zapisuje tylko pola z $data do bazy (mass assignment - fillable w modelu User jest)
        $user->update($data);

        // redirect + with('status') = komunikat sukcesu w sesji (pokazuje sie na dashboardzie).
        return redirect()->route('admin.dashboard')->with('status', 'Dane uzytkownika zapisane.');
    }

    // DELETE /admin/users/{user}
    // usuwamy konta usera z bazy w pizdu
    // w migracjach portfele maja onDelete kaskadowo przy user_id 
    // wiec usuniecie usera usuwa portfela i transakcje

    public function destroy(User $user)
    {
        $this->ensureAdmin();

        // Admin nie moze usunac samego siebie 
        if ($user->id === Auth::id()) {
            return back()->withErrors(['delete' => 'Nie mozesz usunac wlasnego konta.']);
        }

        // Nie wolno usunac ostatniego konta z role_id = 1 bo zostalby system bez admina
        if ((int) $user->role_id === 1 && User::where('role_id', 1)->count() <= 1) {
            return back()->withErrors(['delete' => 'Nie mozna usunac ostatniego administratora.']);
        }

        $user->delete();

        return redirect()->route('admin.dashboard')->with('status', 'Uzytkownik usuniety.');
    }
}
