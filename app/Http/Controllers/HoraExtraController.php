<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HoraExtraController extends Controller
{
    public function overtime()
    {
        $users = User::with(['overtimes' => function($query) {
            $query->join('users', 'pontos.user_id', '=', 'users.id')
                  ->whereRaw('TIMESTAMPDIFF(HOUR, entrada, saida) > users.expediente')
                  ->whereNotNull('saida');
        }])
        ->where('responsavel_id', auth()->id())
        ->get();

        return view('admin.hora-extra', compact('users'));
    }
}
