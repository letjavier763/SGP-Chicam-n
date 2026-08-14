<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Estadísticas {{ $fechaDesde }} al {{ $fechaHasta }} — CAP Chicamán</title>
<style>
    @page { margin: 18mm 15mm; size: letter portrait; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1a1a2e; line-height: 1.4; }

    .header { border-bottom: 3px solid #206bc4; padding-bottom: 10px; margin-bottom: 14px; }
    .header-title { font-size: 16px; font-weight: bold; color: #206bc4; }
    .header-sub { font-size: 11px; color: #555; margin-top: 2px; }
    .header-meta { font-size: 9px; color: #888; text-align: right; }

    .stats-grid { display: table; width: 100%; margin-bottom: 14px; border-collapse: separate; border-spacing: 6px; }
    .stat-box { display: table-cell; width: 33.33%; background: #f0f4ff; border-left: 4px solid #206bc4;
                padding: 10px 12px; border-radius: 4px; }
    .stat-box.green { background: #f0fff4; border-color: #2fb344; }
    .stat-box.blue  { background: #e8f4ff; border-color: #4dabf7; }
    .stat-num { font-size: 22px; font-weight: bold; color: #206bc4; }
    .stat-box.green .stat-num { color: #2fb344; }
    .stat-box.blue  .stat-num { color: #1971c2; }
    .stat-label { font-size: 9px; color: #555; margin-top: 2px; }

    h3.section { font-size: 11px; color: #206bc4; margin: 14px 0 6px; border-bottom: 1px solid #cce; padding-bottom: 3px; }

    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #206bc4; color: #fff; }
    thead th { padding: 6px 8px; font-size: 9px; font-weight: bold; text-align: left; }
    tbody tr:nth-child(even) { background: #f7f9ff; }
    tbody td { padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #e2e8f0; }

    .footer { margin-top: 18px; border-top: 1px solid #ccc; padding-top: 8px; font-size: 8px; color: #888;
              display: table; width: 100%; }
    .footer-left  { display: table-cell; }
    .footer-right { display: table-cell; text-align: right; }
</style>
</head>
<body>

<div class="header">
    <table style="width:100%; border:none">
        <tr>
            <td>
                <div class="header-title">Centro de Atención Permanente — CAP Chicamán</div>
                <div class="header-sub">Estadísticas del período: {{ $fechaDesde }} al {{ $fechaHasta }}</div>
            </td>
            <td style="text-align:right; vertical-align:top">
                <div class="header-meta">Generado: {{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-num">{{ $totalLlegadas }}</div>
        <div class="stat-label">Total Llegadas en el Período</div>
    </div>
    <div class="stat-box green">
        <div class="stat-num">{{ $totalNuevos }}</div>
        <div class="stat-label">Pacientes Nuevos</div>
    </div>
    <div class="stat-box blue">
        <div class="stat-num">{{ $totalRecurrentes }}</div>
        <div class="stat-label">Pacientes Recurrentes</div>
    </div>
</div>

<h3 class="section">Distribución por Sexo</h3>
<table>
    <thead><tr><th>Sexo</th><th>Total Atenciones</th><th>Porcentaje</th></tr></thead>
    <tbody>
    @foreach($porSexo as $s)
        <tr>
            <td>{{ $s->sexo === 'F' ? 'Femenino' : 'Masculino' }}</td>
            <td>{{ $s->total }}</td>
            <td>{{ $totalLlegadas > 0 ? round($s->total / $totalLlegadas * 100, 1) : 0 }}%</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h3 class="section">Llegadas por Día</h3>
<table>
    <thead><tr><th>Fecha</th><th>Total Llegadas</th></tr></thead>
    <tbody>
    @foreach($llegadasPorDia as $d)
        <tr>
            <td>{{ \Carbon\Carbon::parse($d->fecha)->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') }}</td>
            <td>{{ $d->total }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">
    <div class="footer-left">SGP Chicamán — Sistema de Gestión de Pacientes</div>
    <div class="footer-right">Reporte confidencial — uso interno</div>
</div>

</body>
</html>
