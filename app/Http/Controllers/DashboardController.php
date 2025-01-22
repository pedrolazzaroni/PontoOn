<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Certifique-se de que o método middleware está disponível
        
    }

    public function index()
    {
        return view('dashboard');
    }
}
