<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório de Ponto</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 15px;
        }
        h1 {
            color: #f97316;
            margin-bottom: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
        }
        .stat-value.orange { color: #f97316; }
        .stat-value.blue { color: #3b82f6; }
        .stat-value.green { color: #22c55e; }
        .stat-value.red { color: #ef4444; }
        .stat-value.gray { color: #4b5563; }
        .stat-value.yellow { color: #eab308; }
        .sub-value {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Ponto</h1>
        <p>Funcionário: {{ $userName }}</p>
        <p>Período: {{ $startDate }} até {{ $endDate }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Média de Horas/Dia</div>
            <div class="stat-value orange">{{ $stats['mediaHoras'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total de Horas</div>
            <div class="stat-value blue">{{ $stats['horasTotais'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total Horas Extras</div>
            <div class="stat-value green">{{ $stats['totalHorasExtras'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total Atrasos</div>
            <div class="stat-value red">{{ $stats['totalAtrasos'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total de Registros</div>
            <div class="stat-value gray">{{ $stats['totalRegistros'] }}</div>
            <div class="sub-value">Média: {{ $stats['mediaRegistrosDia'] }}/dia</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Dias Trabalhados</div>
            <div class="stat-value gray">{{ $stats['diasTrabalhados'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Máximo de Horas/Dia</div>
            <div class="stat-value green">{{ $stats['maxHoras'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Mínimo de Horas/Dia</div>
            <div class="stat-value yellow">{{ $stats['minHoras'] }}</div>
        </div>
    </div>
</body>
</html>
