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

        // Calcula total de horas extras considerando o formato HH:MM:SS
        $users->getCollection()->transform(function($user) {
            $user->total_horas_extras = $user->pontos->sum(function($ponto) {
                list($hours, $minutes, $seconds) = explode(':', $ponto->horas_extras);
                return number_format($hours + ($minutes/60) + ($seconds/3600), 2);
            });
            return $user;
        });

        return view('admin.hora-extra', compact('users'));
    }
}
