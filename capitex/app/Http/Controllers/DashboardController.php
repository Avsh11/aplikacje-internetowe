<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    // Glowna metoda obslugujaca adres /dashboard
    public function index()
    {
        // Sprawdzamy role_id aktualnie zalogowanego usera
        // I typ int zeby nie bylo kurwa sytuacji ze bedzie "1" i sie rozpierdoli wszystko
        $roleId = (int) Auth::user()->role_id;

        // Jesli role_id = 1 to wtedy admin, wyswietlamy widok admina
        // scisle porownanie operator ===
        if ($roleId === 1) {
            return view('admin.dashboard');
        }

        // Jeśli role_id = 2 to wtedy user i wyswietlamy widok usera
        if ($roleId === 2) {
            return view('user.dashboard');
        }

        // Zabezpieczenie jakby cos sie z rolami zepsulo
        abort(403, 'Brak uprawnien');
    }
}