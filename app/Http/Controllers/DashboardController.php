<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ponto;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Inicializar variáveis com valores padrão
        $activeUsersCount = 0;
        $todayPointsCount = 0;

        // Se o usuário estiver autenticado, buscar as contagens
        if (Auth::check()) {
            $activeUsersCount = User::where('responsavel_id', Auth::id())
                ->where('status', true)
                ->count();

            $todayPointsCount = Ponto::whereHas('user', function($query) {
                $query->where('responsavel_id', Auth::id());
            })
            ->whereDate('created_at', today())
            ->count();
        }

        return view('dashboard', compact('activeUsersCount', 'todayPointsCount'));
    }
}
