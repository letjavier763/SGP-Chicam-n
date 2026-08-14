<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte Turno #{{ $turno->id_turno }} — CAP Chicamán</title>
<style>
    @page { margin: 18mm 15mm; size: letter portrait; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1a1a2e; line-height: 1.4; }

    /* Encabezado */
    .header { border-bottom: 3px solid #206bc4; padding-bottom: 10px; margin-bottom: 14px; }
    .header-title { font-size: 16px; font-weight: bold; color: #206bc4; }
    .header-sub { font-size: 11px; color: #555; margin-top: 2px; }
    .header-meta { font-size: 9px; color: #888; text-align: right; }

    /* Tarjetas de resumen */
    .stats-grid { display: table; width: 100%; margin-bottom: 14px; border-collapse: separate; border-spacing: 6px; }
    .stat-box { display: table-cell; width: 33.33%; background: #f0f4ff; border-left: 4px solid #206bc4;
                padding: 10px 12px; border-radius: 4px; }
    .stat-box.green { background: #f0fff4; border-color: #2fb344; }
    .stat-box.blue  { background: #e8f4ff; border-color: #4dabf7; }
    .stat-num { font-size: 22px; font-weight: bold; color: #206bc4; }
    .stat-box.green .stat-num { color: #2fb344; }
    .stat-box.blue  .stat-num { color: #1971c2; }
    .stat-label { font-size: 9px; color: #555; margin-top: 2px; }

    /* Tabla */
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    thead tr { background: #206bc4; color: #fff; }
    thead th { padding: 6px 8px; font-size: 9px; font-weight: bold; text-align: left; }
    tbody tr:nth-child(even) { background: #f7f9ff; }
    tbody td { padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    .badge { padding: 2px 6px; border-radius: 20px; font-size: 8px; font-weight: bold; }
    .badge-green { background: #d3f9d8; color: #2f9e44; }
    .badge-gray  { background: #f1f3f5; color: #868e96; }

    /* Pie de página */
    .footer { margin-top: 18px; border-top: 1px solid #ccc; padding-top: 8px; font-size: 8px; color: #888;
              display: table; width: 100%; }
    .footer-left  { display: table-cell; }
    .footer-right { display: table-cell; text-align: right; }
</style>
</head>
<body>

{{-- Encabezado institucional --}}
<div class="header">
    <table style="width:100%; border:none">
        <tr>
            <td>
                <div class="header-title">Centro de Atención Permanente — CAP Chicamán</div>
                <div class="header-sub">
                    Reporte Diario de Turno #{{ $turno->id_turno }}
                    — {{ ucfirst($turno->tipo_turno) }}
                </div>
            </td>
            <td style="text-align:right; vertical-align:top">
                <div class="header-meta">
                    Fecha: {{ $turno->fecha->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}<br>
                    Horario: {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}
                    – {{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}<br>
                    Personal: {{ $turno->usuario->nombre_completo }}<br>
                    Generado: {{ now()->format('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- Estadísticas de resumen --}}
<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-num">{{ $totalPacientes }}</div>
        <div class="stat-label">Total Pacientes Atendidos</div>
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

{{-- Tabla de llegadas --}}
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Paciente</th>
            <th>Sexo</th>
            <th>Expediente</th>
            <th>DPI</th>
            <th>Hora Llegada</th>
            <th>Tipo</th>
            <th>Familia</th>
        </tr>
    </thead>
    <tbody>
    @forelse($llegadas as $i => $reg)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $reg->paciente->nombres }} {{ $reg->paciente->apellidos }}</td>
            <td>{{ $reg->paciente->sexo === 'F' ? 'Femenino' : 'Masculino' }}</td>
            <td>{{ $reg->paciente->numero_expediente_fisico }}</td>
            <td>{{ $reg->paciente->dpi ?: '—' }}</td>
            <td>{{ \Carbon\Carbon::parse($reg->hora_llegada)->format('H:i') }}</td>
            <td>
                @if($reg->es_nuevo)
                    <span class="badge badge-green">Nuevo</span>
                @else
                    <span class="badge badge-gray">Recurrente</span>
                @endif
            </td>
            <td>#{{ $reg->paciente->familia->numero_familia }} {{ $reg->paciente->familia->apellido_cabeza }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="text-align:center; padding:14px; color:#888;">
                No se registraron llegadas en este turno.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

{{-- Pie de página --}}
<div class="footer">
    <div class="footer-left">SGP Chicamán — Sistema de Gestión de Pacientes</div>
    <div class="footer-right">Reporte confidencial — uso interno</div>
</div>

</body>
</html>
