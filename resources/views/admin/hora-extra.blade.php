@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-4 py-8 bg-white rounded-b-lg shadow-md" style="min-height: calc(100vh - 80px);">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Horas Extras dos Usuários</h1>
        <div class="flex space-x-2">
            <span class="px-4 py-2 bg-orange-100 text-orange-600 rounded-lg text-sm font-medium">
                Total de Registros: {{ $users->sum(function($user) { return $user->pontos->count(); }) }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</span>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Acumulado</span>
                        </th>
                        <th class="px-6 py-4 text-right">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($users as $user)
                        @if($user->pontos->count() > 0)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-orange-100 rounded-full flex items-center justify-center">
                                        <span class="text-lg font-medium text-orange-600">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->pontos->count() }} registros</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ number_format($user->total_horas_extras, 2) }} horas
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="toggleDetails({{ $user->id }})"
                                        class="text-orange-600 hover:text-orange-900 transition-colors duration-200 flex items-center justify-end space-x-1">
                                    <span>Detalhes</span>
                                    <svg id="arrow-{{ $user->id }}" class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <!-- Detailed Records Row -->
                        <tr id="details-{{ $user->id }}" class="hidden bg-gray-50">
                            <td colspan="3" class="px-6 py-4">
                                <div class="space-y-3">
                                    @foreach($user->pontos as $ponto)
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                                        <div>
                                            <div class="text-sm text-gray-900">{{ $ponto->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $ponto->created_at->format('H:i') }}</div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                            +{{ $ponto->horas_extras }}
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            @if($users->sum('total_horas_extras') == 0)
            <div class="text-center py-8">
                <div class="text-gray-400 text-lg">Nenhum registro de hora extra encontrado</div>
            </div>
            @endif
        </div>

        <!-- Pagination Section -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Mostrando {{ $users->firstItem() }} até {{ $users->lastItem() }} de {{ $users->total() }} registros
                </div>
                <div class="flex space-x-1">
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
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function toggleDetails(userId) {
    const detailsRow = document.getElementById(`details-${userId}`);
    const arrow = document.getElementById(`arrow-${userId}`);

    if (detailsRow.classList.contains('hidden')) {
        detailsRow.classList.remove('hidden');
        arrow.classList.add('rotate-180');
    } else {
        detailsRow.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
}
</script>
@endsection
