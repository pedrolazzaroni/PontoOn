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

        // Updated overtime query
        $overtimeUsers = Ponto::with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id())
                      ->where('status', true);
            })
            ->whereNotNull('horas_extras')
            ->where('horas_extras', '>', '00:00:00')
            ->latest()
            ->take(4)
            ->get();

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
            Log::error('Erro ao atualizar expediente:', ['error' => $e->getMessage()]);

            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Erro ao atualizar horário de expediente');
        }
    }

    public function lateHours()
    {
        $users = User::where('responsavel_id', auth()->id())
            ->where('status', true)
            ->with('pontos')
            ->paginate(10);

        // Calculate total late time for each user
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
