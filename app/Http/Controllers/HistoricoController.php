<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonPeriod;

class HistoricoController extends Controller
{
    public function index(){
        return view('historico.index');
    }

    public function getData(Request $request){
        $query = Ponto::with('user')
            ->orderBy('entrada', 'desc');

        // Filtrar por data
        if ($request->data_inicio && $request->data_fim) {
            $query->whereBetween('entrada', [
                Carbon::parse($request->data_inicio)->startOfDay(),
                Carbon::parse($request->data_fim)->endOfDay()
            ]);
        }

        $registros = $query->get()->map(function($ponto) {
            $tempoTrabalhado = null;
            if ($ponto->saida) {
                $tempoTrabalhado = Carbon::parse($ponto->entrada)
                    ->diffForHumans(Carbon::parse($ponto->saida), ['parts' => 2]);
            }

            return [
                'user_name' => $ponto->user->name,
                'data' => Carbon::parse($ponto->entrada)->format('d/m/Y'),
                'entrada' => Carbon::parse($ponto->entrada)->format('H:i:s'),
                'saida' => $ponto->saida ? Carbon::parse($ponto->saida)->format('H:i:s') : '-',
                'tempo_trabalhado' => $tempoTrabalhado ?? 'Em andamento',
            ];
        });

        return response()->json(['data' => $registros]);
    }

    /**
     * Get weekly summary of worked hours and extra hours.
     */
    public function weeklySummary(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            $user = Auth::user();
            $currentDate = Carbon::now()->setTimezone('America/Sao_Paulo');
            $startOfWeek = $currentDate->copy()->startOfWeek();
            $endOfWeek = $currentDate->copy()->endOfWeek();

            $pontos = Ponto::where('user_id', $user->id)
                ->whereBetween('entrada', [$startOfWeek, $endOfWeek])
                ->whereNotNull('saida')
                ->get();

            $totalSeconds = 0;

            foreach ($pontos as $ponto) {
                $entrada = Carbon::parse($ponto->entrada);
                $saida = Carbon::parse($ponto->saida);
                $totalSeconds += $entrada->diffInSeconds($saida);
            }

            $totalHours = floor($totalSeconds / 3600);
            $totalMinutes = floor(($totalSeconds % 3600) / 60);
            $totalTime = sprintf("%02d:%02d", $totalHours, $totalMinutes);

            // Define weekly threshold (e.g., 40 hours)
            $weeklyThreshold = 40;
            $extraHours = $totalHours > $weeklyThreshold ? $totalHours - $weeklyThreshold : 0;

            return response()->json([
                'status' => 'success',
                'weekly_summary' => [
                    'total_hours' => $totalTime,
                    'extra_hours' => $extraHours
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao calcular resumo semanal: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao calcular resumo semanal'
            ], 500);
        }
    }

    /**
     * Get monthly summary of worked hours and extra hours.
     */
    public function monthlySummary(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            $user = Auth::user();
            $currentDate = Carbon::now()->setTimezone('America/Sao_Paulo');
            $startOfMonth = $currentDate->copy()->startOfMonth();
            $endOfMonth = $currentDate->copy()->endOfMonth();

            $pontos = Ponto::where('user_id', $user->id)
                ->whereBetween('entrada', [$startOfMonth, $endOfMonth])
                ->whereNotNull('saida')
                ->get();

            $totalSeconds = 0;

            foreach ($pontos as $ponto) {
                $entrada = Carbon::parse($ponto->entrada);
                $saida = Carbon::parse($ponto->saida);
                $totalSeconds += $entrada->diffInSeconds($saida);
            }

            $totalHours = floor($totalSeconds / 3600);
            $totalMinutes = floor(($totalSeconds % 3600) / 60);
            $totalTime = sprintf("%02d:%02d", $totalHours, $totalMinutes);

            // Define monthly threshold (e.g., 160 hours)
            $monthlyThreshold = 160;
            $extraHours = $totalHours > $monthlyThreshold ? $totalHours - $monthlyThreshold : 0;

            return response()->json([
                'status' => 'success',
                'monthly_summary' => [
                    'total_hours' => $totalTime,
                    'extra_hours' => $extraHours
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao calcular resumo mensal: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao calcular resumo mensal'
            ], 500);
        }
    }
}
