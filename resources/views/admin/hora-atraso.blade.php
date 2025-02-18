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
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Usuário</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Atraso Total</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
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
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                {{ $user->total_late_hours }}
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
                    <!-- Linha de detalhes expandível -->
                    <tr id="details-{{ $user->id }}" class="hidden bg-gray-50">
                        <td colspan="3" class="px-6 py-0">
                            <div id="details-content-{{ $user->id }}"
                                 class="transition-all duration-500 ease-in-out origin-top transform opacity-0"
                                 style="max-height: 0; overflow: hidden;">
                                <div class="space-y-3 p-4">
                                    @foreach($user->pontos as $ponto)
                                    <div class="bg-white rounded-lg shadow-sm p-4 transform translate-y-4 opacity-0 transition-all duration-300"
                                         id="record-{{ $user->id }}-{{ $loop->index }}">
                                        <!-- Cabeçalho do registro -->
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="font-medium text-gray-900">
                                                {{ Carbon\Carbon::parse($ponto->created_at)->format('d/m/Y') }}
                                            </span>
                                        </div>

                                        <!-- Registro do atraso -->
                                        <div class="bg-orange-50 rounded-lg p-3">
                                            <div class="flex items-center justify-between space-x-4">
                                                <!-- Entrada e Saída -->
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-xs font-medium text-gray-600">Entrada/Saída</span>
                                                    </div>
                                                    <span class="text-sm font-semibold text-gray-800">
                                                        {{ Carbon\Carbon::parse($ponto->entrada)->format('H:i:s') }} -
                                                        {{ $ponto->saida ? Carbon\Carbon::parse($ponto->saida)->format('H:i:s') : 'Em andamento' }}
                                                    </span>
                                                </div>

                                                <!-- Tempo de Almoço -->
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-xs font-medium text-gray-600">Tempo de Almoço</span>
                                                    </div>
                                                    <span class="text-sm font-semibold text-gray-800">
                                                        @if($ponto->entrada_almoco && $ponto->saida_almoco)
                                                            @php
                                                                $entrada_almoco = Carbon\Carbon::parse($ponto->entrada_almoco);
                                                                $saida_almoco = Carbon\Carbon::parse($ponto->saida_almoco);
                                                                $tempo_almoco = $entrada_almoco->diff($saida_almoco)->format('%H:%I:%S');
                                                            @endphp
                                                            {{ $tempo_almoco }}
                                                        @else
                                                            -
                                                        @endif
                                                    </span>
                                                </div>

                                                <!-- Tempo de Atraso -->
                                                <div class="flex-1 text-right">
                                                    <div class="flex items-center justify-end space-x-2">
                                                        <span class="text-xs font-medium text-red-600">Tempo de Atraso</span>
                                                    </div>
                                                    <span class="text-sm font-semibold text-red-600">
                                                        {{ $ponto->atraso }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                            Nenhum registro de atraso encontrado.
                        </td>
                    </tr>
                    @endforelse
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
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            {{ $user->total_late_hours }}
                        </span>
                    </div>

                    <!-- Toggle Details Button -->
                    <button onclick="toggleDetailsMobile({{ $user->id }})"
                            class="w-full flex items-center justify-center space-x-2 bg-orange-50 text-orange-600 px-4 py-2 rounded-lg hover:bg-orange-100 transition-colors duration-200">
                        <span class="text-sm">Ver Detalhes</span>
                        <svg id="arrow-mobile-{{ $user->id }}" class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Collapsible Content -->
                    <div id="details-mobile-{{ $user->id }}" class="hidden mt-4">
                        <div class="space-y-3">
                            @foreach($user->pontos as $ponto)
                            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ Carbon\Carbon::parse($ponto->created_at)->format('d/m/Y') }}
                                        </p>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-xs text-gray-500">Entrada/Saída</p>
                                                <p class="text-sm text-gray-700">
                                                    {{ Carbon\Carbon::parse($ponto->entrada)->format('H:i:s') }} -
                                                    {{ $ponto->saida ? Carbon\Carbon::parse($ponto->saida)->format('H:i:s') : 'Em andamento' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Tempo de Almoço</p>
                                                <p class="text-sm text-gray-700">
                                                    @if($ponto->entrada_almoco && $ponto->saida_almoco)
                                                        @php
                                                            $entrada_almoco = Carbon\Carbon::parse($ponto->entrada_almoco);
                                                            $saida_almoco = Carbon\Carbon::parse($ponto->saida_almoco);
                                                            $tempo_almoco = $entrada_almoco->diff($saida_almoco)->format('%H:%I:%S');
                                                        @endphp
                                                        {{ $tempo_almoco }}
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                        {{ $ponto->atraso }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        @if($users->hasPages())
        <div class="px-6 py-6 border-t border-gray-100">
            {{ $users->links() }}
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
        records.forEach((record, index) => {
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

        // Animate content expansion
        detailsContent.style.opacity = '0';
        detailsContent.style.maxHeight = '0';

        setTimeout(() => {
            detailsContent.style.opacity = '1';
            detailsContent.style.maxHeight = `${detailsContent.scrollHeight}px`;
        }, 50);
    } else {
        // Animate content collapse
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
