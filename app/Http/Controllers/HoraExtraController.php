<?php
namespace App\Http\Controllers;

use App\Models\User;
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
            ->paginate(20);

        // Calculate total overtime for each user
        $users->getCollection()->transform(function($user) {
            $user->total_horas_extras = $user->pontos->sum(function($ponto) {
                list($hours, $minutes, $seconds) = explode(':', $ponto->horas_extras);
                return $hours + ($minutes/60) + ($seconds/3600);
            });
            return $user;
        });

        return view('admin.hora-extra', compact('users'));
    }
}
