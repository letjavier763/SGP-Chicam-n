<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Estadísticas — CAP Chicamán</title>
@include('reportes.pdf._base')
</head>
<body>

{{-- Encabezado --}}
<table class="header">
    <tr>
        <td style="width: 60%;">
            <div class="inst-title">Centro de Atención Permanente — CAP Chicamán</div>
            <div class="inst-sub">Informe Estadístico</div>
        </td>
        <td style="width: 40%;" class="header-meta">
            <div><strong>Período:</strong> {{ $fechaDesde }} al {{ $fechaHasta }}</div>
            <div><strong>Total Atenciones:</strong> {{ $totalLlegadas }}</div>
            <div><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</div>
        </td>
    </tr>
</table>

{{-- Resumen --}}
<table class="stats-table">
    <tr>
        <td class="stat-card primary" style="width: 33.33%;">
            <span class="stat-num">{{ $totalLlegadas }}</span>
            <span class="stat-label">Total Llegadas</span>
        </td>
        <td class="stat-card success" style="width: 33.33%;">
            <span class="stat-num">{{ $totalNuevos }}</span>
            <span class="stat-label">Nuevos</span>
        </td>
        <td class="stat-card neutral" style="width: 33.33%;">
            <span class="stat-num">{{ $totalRecurrentes }}</span>
            <span class="stat-label">Recurrentes</span>
        </td>
    </tr>
</table>

{{-- Distribución por Sexo --}}
<div class="section-title">Distribución por Sexo</div>
<table class="data-table">
    <thead>
        <tr>
            <th>Sexo</th>
            <th style="width: 30%;">Atenciones</th>
            <th style="width: 30%;">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
    @foreach($porSexo as $s)
        <tr>
            <td><strong>{{ $s->sexo === 'F' ? 'Femenino' : 'Masculino' }}</strong></td>
            <td>{{ $s->total }}</td>
            <td>{{ $totalLlegadas > 0 ? round($s->total / $totalLlegadas * 100, 1) : 0 }}%</td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- Llegadas por Día --}}
<div class="section-title">Llegadas Diarias</div>
<table class="data-table">
    <thead>
        <tr>
            <th>Fecha</th>
            <th style="width: 40%;">Llegadas</th>
        </tr>
    </thead>
    <tbody>
    @foreach($llegadasPorDia as $d)
        <tr>
            <td>{{ \Carbon\Carbon::parse($d->fecha)->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') }}</td>
            <td>{{ $d->total }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- Pie de Página --}}
<div class="footer">
    <table>
        <tr>
            <td style="text-align: left; width: 60%;">SGP Chicamán &middot; Centro de Atención Permanente</td>
            <td style="text-align: right; width: 40%;">Uso interno &middot; {{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>
</div>

</body>
</html>
