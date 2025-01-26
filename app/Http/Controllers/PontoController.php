<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PontoController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Autenticação do usuário
            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            $user = Auth::user();
            $now = now()->setTimezone('America/Sao_Paulo');

            // Busca o último registro de ponto do usuário com 'entrada' definida e 'saida' nula
            $lastPonto = Ponto::where('user_id', $user->id)
                ->whereNull('saida')
                ->latest('entrada')
                ->first();

            if ($lastPonto) {
                // Salvar o valor de 'entrada' em uma variável
                $entradaAntiga = $lastPonto->entrada;

                // Deletar o registro de entrada existente
                $lastPonto->delete();

                // Criar um novo registro de ponto com 'entrada' e 'saida'
                $novoPonto = Ponto::create([
                    'user_id' => $user->id,
                    'entrada' => $entradaAntiga,
                    'saida' => $now,
                ]);

                $tempoTrabalhado = $this->calcularTempoTrabalhado($entradaAntiga, $now);
                $message = "Saída registrada com sucesso! Tempo trabalhado: {$tempoTrabalhado}";
                $working = false;
            } else {
                // Registrar entrada
                $ponto = Ponto::create([
                    'user_id' => $user->id,
                    'entrada' => $now
                ]);

                $message = 'Entrada registrada com sucesso às ' . $now->format('H:i:s');
                $working = true;
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data_hora' => $now->format('d/m/Y H:i:s'),
                'working' => $working
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao registrar ponto: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao registrar ponto'
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

    public function status()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            $user = Auth::user();
            $currentPonto = Ponto::where('user_id', $user->id)
                ->whereNull('saida')
                ->latest()
                ->first();

            $recentLogs = Ponto::with('user')
                ->orderBy('entrada', 'desc')
                ->limit(15)
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

            return response()->json([
                'status' => 'success',
                'working' => !is_null($currentPonto),
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
