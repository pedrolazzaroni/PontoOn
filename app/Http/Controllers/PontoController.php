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

            $now = now();

            // Buscar último registro
            $lastPonto = Ponto::where('user_id', $user->id)
                ->whereNull('saida')
                ->latest('entrada')
                ->first();

            DB::beginTransaction();
            try {
                if ($lastPonto) {
                    $lastPonto->saida = $now;

                    // Calcula tempo do ponto atual
                    $entrada = Carbon::parse($lastPonto->entrada);
                    $segundosTrabalhados = $entrada->diffInSeconds($now);

                    // Calcula tempo total trabalhado no dia
                    $segundosTotaisNoDia = $this->calcularTempoTotalDia($user->id, $now->format('Y-m-d'));
                    $segundosTotaisNoDia += $segundosTrabalhados;

                    // Converte expediente de horas para segundos
                    $expedienteEmSegundos = $user->expediente * 3600;

                    // Formata o tempo trabalhado neste ponto específico
                    $lastPonto->horas_trabalhadas = $this->formatarTempo($segundosTrabalhados);

                    // Calcula horas extras ou atraso com base no total do dia
                    if ($segundosTotaisNoDia >= $expedienteEmSegundos) {
                        $segundosExtras = $segundosTotaisNoDia - $expedienteEmSegundos;
                        $lastPonto->horas_extras = $this->formatarTempo($segundosExtras);
                        $lastPonto->atraso = "00:00:00";
                        $mensagemAdicional = " (+" . $lastPonto->horas_extras . " extras)";
                    } else {
                        $segundosAtraso = $expedienteEmSegundos - $segundosTotaisNoDia;
                        $lastPonto->horas_extras = "00:00:00";
                        $lastPonto->atraso = $this->formatarTempo($segundosAtraso);
                        $mensagemAdicional = " (-" . $lastPonto->atraso . " atraso)";
                    }

                    $lastPonto->save();

                    $message = "Saída registrada com sucesso! Tempo trabalhado hoje: " .
                              $this->formatarTempo($segundosTotaisNoDia) .
                              $mensagemAdicional;
                    $working = false;
                } else {
                    Ponto::create([
                        'user_id' => $user->id,
                        'entrada' => $now,
                        'horas_extras' => "00:00:00",
                        'atraso' => "00:00:00",
                        'horas_trabalhadas' => "00:00:00"
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
        $entrada = Carbon::parse($entrada);
        $saida = Carbon::parse($saida);
        $diffInSeconds = $entrada->diffInSeconds($saida);
        $hours = floor($diffInSeconds / 3600);
        $minutes = floor(($diffInSeconds % 3600) / 60);
        $seconds = $diffInSeconds % 60;
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }

    private function calcularHorasTrabalhadasNoDia($user_id, $data)
    {
        $pontos = Ponto::where('user_id', $user_id)
            ->whereDate('entrada', $data)
            ->whereNotNull('saida')
            ->get();

        $segundosTotais = 0;
        foreach ($pontos as $ponto) {
            $entrada = Carbon::parse($ponto->entrada);
            $saida = Carbon::parse($ponto->saida);
            $segundosTotais += $entrada->diffInSeconds($saida);
        }

        return $segundosTotais;
    }

    private function formatarTempo($segundos)
    {
        return sprintf(
            "%02d:%02d:%02d",
            floor($segundos / 3600),
            floor(($segundos % 3600) / 60),
            $segundos % 60
        );
    }

    private function calcularTempoTotalDia($user_id, $data)
    {
        return Ponto::where('user_id', $user_id)
            ->whereDate('entrada', $data)
            ->whereNotNull('saida')
            ->get()
            ->sum(function ($ponto) {
                $entrada = Carbon::parse($ponto->entrada);
                $saida = Carbon::parse($ponto->saida);
                return $entrada->diffInSeconds($saida);
            });
    }

    public function status()
    {
        try {
            $recentLogs = Ponto::with('user')
                ->orderBy('entrada', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($ponto) {
                    $entrada = Carbon::parse($ponto->entrada);
                    $saida = $ponto->saida ? Carbon::parse($ponto->saida) : null;

                    return [
                        'user_name' => $ponto->user->name,
                        'entrada' => $entrada->format('d/m/Y H:i:s'),
                        'saida' => $saida ? $saida->format('d/m/Y H:i:s') : '-',
                        'status' => $saida ? 'Saída' : 'Entrada',
                        'tempo_total' => $this->calcularTempoTrabalhado($entrada, $saida ?: now()) .
                            ($saida ? '' : ' (Em andamento)')
                    ];
                });

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
