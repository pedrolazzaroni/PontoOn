<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PontoController extends Controller
{
    public function register()
    {
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
            $ponto = Ponto::create([
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
        ]);
    }

    public function status()
    {
        $user = Auth::user();
        $currentPonto = Ponto::where('user_id', $user->id)
                            ->whereNull('saida')
                            ->latest()
                            ->first();

        $lastCompletePonto = Ponto::where('user_id', $user->id)
                                 ->whereNotNull('saida')
                                 ->latest()
                                 ->first();

        $lastRecord = $currentPonto ? $currentPonto->entrada :
                     ($lastCompletePonto ? $lastCompletePonto->saida : null);

        return response()->json([
            'working' => !is_null($currentPonto),
            'last_record' => $lastRecord ? $lastRecord->format('d/m/Y H:i:s') : null,
            'entrada' => $currentPonto ? $currentPonto->entrada->format('d/m/Y H:i:s') : null
        ]);
    }
}
