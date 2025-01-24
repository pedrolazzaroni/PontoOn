<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    // Remove constructor since middleware is handled in routes

    public function index()
    {
        return view('dashboard');
    }
}
