@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-4 py-8 bg-white rounded-b-lg shadow-md" style="min-height: calc(100vh - 80px);">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-orange-600">Horas em Atraso</h1>
        <span class="px-4 py-2 bg-orange-100 text-orange-600 rounded-lg text-sm font-medium">
            Total de Registros: {{ $users->total() }}
        </span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Usuário</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Atraso Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                {{ $user->late_hours }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">
                            Nenhum registro de atraso encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-6 border-t border-gray-100">
            <div class="flex flex-col items-center">
                <div class="flex justify-center space-x-1 mb-4">
                    @if($users->onFirstPage())
                        <span class="px-4 py-2 text-gray-400 bg-gray-50 rounded-lg cursor-not-allowed">
                            Anterior
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}"
                           class="px-4 py-2 text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors duration-200">
                            Anterior
                        </a>
                    @endif

                    <div class="flex space-x-1">
                        @foreach ($users->getUrlRange(max($users->currentPage() - 2, 1), min($users->currentPage() + 2, $users->lastPage())) as $page => $url)
                            <a href="{{ $url }}"
                               class="px-4 py-2 {{ $page == $users->currentPage()
                                    ? 'bg-orange-500 text-white'
                                    : 'text-orange-600 bg-orange-50 hover:bg-orange-100' }}
                                    rounded-lg transition-colors duration-200">
                                {{ $page }}
                            </a>
                        @endforeach
                    </div>

                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}"
                           class="px-4 py-2 text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors duration-200">
                            Próxima
                        </a>
                    @else
                        <span class="px-4 py-2 text-gray-400 bg-gray-50 rounded-lg cursor-not-allowed">
                            Próxima
                        </span>
                    @endif
                </div>
                <div class="text-sm text-gray-500">
                    Mostrando {{ $users->firstItem() }} até {{ $users->lastItem() }} de {{ $users->total() }} registros
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
