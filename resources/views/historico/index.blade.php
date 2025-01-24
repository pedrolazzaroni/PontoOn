@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Histórico de Pontos</h1>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Filtros</h2>
            <form id="filtroForm" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                    <input type="date" name="data_inicio"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                    <input type="date" name="data_fim"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-orange-400 text-white rounded-md hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    Filtrar
                </button>
            </form>
        </div>

        <!-- Tabela -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-orange-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saída</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tempo Total</th>
                        </tr>
                    </thead>
                    <tbody id="historicoTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Dados serão inseridos aqui via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function carregarHistorico(filtros = {}) {
        const queryString = new URLSearchParams(filtros).toString();
        fetch(`/historico/dados?${queryString}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('historicoTableBody');
                tbody.innerHTML = '';

                data.data.forEach(registro => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${registro.user_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${registro.data}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${registro.entrada}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${registro.saida}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${registro.tempo_trabalhado}</td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => console.error('Erro ao carregar histórico:', error));
    }

    document.getElementById('filtroForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const filtros = Object.fromEntries(formData.entries());
        carregarHistorico(filtros);
    });

    // Carregar histórico inicial
    carregarHistorico();
});
</script>
@endsection
