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

            DB::beginTransaction();
            try {
                // Buscar registro do dia atual
                $registroDia = Ponto::where('user_id', $user->id)
                    ->whereDate('entrada', $now->format('Y-m-d'))
                    ->first();

                if ($registroDia) {
                    // Já existe registro hoje
                    if (!$registroDia->entrada_almoco) {
                        // Registrar entrada almoço
                        $registroDia->entrada_almoco = $now;
                        $registroDia->save();
                        $message = 'Entrada de almoço registrada às ' . $now->format('H:i:s');
                        $working = false;
                    }
                    elseif (!$registroDia->saida_almoco) {
                        // Registrar saída almoço
                        $registroDia->saida_almoco = $now;
                        $registroDia->save();
                        $message = 'Saída de almoço registrada às ' . $now->format('H:i:s');
                        $working = true;
                    }
                    elseif (!$registroDia->saida) {
                        // Registrar saída final
                        $registroDia->saida = $now;

                        // Calcula tempo total trabalhado
                        $segundosTrabalhados = $this->calcularTempoTotalComAlmoco($registroDia);
                        $registroDia->horas_trabalhadas = $this->formatarTempo($segundosTrabalhados);

                        // Calcula horas extras ou atraso
                        $expedienteEmSegundos = $user->expediente * 3600;
                        if ($segundosTrabalhados > $expedienteEmSegundos) {
                            $segundosExtras = $segundosTrabalhados - $expedienteEmSegundos;
                            $registroDia->horas_extras = $this->formatarTempo($segundosExtras);
                            $registroDia->atraso = "00:00:00";
                            $mensagemExtra = " (+" . $registroDia->horas_extras . " extras)";
                        } else {
                            $segundosAtraso = $expedienteEmSegundos - $segundosTrabalhados;
                            $registroDia->horas_extras = "00:00:00";
                            $registroDia->atraso = $this->formatarTempo($segundosAtraso);
                            $mensagemExtra = " (-" . $registroDia->atraso . " atraso)";
                        }

                        $registroDia->save();
                        $message = "Saída registrada com sucesso! Tempo total: " .
                                 $registroDia->horas_trabalhadas . $mensagemExtra;
                        $working = false;
                    }
                } else {
                    // Primeiro registro do dia
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

    private function calcularTempoTotalComAlmoco($ponto)
    {
        $segundosTotais = 0;

        // Período da manhã (entrada até almoço)
        if ($ponto->entrada && $ponto->entrada_almoco) {
            $segundosTotais += Carbon::parse($ponto->entrada)
                ->diffInSeconds(Carbon::parse($ponto->entrada_almoco));
        }

        // Período da tarde (volta do almoço até saída)
        if ($ponto->saida_almoco && $ponto->saida) {
            $segundosTotais += Carbon::parse($ponto->saida_almoco)
                ->diffInSeconds(Carbon::parse($ponto->saida));
        }

        return $segundosTotais;
    }

    public function status()
    {
        try {
            $recentLogs = Ponto::with('user')
                ->orderBy('entrada', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($ponto) {
                    $status = $this->determinarStatus($ponto);

                    return [
                        'user_name' => $ponto->user->name,
                        'entrada' => Carbon::parse($ponto->entrada)->format('d/m/Y H:i:s'),
                        'entrada_almoco' => $ponto->entrada_almoco ? Carbon::parse($ponto->entrada_almoco)->format('H:i:s') : '-',
                        'saida_almoco' => $ponto->saida_almoco ? Carbon::parse($ponto->saida_almoco)->format('H:i:s') : '-',
                        'saida' => $ponto->saida ? Carbon::parse($ponto->saida)->format('H:i:s') : '-',
                        'status' => $status,
                        'tempo_total' => $this->calcularTempoTotalComAlmoco($ponto) .
                            (!$ponto->saida ? ' (Em andamento)' : '')
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

    private function determinarStatus($ponto)
    {
        if (!$ponto->entrada_almoco) return 'Trabalhando';
        if (!$ponto->saida_almoco) return 'Almoço';
        if (!$ponto->saida) return 'Trabalhando';
        return 'Finalizado';
    }
}
