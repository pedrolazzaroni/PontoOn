@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-4 py-8 bg-white rounded-b-lg shadow-md" style="min-height: calc(100vh - 80px);">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Histórico de Pontos</h1>

    <div class="bg-white rounded-lg shadow-md p-6">
        <table class="min-w-full">
            <thead class="bg-orange-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuário</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saída</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tempo Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora Extra</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($historico as $ponto)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ponto->user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($ponto->entrada)->format('d/m/Y H:i:s') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ponto->saida ? \Carbon\Carbon::parse($ponto->saida)->format('d/m/Y H:i:s') : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($ponto->saida)
                            @php
                                $entrada = \Carbon\Carbon::parse($ponto->entrada);
                                $saida = \Carbon\Carbon::parse($ponto->saida);
                                $tempoTotal = $entrada->diff($saida)->format('%H:%I:%S');
                            @endphp
                            {{ $tempoTotal }}
                        @else
                            Em andamento
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($ponto->horas_extras)
                            {{ $ponto->horas_extras }} horas
                        @else
                            0 horas
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $ponto->saida ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $ponto->saida ? 'Saída' : 'Entrada' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
