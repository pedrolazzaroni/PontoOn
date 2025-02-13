<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::where('responsavel_id', auth()->id())->get();

        // Buscar pontos recentes
        $recentPoints = Ponto::with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Buscar usuários com horas extras
        $overtimeUsers = Ponto::with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id());
            })
            ->whereNotNull('horas_extras')
            ->where('horas_extras', '>', '00:00:00')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Buscar usuários com atrasos
        $lateUsers = Ponto::with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id());
            })
            ->whereNotNull('atraso')
            ->where('atraso', '>', '00:00:00')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Calcular média de horas de trabalho
        $avgWorkingHours = $users->first()->expediente ?? 8;

        return view('admin.dashboard', compact('users', 'recentPoints', 'overtimeUsers', 'lateUsers', 'avgWorkingHours'));
    }

    public function updateWorkingHours(Request $request)
    {
        $request->validate([
            'expediente' => 'required|integer|min:1|max:24',
        ]);

        try {
            User::where('responsavel_id', auth()->id())
                ->update(['expediente' => $request->expediente]);

            return redirect()->route('admin.dashboard')
                        ->with('success', 'Expediente atualizado com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar expediente:', ['error' => $e->getMessage()]);

            return redirect()->route('admin.dashboard')
                        ->with('error', 'Erro ao atualizar expediente.');
        }
    }

    public function lateHours()
    {
        $users = User::where('responsavel_id', auth()->id())
            ->where('status', true)
            ->with(['pontos' => function($query) {
                $query->whereNotNull('atraso')
                      ->where('atraso', '>', '00:00:00')
                      ->orderBy('created_at', 'desc');
            }])
            ->paginate(10);

        $users->getCollection()->transform(function($user) {
            // Calcular atraso total
            $totalLateSeconds = 0;
            foreach ($user->pontos as $ponto) {
                list($hours, $minutes, $seconds) = explode(':', $ponto->atraso);
                $totalLateSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
            }

            $user->total_late_hours = sprintf(
                "%02d:%02d:%02d",
                floor($totalLateSeconds / 3600),
                floor(($totalLateSeconds % 3600) / 60),
                $totalLateSeconds % 60
            );

            return $user;
        });

        return view('admin.hora-atraso', compact('users'));
    }

}
