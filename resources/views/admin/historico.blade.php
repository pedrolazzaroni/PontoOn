@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-4 py-8 bg-white rounded-b-lg shadow-md" style="min-height: calc(100vh - 80px);">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-orange-600">Histórico de Pontos</h1>
        <div class="flex space-x-2">
            <span class="px-4 py-2 bg-orange-100 text-orange-600 rounded-lg text-sm font-medium">
                Total de Registros: {{ $users->sum(function($user) { return $user->pontos->count(); }) }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</span>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Registros</span>
                        </th>
                        <th class="px-6 py-4 text-center">
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
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $user->pontos->count() }} pontos registrados
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center">
                                    <button onclick="toggleDetails({{ $user->id }})"
                                            class="text-orange-600 hover:text-orange-900 transition-colors duration-200 flex items-center space-x-1">
                                        <span>Detalhes</span>
                                        <svg id="arrow-{{ $user->id }}" class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Detailed Records Row -->
                        <tr id="details-{{ $user->id }}" class="hidden bg-gray-50">
                            <td colspan="3" class="px-6 py-0">
                                <div id="details-content-{{ $user->id }}"
                                     class="transition-all duration-500 ease-in-out origin-top transform opacity-0"
                                     style="max-height: 0; overflow: hidden;">
                                    <div class="space-y-3 p-4">
                                        @foreach($user->pontos->sortByDesc('created_at') as $ponto)
                                        <div class="bg-white rounded-lg shadow-sm p-4 transform translate-y-4 opacity-0 transition-all duration-300"
                                             id="record-{{ $user->id }}-{{ $loop->index }}">
                                            <!-- Cabeçalho do registro -->
                                            <div class="flex justify-between items-center mb-3">
                                                <span class="font-medium text-gray-900">
                                                    {{ $ponto->created_at->format('d/m/Y') }}
                                                </span>
                                                <span class="text-xs px-2 py-1 bg-orange-100 text-orange-600 rounded-full">
                                                    {{ $ponto->saida ? 'Completo' : 'Em andamento' }}
                                                </span>
                                            </div>

                                            <!-- Registro de ponto -->
                                            <div class="bg-orange-50 rounded-lg p-3">
                                                <!-- Horários -->
                                                <div class="flex items-center justify-between space-x-4">
                                                    <!-- Entrada -->
                                                    <div class="flex-1">
                                                        <div class="flex items-center space-x-2">
                                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                            <span class="text-xs font-medium text-gray-600">Entrada</span>
                                                        </div>
                                                        <span class="text-sm font-semibold text-gray-800 ml-4">
                                                            {{ $ponto->entrada ? \Carbon\Carbon::parse($ponto->entrada)->format('H:i:s') : '-' }}
                                                        </span>
                                                    </div>

                                                    <!-- Seta indicativa -->
                                                    <div class="text-gray-400">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                        </svg>
                                                    </div>

                                                    <!-- Saída -->
                                                    <div class="flex-1 text-right">
                                                        <div class="flex items-center justify-end space-x-2">
                                                            <span class="text-xs font-medium text-gray-600">Saída</span>
                                                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                                        </div>
                                                        <span class="text-sm font-semibold text-gray-800 mr-4">
                                                            {{ $ponto->saida ? \Carbon\Carbon::parse($ponto->saida)->format('H:i:s') : '-' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Informações adicionais -->
                                                @if($ponto->saida)
                                                <div class="mt-3 pt-3 border-t border-orange-100 flex justify-between items-center">
                                                    <div class="flex space-x-4">
                                                        <span class="text-xs text-gray-600">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            Total: {{ $ponto->horas_trabalhadas }}
                                                        </span>
                                                        @if($ponto->horas_extras != '00:00:00')
                                                        <span class="text-xs text-green-600">
                                                            <i class="fas fa-plus-circle mr-1"></i>
                                                            Extras: {{ $ponto->horas_extras }}
                                                        </span>
                                                        @endif
                                                        @if($ponto->atraso != '00:00:00')
                                                        <span class="text-xs text-red-600">
                                                            <i class="fas fa-minus-circle mr-1"></i>
                                                            Atraso: {{ $ponto->atraso }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-100">
            @foreach($users as $user)
                @if($user->pontos->count() > 0)
                <div class="p-4">
                    <!-- User Info -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-orange-100 rounded-full flex items-center justify-center">
                                <span class="text-lg font-medium text-orange-600">
                                    {{ substr($user->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->pontos->count() }} registros</p>
                            </div>
                        </div>
                        <button onclick="toggleDetailsMobile({{ $user->id }})"
                                class="text-orange-600 hover:text-orange-900 transition-colors duration-200 flex items-center space-x-1">
                            <span class="text-sm">Detalhes</span>
                            <svg id="arrow-mobile-{{ $user->id }}" class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Collapsible Content -->
                    <div id="details-mobile-{{ $user->id }}" class="hidden">
                        <div class="space-y-3">
                            @foreach($user->pontos->sortByDesc('created_at') as $ponto)
                            <div class="bg-orange-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $ponto->created_at->format('d/m/Y') }}
                                    </span>
                                    <span class="text-xs px-2 py-1 bg-orange-100 text-orange-600 rounded-full">
                                        {{ $ponto->saida ? 'Completo' : 'Em andamento' }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Entrada:</span>
                                        <span class="font-medium">{{ $ponto->entrada ? \Carbon\Carbon::parse($ponto->entrada)->format('H:i:s') : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Saída:</span>
                                        <span class="font-medium">{{ $ponto->saida ? \Carbon\Carbon::parse($ponto->saida)->format('H:i:s') : '-' }}</span>
                                    </div>
                                    @if($ponto->saida)
                                    <div class="pt-2 border-t border-orange-100">
                                        <div class="flex flex-col space-y-1">
                                            <div class="flex justify-between text-xs">
                                                <span class="text-gray-600">Total:</span>
                                                <span class="font-medium">{{ $ponto->horas_trabalhadas }}</span>
                                            </div>
                                            @if($ponto->horas_extras != '00:00:00')
                                            <div class="flex justify-between text-xs">
                                                <span class="text-green-600">Extras:</span>
                                                <span class="font-medium text-green-600">{{ $ponto->horas_extras }}</span>
                                            </div>
                                            @endif
                                            @if($ponto->atraso != '00:00:00')
                                            <div class="flex justify-between text-xs">
                                                <span class="text-red-600">Atraso:</span>
                                                <span class="font-medium text-red-600">{{ $ponto->atraso }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- Pagination Section -->
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

<script>
function toggleDetails(userId) {
    const detailsRow = document.getElementById(`details-${userId}`);
    const detailsContent = document.getElementById(`details-content-${userId}`);
    const arrow = document.getElementById(`arrow-${userId}`);
    const records = document.querySelectorAll(`[id^="record-${userId}-"]`);

    if (detailsRow.classList.contains('hidden')) {
        // Show the row first
        detailsRow.classList.remove('hidden');

        // Start animation after a small delay
        setTimeout(() => {
            // Expand content
            detailsContent.style.maxHeight = `${detailsContent.scrollHeight}px`;
            detailsContent.classList.remove('opacity-0');

            // Animate each record with delay
            records.forEach((record, index) => {
                setTimeout(() => {
                    record.classList.remove('translate-y-4', 'opacity-0');
                }, index * 100); // Stagger animation
            });
        }, 50);

        // Rotate arrow
        arrow.classList.add('rotate-180');
    } else {
        // Collapse animation
        detailsContent.style.maxHeight = '0px';
        detailsContent.classList.add('opacity-0');

        // Animate records out
        records.forEach((record) => {
            record.classList.add('translate-y-4', 'opacity-0');
        });

        // Rotate arrow back
        arrow.classList.remove('rotate-180');

        // Hide row after animation
        setTimeout(() => {
            detailsRow.classList.add('hidden');
        }, 500); // Match duration with CSS transition
    }
}

function toggleDetailsMobile(userId) {
    const detailsContent = document.getElementById(`details-mobile-${userId}`);
    const arrow = document.getElementById(`arrow-mobile-${userId}`);

    if (detailsContent.classList.contains('hidden')) {
        detailsContent.classList.remove('hidden');
        arrow.classList.add('rotate-180');

        // Animar a expansão do conteúdo
        detailsContent.style.opacity = '0';
        detailsContent.style.maxHeight = '0';

        setTimeout(() => {
            detailsContent.style.opacity = '1';
            detailsContent.style.maxHeight = `${detailsContent.scrollHeight}px`;
        }, 50);
    } else {
        // Animar o recolhimento do conteúdo
        detailsContent.style.opacity = '0';
        detailsContent.style.maxHeight = '0';
        arrow.classList.remove('rotate-180');

        setTimeout(() => {
            detailsContent.classList.add('hidden');
        }, 300);
    }
}
</script>
@endsection
