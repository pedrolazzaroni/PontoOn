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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total de Horas Extras</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->overtimes->sum('horas_extras') }} horas
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
