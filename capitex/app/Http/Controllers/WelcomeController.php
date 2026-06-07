<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Transaction;
use App\Models\User;

class WelcomeController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'usersCount'        => User::count(),
            'portfoliosCount'   => Portfolio::count(),
            'transactionsCount' => Transaction::count(),
        ]);
    }
}
