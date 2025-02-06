<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ponto;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RelatorioController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('responsavel_id', auth()->id())->get();

        $query = Ponto::query()->with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id());
            });

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $pontos = $query->get();

        // Prepare data for charts
        $dates = $pontos->pluck('created_at')->map(fn($date) => $date->format('d/m'))->unique()->values();

        $workingHours = [];
        $overtime = [];
        $late = [];
        $totalSeconds = 0;
        $totalOvertimeSeconds = 0;
        $totalLateSeconds = 0;
        $maxSeconds = 0;
        $minSeconds = PHP_FLOAT_MAX;
        $daysPresent = 0;
        $totalEntries = 0;

        foreach ($dates as $date) {
            $dayPoints = $pontos->filter(fn($p) => $p->created_at->format('d/m') === $date);
            $daySeconds = 0;
            $dayOvertimeSeconds = 0;
            $dayLateSeconds = 0;

            foreach ($dayPoints as $ponto) {
                // Calculate working hours
                if ($ponto->entrada && $ponto->saida) {
                    $entrada = Carbon::parse($ponto->entrada);
                    $saida = Carbon::parse($ponto->saida);
                    $daySeconds += $entrada->diffInSeconds($saida);
                    $totalEntries++;
                }

                // Calculate overtime - Fixed the variable name conflict and parsing
                if ($ponto->horas_extras) {
                    $overtimeTime = Carbon::parse($ponto->horas_extras);
                    $startOfDay = Carbon::today();
                    $dayOvertimeSeconds += $startOfDay->diffInSeconds($overtimeTime);
                }

                // Calculate late time
                $scheduledStart = Carbon::parse($ponto->created_at->format('Y-m-d') . ' 09:00:00');
                if ($ponto->entrada && Carbon::parse($ponto->entrada)->greaterThan($scheduledStart)) {
                    $dayLateSeconds += $scheduledStart->diffInSeconds(Carbon::parse($ponto->entrada));
                }
            }

            if ($daySeconds > 0) {
                $daysPresent++;
                $maxSeconds = max($maxSeconds, $daySeconds);
                $minSeconds = min($minSeconds, $daySeconds);
            }

            $totalSeconds += $daySeconds;
            $totalOvertimeSeconds += $dayOvertimeSeconds;
            $totalLateSeconds += $dayLateSeconds;

            // Convert seconds to hours for charts
            $workingHours[] = round($daySeconds / 3600, 2);
            $overtime[] = round($dayOvertimeSeconds / 3600, 2);
            $late[] = round($dayLateSeconds / 3600, 2);
        }

        $stats = [
            'mediaHoras' => $daysPresent ? round($totalSeconds / ($daysPresent * 3600), 2) : 0,
            'totalHorasExtras' => round($totalOvertimeSeconds / 3600, 2),
            'totalAtrasos' => round($totalLateSeconds / 3600, 2),
            'diasTrabalhados' => $daysPresent,
            'maxHoras' => round($maxSeconds / 3600, 2),
            'minHoras' => $daysPresent ? round($minSeconds / 3600, 2) : 0,
            'totalRegistros' => $totalEntries,
            'mediaRegistrosDia' => $daysPresent ? round($totalEntries / $daysPresent, 1) : 0,
            'horasTotais' => round($totalSeconds / 3600, 2),
        ];

        return view('admin.relatorio', compact('users', 'dates', 'workingHours', 'overtime', 'late', 'stats'));
    }
}
