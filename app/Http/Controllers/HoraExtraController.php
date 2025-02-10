<?php
namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HoraExtraController extends Controller
{
    public function overtime()
    {
        $users = User::where('responsavel_id', auth()->id())
            ->where('status', true)
            ->with(['pontos' => function($query) {
                $query->whereNotNull('horas_extras')
                    ->where('horas_extras', '>', '00:00:00')
                    ->orderBy('created_at', 'desc');
            }])
            ->paginate(10);

        $totalRecords = 0;

        // Calcula total de horas extras considerando o formato HH:MM:SS
        $users->getCollection()->transform(function($user) use (&$totalRecords) {
            // Incrementa o contador de registros
            $totalRecords += $user->pontos->count();

            // Calcula o total de segundos das horas extras
            $totalSeconds = 0;
            foreach ($user->pontos as $ponto) {
                if (!empty($ponto->horas_extras)) {
                    $timeParts = explode(':', $ponto->horas_extras);
                    if (count($timeParts) === 3) {
                        $totalSeconds += ((int)$timeParts[0] * 3600) + ((int)$timeParts[1] * 60) + (int)$timeParts[2];
                    }
                }

                // Formata as datas do ponto
                $ponto->data_formatada = Carbon::parse($ponto->entrada)->format('d/m/Y');
                $ponto->hora_entrada_formatada = Carbon::parse($ponto->entrada)->format('H:i');
                $ponto->hora_saida_formatada = $ponto->saida ? Carbon::parse($ponto->saida)->format('H:i') : null;
            }

            // Converte o total de segundos para formato HH:MM:SS
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = $totalSeconds % 60;
            $user->total_horas_extras = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            return $user;
        });

        return view('admin.hora-extra', compact('users', 'totalRecords'));
    }
}
