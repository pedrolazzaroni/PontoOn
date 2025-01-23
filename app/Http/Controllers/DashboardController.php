<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Certifique-se de que o método middleware está disponível

    }

    public function index()
    {
        // Verifica se o usuário está logado
        if (!Auth::user()) {
            return redirect()->route('login');
        }


        return view('dashboard');
    }
}
