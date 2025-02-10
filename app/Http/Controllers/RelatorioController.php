<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ponto;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Http;

class RelatorioController extends Controller
{
    protected function getFilteredData(Request $request)
    {
        $users = User::where('responsavel_id', auth()->id())->get();
        $query = Ponto::query()->with('user')
            ->whereHas('user', function($query) {
                $query->where('responsavel_id', auth()->id());
            });

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return [$users, $query->get()];
    }

    protected function processData($pontos)
    {
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
            $dayStats = $this->processDayStats($dayPoints);

            // Atualizar totais
            if ($dayStats['seconds'] > 0) {
                $daysPresent++;
                $maxSeconds = max($maxSeconds, $dayStats['seconds']);
                $minSeconds = min($minSeconds, $dayStats['seconds']);
            }

            $totalSeconds += $dayStats['seconds'];
            $totalOvertimeSeconds += $dayStats['overtimeSeconds'];
            $totalLateSeconds += $dayStats['lateSeconds'];
            $totalEntries += $dayStats['entries'];

            // Dados para gráficos
            $workingHours[] = round($dayStats['seconds'] / 3600, 2);
            $overtime[] = round($dayStats['overtimeSeconds'] / 3600, 2);
            $late[] = round($dayStats['lateSeconds'] / 3600, 2);
        }

        return [
            'dates' => $dates,
            'workingHours' => $workingHours,
            'overtime' => $overtime,
            'late' => $late,
            'stats' => $this->calculateStats($totalSeconds, $totalOvertimeSeconds, $totalLateSeconds, $daysPresent, $totalEntries, $maxSeconds, $minSeconds)
        ];
    }

    protected function processDayStats($dayPoints)
    {
        $stats = ['seconds' => 0, 'overtimeSeconds' => 0, 'lateSeconds' => 0, 'entries' => 0];

        foreach ($dayPoints as $ponto) {
            if ($ponto->entrada && $ponto->saida) {
                $entrada = Carbon::parse($ponto->entrada);
                $saida = Carbon::parse($ponto->saida);
                $stats['seconds'] += $entrada->diffInSeconds($saida);
                $stats['entries']++;
            }

            if ($ponto->horas_extras && $ponto->horas_extras !== '00:00:00') {
                list($hours, $minutes, $seconds) = explode(':', $ponto->horas_extras);
                $stats['overtimeSeconds'] += ($hours * 3600) + ($minutes * 60) + $seconds;
            }

            if ($ponto->atraso && $ponto->atraso !== '00:00:00') {
                list($hours, $minutes, $seconds) = explode(':', $ponto->atraso);
                $stats['lateSeconds'] += ($hours * 3600) + ($minutes * 60) + $seconds;
            }
        }

        return $stats;
    }

    protected function calculateStats($totalSeconds, $totalOvertimeSeconds, $totalLateSeconds, $daysPresent, $totalEntries, $maxSeconds, $minSeconds)
    {
        return [
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
    }

    protected function generateChartUrls($dates, $workingHours, $overtime, $late)
    {
        $chartBaseUrl = "https://quickchart.io/chart?c=";

        $workingHoursChart = $chartBaseUrl . urlencode(json_encode([
            'type' => 'line',
            'data' => [
                'labels' => $dates,
                'datasets' => [[
                    'label' => 'Horas Trabalhadas',
                    'data' => $workingHours,
                    'borderColor' => '#f97316',
                    'fill' => false
                ]]
            ]
        ]));

        $overtimeChart = $chartBaseUrl . urlencode(json_encode([
            'type' => 'bar',
            'data' => [
                'labels' => $dates,
                'datasets' => [[
                    'label' => 'Horas Extras',
                    'data' => $overtime,
                    'backgroundColor' => '#f97316'
                ]]
            ]
        ]));

        $lateChart = $chartBaseUrl . urlencode(json_encode([
            'type' => 'bar',
            'data' => [
                'labels' => $dates,
                'datasets' => [[
                    'label' => 'Atrasos',
                    'data' => $late,
                    'backgroundColor' => '#ef4444'
                ]]
            ]
        ]));

        return [$workingHoursChart, $overtimeChart, $lateChart];
    }

    public function index(Request $request)
    {
        [$users, $pontos] = $this->getFilteredData($request);
        $data = $this->processData($pontos);

        return view('admin.relatorio', array_merge(
            ['users' => $users],
            $data
        ));
    }

    public function downloadPDF(Request $request)
    {
        [$users, $pontos] = $this->getFilteredData($request);
        $data = $this->processData($pontos);

        $userName = $request->filled('user_id')
            ? User::find($request->user_id)->name
            : 'Todos os funcionários';

        // Converter as Collections para arrays
        $dates = $data['dates']->toArray();
        $workingHours = $data['workingHours'];
        $overtime = $data['overtime'];
        $late = $data['late'];

        // Gerar URLs dos gráficos usando Google Image Charts
        $workingHoursChart = 'https://chart.googleapis.com/chart?' . http_build_query([
            'cht' => 'lc', // line chart
            'chs' => '800x400',
            'chd' => 't:' . implode(',', $workingHours),
            'chl' => implode('|', $dates),
            'chtt' => 'Horas Trabalhadas',
            'chco' => 'f97316', // cor laranja
            'chxt' => 'x,y',
            'chxl' => '0:|' . implode('|', $dates),
            'chf' => 'bg,s,FFFFFF00'
        ]);

        $overtimeChart = 'https://chart.googleapis.com/chart?' . http_build_query([
            'cht' => 'bvs', // bar chart
            'chs' => '800x400',
            'chd' => 't:' . implode(',', $overtime),
            'chl' => implode('|', $dates),
            'chtt' => 'Horas Extras',
            'chco' => 'f97316',
            'chxt' => 'x,y',
            'chxl' => '0:|' . implode('|', $dates),
            'chf' => 'bg,s,FFFFFF00'
        ]);

        $lateChart = 'https://chart.googleapis.com/chart?' . http_build_query([
            'cht' => 'bvs',
            'chs' => '800x400',
            'chd' => 't:' . implode(',', $late),
            'chl' => implode('|', $dates),
            'chtt' => 'Atrasos',
            'chco' => 'ef4444', // cor vermelha
            'chxt' => 'x,y',
            'chxl' => '0:|' . implode('|', $dates),
            'chf' => 'bg,s,FFFFFF00'
        ]);

        $pdf = FacadePdf::loadView('admin.pdf.relatorio', [
            'stats' => $data['stats'],
            'userName' => $userName,
            'dates' => $data['dates'],
            'workingHours' => $data['workingHours'],
            'overtime' => $data['overtime'],
            'late' => $data['late'],
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'workingHoursChart' => $workingHoursChart,
            'overtimeChart' => $overtimeChart,
            'lateChart' => $lateChart
        ]);

        // Configurar o PDF
        $pdf->setOption([
            'enable-smart-shrinking' => true,
            'enable-remote' => true,
            'images' => true,
            'isRemoteEnabled' => true
        ]);

        return $pdf->download('relatorio-' . now()->format('Y-m-d') . '.pdf');
    }
}
