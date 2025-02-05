@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-4 py-8 bg-white rounded-b-lg shadow-md" style="min-height: calc(100vh - 80px);">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-orange-500">Relatório</h1>
            <p class="text-gray-600">Gere relatórios personalizados dos seus funcionários</p>
        </div>
    </div>

    <!-- Filtros Aprimorados -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form id="reportForm" action="{{ route('admin.relatorio') }}" method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Select Funcionário Estilizado -->
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Funcionário</label>
                    <div class="relative">
                        <select name="user_id"
                                class="appearance-none w-full bg-gray-50 border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="">Todos os funcionários</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Data Inicial Estilizada -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Data Inicial</label>
                    <input type="date" name="start_date"
                           value="{{ request('start_date', date('Y-m-d', strtotime('-7 days'))) }}"
                           class="w-full bg-gray-50 border border-gray-300 text-gray-700 py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Data Final Estilizada -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Data Final</label>
                    <input type="date" name="end_date"
                           value="{{ request('end_date', date('Y-m-d')) }}"
                           class="w-full bg-gray-50 border border-gray-300 text-gray-700 py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="window.location.href='{{ route('admin.relatorio') }}'"
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Limpar Filtros
                </button>
                <button type="submit"
                        class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Gerar Relatório
                </button>
            </div>
        </form>
    </div>

    <!-- Área dos Gráficos -->
    <div id="chartsArea" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 hidden">
        <!-- Gráfico de Horas Trabalhadas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Horas Trabalhadas</h3>
            <div id="workingHoursChart"></div>
        </div>

        <!-- Gráfico de Horas Extras -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Horas Extras</h3>
            <div id="overtimeChart"></div>
        </div>

        <!-- Gráfico de Atrasos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Atrasos</h3>
            <div id="lateChart"></div>
        </div>

        <!-- Resumo Estatístico -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Resumo</h3>
            <div id="statsDisplay" class="space-y-4">
                <!-- Será preenchido via JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Substitua o script do Chart.js pelo Google Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    // Carrega a API de visualização e o pacote corechart
    google.charts.load('current', {'packages':['corechart']});

    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        // Mostrar área de gráficos
        document.getElementById('chartsArea').classList.remove('hidden');

        // Fazer requisição AJAX para buscar dados
        fetch(`/admin/relatorio/dados?${new URLSearchParams(formData)}`)
            .then(response => response.json())
            .then(data => {
                // Prepara dados para os gráficos do Google
                const workingHoursData = [['Data', 'Horas Trabalhadas']];
                const overtimeData = [['Data', 'Horas Extras']];
                const lateData = [['Data', 'Atrasos']];

                data.dates.forEach((date, index) => {
                    workingHoursData.push([date, data.workingHours[index]]);
                    overtimeData.push([date, data.overtime[index]]);
                    lateData.push([date, data.late[index]]);
                });

                // Gráfico de Horas Trabalhadas (Line Chart)
                const workingHoursChart = new google.visualization.LineChart(
                    document.getElementById('workingHoursChart')
                );
                workingHoursChart.draw(
                    google.visualization.arrayToDataTable(workingHoursData),
                    {
                        title: 'Horas Trabalhadas por Dia',
                        curveType: 'function',
                        legend: { position: 'bottom' },
                        colors: ['#f97316'],
                        pointSize: 5,
                        lineWidth: 3,
                        backgroundColor: { fill:'transparent' }
                    }
                );

                // Gráfico de Horas Extras (Column Chart)
                const overtimeChart = new google.visualization.ColumnChart(
                    document.getElementById('overtimeChart')
                );
                overtimeChart.draw(
                    google.visualization.arrayToDataTable(overtimeData),
                    {
                        title: 'Horas Extras por Dia',
                        legend: { position: 'bottom' },
                        colors: ['#f97316'],
                        backgroundColor: { fill:'transparent' }
                    }
                );

                // Gráfico de Atrasos (Column Chart)
                const lateChart = new google.visualization.ColumnChart(
                    document.getElementById('lateChart')
                );
                lateChart.draw(
                    google.visualization.arrayToDataTable(lateData),
                    {
                        title: 'Atrasos por Dia',
                        legend: { position: 'bottom' },
                        colors: ['#ef4444'],
                        backgroundColor: { fill:'transparent' }
                    }
                );

                // Atualizar resumo estatístico
                const statsHtml = `
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-orange-50 rounded-lg">
                            <p class="text-sm text-gray-600">Média de Horas/Dia</p>
                            <p class="text-2xl font-bold text-orange-600">${data.stats.avgHours}h</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-gray-600">Total Horas Extras</p>
                            <p class="text-2xl font-bold text-green-600">${data.stats.totalOvertime}h</p>
                        </div>
                        <div class="p-4 bg-red-50 rounded-lg">
                            <p class="text-sm text-gray-600">Total Atrasos</p>
                            <p class="text-2xl font-bold text-red-600">${data.stats.totalLate}h</p>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-gray-600">Dias Trabalhados</p>
                            <p class="text-2xl font-bold text-blue-600">${data.stats.daysWorked}</p>
                        </div>
                    </div>
                `;
                document.getElementById('statsDisplay').innerHTML = statsHtml;
            });
    });

    // Função para redimensionar os gráficos quando a janela for redimensionada
    window.addEventListener('resize', function() {
        if (typeof workingHoursChart !== 'undefined') workingHoursChart.draw(workingHoursData, workingHoursOptions);
        if (typeof overtimeChart !== 'undefined') overtimeChart.draw(overtimeData, overtimeOptions);
        if (typeof lateChart !== 'undefined') lateChart.draw(lateData, lateOptions);
    });
</script>

@endsection
