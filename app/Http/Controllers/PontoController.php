<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PontoController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validar credenciais
            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            $user = Auth::user();
            $now = Carbon::now();

            // Busca o último registro do usuário
            $lastPonto = Ponto::where('user_id', $user->id)
                             ->whereNull('saida')
                             ->latest()
                             ->first();

            if ($lastPonto) {
                // Se existe um registro aberto, registra a saída
                $lastPonto->update(['saida' => $now]);
                $message = 'Saída registrada com sucesso!';
                $working = false;
            } else {
                // Se não existe registro aberto, cria um novo com entrada
                Ponto::create([
                    'user_id' => $user->id,
                    'entrada' => $now,
                ]);
                $message = 'Entrada registrada com sucesso!';
                $working = true;
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data_hora' => $now->format('d/m/Y H:i:s'),
                'working' => $working
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao registrar ponto: ' . $e->getMessage()
            ], 500);
        }
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

            // Verifica se há ponto aberto
            $currentPonto = Ponto::where('user_id', $user->id)
                                ->whereNull('saida')
                                ->latest()
                                ->first();

            // Busca os registros com eager loading do usuário
            $recentLogs = Ponto::with('user')
                              ->orderBy('entrada', 'desc')
                              ->limit(15)
                              ->get();

            $formattedLogs = $recentLogs->map(function($ponto) {
                $entrada = Carbon::parse($ponto->entrada);
                $saida = $ponto->saida ? Carbon::parse($ponto->saida) : null;

                return [
                    'user_name' => $ponto->user->name,
                    'entrada' => $entrada->format('d/m/Y H:i:s'),
                    'saida' => $saida ? $saida->format('d/m/Y H:i:s') : null,
                    'status' => $saida ? 'Saída' : 'Entrada',
                    'tempo_total' => $saida ? $entrada->diff($saida)->format('%H:%I:%S') : 'Em andamento'
                ];
            });

            return response()->json([
                'status' => 'success',
                'working' => !is_null($currentPonto),
                'logs' => $formattedLogs
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar registros'
            ], 500);
        }
    }

    public function consultar(Request $request)
    {
        try {
            // Validar credenciais
            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            $user = Auth::user();

            // Buscar o último registro do usuário
            $lastPonto = Ponto::where('user_id', $user->id)
                             ->latest()
                             ->first();

            if ($lastPonto) {
                $entrada = $lastPonto->entrada->format('d/m/Y H:i:s');
                $saida = $lastPonto->saida ? $lastPonto->saida->format('d/m/Y H:i:s') : '-';
                $tempoTrabalhado = $lastPonto->saida ? Carbon::parse($lastPonto->entrada)->diffForHumans(Carbon::parse($lastPonto->saida), ['parts' => 2]) : 'Em andamento';
            } else {
                $entrada = '-';
                $saida = '-';
                $tempoTrabalhado = '-';
            }

            return response()->json([
                'status' => 'success',
                'entrada' => $entrada,
                'saida' => $saida,
                'tempo_trabalhado' => $tempoTrabalhado
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Erro ao consultar situação: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao consultar situação'
            ], 500);
        }
    }
}
