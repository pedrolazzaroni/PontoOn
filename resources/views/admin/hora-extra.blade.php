@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-4 py-8 bg-white rounded-b-lg shadow-md" style="min-height: calc(100vh - 80px);">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Horas Extras dos Usuários</h1>

    <div class="bg-white rounded-lg shadow-md p-6">
        <table class="min-w-full">
            <thead class="bg-orange-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuário</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horas Extras</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Acumulado</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                    @if($user->pontos->count() > 0)
                        @foreach($user->pontos as $ponto)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ponto->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-500 font-medium">
                                +{{ $ponto->horas_extras }}
                            </td>
                            @if($loop->first)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" rowspan="{{ $user->pontos->count() }}">
                                {{ number_format($user->total_horas_extras, 2) }} horas
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>

        @if($users->sum('total_horas_extras') == 0)
        <div class="text-center py-4 text-gray-500">
            Nenhum registro de hora extra encontrado.
        </div>
        @endif
    </div>
</div>
@endsection
