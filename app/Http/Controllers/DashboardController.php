<?php

namespace App\Http\Controllers;

// ...existing use statements...

class DashboardController extends Controller
{
    // Remove auth middleware since dashboard is public

    public function index()
    {
        return view('dashboard');
    }
}
