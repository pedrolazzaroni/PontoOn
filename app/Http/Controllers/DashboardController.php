<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ponto;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Contagem de usuários ativos
        $activeUsersCount = User::where('responsavel_id', Auth::id())
            ->where('status', true)
            ->count();

        // Contagem de pontos registrados hoje
        $todayPointsCount = Ponto::whereHas('user', function($query) {
            $query->where('responsavel_id', Auth::id());
        })
        ->whereDate('created_at', today())
        ->count();

        return view('dashboard', compact('activeUsersCount', 'todayPointsCount'));
    }
}
