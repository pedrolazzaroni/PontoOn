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
            ->with('pontos')
            ->paginate(10);

        $users->getCollection()->transform(function($user) {
            $lateSeconds = 0;
            foreach ($user->pontos as $ponto) {
                $scheduledStart = Carbon::parse($ponto->created_at->format('Y-m-d') . ' 09:00:00');
                if ($ponto->entrada && Carbon::parse($ponto->entrada)->greaterThan($scheduledStart)) {
                    $lateSeconds += $scheduledStart->diffInSeconds(Carbon::parse($ponto->entrada));
                }
            }
            $user->late_hours = gmdate('H:i:s', $lateSeconds);
            return $user;
        });

        return view('admin.hora-atraso', compact('users'));
    }

}
