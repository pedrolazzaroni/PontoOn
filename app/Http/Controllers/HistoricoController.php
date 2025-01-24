<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
}
