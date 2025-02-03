<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PontoController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validar os dados recebidos
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Buscar usuário e verificar credenciais
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuário não encontrado'
                ], 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Senha incorreta'
                ], 401);
            }

            // Verificar status do usuário
            if (!$user->status) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuário não está autorizado a registrar ponto'
                ], 403);
            }

            $now = now()->setTimezone('America/Sao_Paulo');

            // Buscar último registro
            $lastPonto = Ponto::where('user_id', $user->id)
                ->whereNull('saida')
                ->latest('entrada')
                ->first();

            DB::beginTransaction();
            try {
                if ($lastPonto) {
                    $lastPonto->saida = $now;
                    // Pass user's expediente to the overtime calculation
                    $horaExtra = $this->calcularHoraExtra($lastPonto->entrada, $now, $user->expediente);
                    $lastPonto->horas_extras = $horaExtra;
                    $lastPonto->save();

                    $tempoTrabalhado = $this->calcularTempoTrabalhado($lastPonto->entrada, $now);
                    $message = "Saída registrada com sucesso! Tempo trabalhado: {$tempoTrabalhado}. Horas Extras: {$horaExtra}";
                    $working = false;
                } else {
                    Ponto::create([
                        'user_id' => $user->id,
                        'entrada' => $now
                    ]);

                    $message = 'Entrada registrada com sucesso às ' . $now->format('H:i:s');
                    $working = true;
                }
                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'data_hora' => $now->format('d/m/Y H:i:s'),
                    'working' => $working
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Erro ao registrar ponto: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao registrar ponto: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calcularTempoTrabalhado($entrada, $saida)
    {
        $diffInSeconds = Carbon::parse($entrada)->diffInSeconds($saida);
        $hours = floor($diffInSeconds / 3600);
        $minutes = floor(($diffInSeconds % 3600) / 60);
        $seconds = $diffInSeconds % 60;
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }

    // Update to use the user's expediente value
    private function calcularHoraExtra($entrada, $saida, $expediente)
    {
        $diffInSeconds = Carbon::parse($entrada)->diffInSeconds($saida);
        $totalHours = $diffInSeconds / 3600;

        $extraHours = $totalHours > $expediente ? $totalHours - $expediente : 0;

        return round($extraHours, 2);
    }

    public function status()
    {
        try {
            Log::info('Iniciando busca de status');
            $recentLogs = Ponto::with('user')
                ->orderBy('entrada', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($ponto) {
                    $entrada = Carbon::parse($ponto->entrada)->setTimezone('America/Sao_Paulo');
                    $saida = $ponto->saida ? Carbon::parse($ponto->saida)->setTimezone('America/Sao_Paulo') : null;

                    return [
                        'user_name' => $ponto->user->name,
                        'entrada' => $entrada->format('d/m/Y H:i:s'),
                        'saida' => $saida ? $saida->format('d/m/Y H:i:s') : '-',
                        'status' => $saida ? 'Saída' : 'Entrada',
                        'tempo_total' => $saida ? $this->calcularTempoTrabalhado($entrada, $saida) : 'Em andamento'
                    ];
                });

            Log::info('Busca de status concluída com sucesso');

            return response()->json([
                'status' => 'success',
                'logs' => $recentLogs
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar registros'
            ], 500);
        }
    }
}
