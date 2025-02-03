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
        // Busca usuários ativos vinculados ao responsável logado
        $users = User::where('responsavel_id', auth()->id())
                    ->where('status', true)
                    ->get();

        // Calcula média de horas apenas para usuários ativos
        $avgWorkingHours = $users->avg('expediente') ?? 8;

        $recentPoints = Ponto::with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id())
                      ->where('status', true);
            })
            ->latest()
            ->take(4)
            ->get();

        // Calculate overtime based on user's expediente
        $overtimeUsers = Ponto::with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id())
                      ->where('status', true);
            })
            ->selectRaw('user_id, SUM(TIMESTAMPDIFF(HOUR, entrada, saida)) as total_hours')
            ->whereNotNull('saida')
            ->groupBy('user_id')
            ->get()
            ->map(function($point) {
                $expediente = $point->user->expediente;
                $point->extra_hours = max(0, $point->total_hours - $expediente);
                return $point;
            })
            ->take(4);

        $lateUsers = Ponto::with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id())
                      ->where('status', true);
            })
            ->whereRaw('TIME(entrada) > "09:00:00"')
            ->latest()
            ->take(4)
            ->get();

        // Debug para verificar os usuários
        \Log::info('Usuários carregados:', ['count' => $users->count(), 'users' => $users->toArray()]);

        return view('admin.dashboard', compact('users', 'recentPoints', 'overtimeUsers', 'lateUsers', 'avgWorkingHours'));
    }

    // Add new method for updating working hours
    public function updateWorkingHours(Request $request)
    {
        $request->validate([
            'expediente' => 'required|integer|min:1|max:24',
            'users' => 'required|array',
            'users.*' => 'exists:users,id'
        ]);

        try {
            // Garante que apenas usuários vinculados ao responsável sejam atualizados
            User::where('responsavel_id', auth()->id())
                ->whereIn('id', $request->users)
                ->update(['expediente' => $request->expediente]);

            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Horário de expediente atualizado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar expediente:', ['error' => $e->getMessage()]);

            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Erro ao atualizar horário de expediente');
        }
    }

}
