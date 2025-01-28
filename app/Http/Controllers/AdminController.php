<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Fetch users for the main table
        $users = User::where('responsavel_id', auth()->id())->get();

        // Fetch last 4 point records
        $recentPoints = Ponto::with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id());
            })
            ->latest()
            ->take(4)
            ->get();

        // Fetch last 4 users with overtime
        $overtimeUsers = Ponto::with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id());
            })
            ->selectRaw('user_id, SUM(TIMESTAMPDIFF(HOUR, entrada, saida)) as total_hours')
            ->whereNotNull('saida')
            ->having('total_hours', '>', 8)
            ->groupBy('user_id')
            ->latest()
            ->take(4)
            ->get();

        // Fetch last 4 users with late hours
        $lateUsers = Ponto::with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id());
            })
            ->whereRaw('TIME(entrada) > "09:00:00"')
            ->latest()
            ->take(4)
            ->get();

        return view('admin.dashboard', compact('users', 'recentPoints', 'overtimeUsers', 'lateUsers'));
    }
}
