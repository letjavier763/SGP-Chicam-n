<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte Turno #{{ $turno->id_turno }} — CAP Chicamán</title>
@include('reportes.pdf._base')
<style>
    /* Estilos específicos del reporte diario */
    .paciente-nombre { font-weight: 700; color: #0f172a; }
    .paciente-obs    { font-size: 6.2pt; color: #64748b; margin-top: 1px; }
</style>
</head>
<body>

{{-- Encabezado --}}
<table class="header">
    <tr>
        <td style="width: 60%;">
            <div class="inst-title">Centro de Atención Permanente — CAP Chicamán</div>
            <div class="inst-sub">Reporte Diario de Turno #{{ $turno->id_turno }}</div>
        </td>
        <td style="width: 40%;" class="header-meta">
            <div><strong>Fecha:</strong> {{ $turno->fecha->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</div>
            <div><strong>Horario:</strong> {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }} – {{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }} hrs</div>
            <div><strong>Responsable:</strong> {{ $turno->nombre_responsable }}</div>
            <div><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</div>
        </td>
    </tr>
</table>

{{-- Resumen --}}
<table class="stats-table">
    <tr>
        <td class="stat-card primary" style="width: 33.33%;">
            <span class="stat-num">{{ $totalPacientes }}</span>
            <span class="stat-label">Total Atendidos</span>
        </td>
        <td class="stat-card success" style="width: 33.33%;">
            <span class="stat-num">{{ $totalNuevos }}</span>
            <span class="stat-label">Pacientes Nuevos</span>
        </td>
        <td class="stat-card neutral" style="width: 33.33%;">
            <span class="stat-num">{{ $totalRecurrentes }}</span>
            <span class="stat-label">Recurrentes</span>
        </td>
    </tr>
</table>

{{-- Listado --}}
<div class="section-title">
    Listado de Llegadas <span>({{ $llegadas->count() }} {{ $llegadas->count() === 1 ? 'paciente' : 'pacientes' }})</span>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th class="center" style="width: 4%;">#</th>
            <th style="width: 27%;">Paciente</th>
            <th class="center" style="width: 9%;">Sexo</th>
            <th class="center" style="width: 12%;">Expediente</th>
            <th class="center" style="width: 14%;">DPI / CUI</th>
            <th class="center" style="width: 8%;">Hora</th>
            <th class="center" style="width: 10%;">Condición</th>
            <th style="width: 16%;">Familia</th>
        </tr>
    </thead>
    <tbody>
    @forelse($llegadas as $i => $reg)
        <tr>
            <td class="center text-muted text-bold">{{ $i + 1 }}</td>
            <td>
                <div class="paciente-nombre">{{ $reg->paciente->nombres }} {{ $reg->paciente->apellidos }}</div>
                @if($reg->observaciones)
                    <div class="paciente-obs">Obs: {{ $reg->observaciones }}</div>
                @endif
            </td>
            <td class="center text-muted">
                {{ $reg->paciente->sexo === 'F' ? 'Femenino' : 'Masculino' }}
            </td>
            <td class="center">{{ $reg->paciente->numero_expediente_fisico ?: '—' }}</td>
            <td class="center">{{ $reg->paciente->dpi ?: '—' }}</td>
            <td class="center text-bold">
                {{ \Carbon\Carbon::parse($reg->hora_llegada)->format('H:i') }}
            </td>
            <td class="center">
                @if($reg->es_nuevo)
                    <span class="badge badge-nuevo">Nuevo</span>
                @else
                    <span class="badge badge-recurrente">Recurrente</span>
                @endif
            </td>
            <td class="text-muted">
                Fam. #{{ $reg->paciente->familia->numero_familia }} ({{ $reg->paciente->familia->apellido_cabeza }})
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="text-align: center; padding: 18px; color: #94a3b8; font-style: italic;">
                No se registraron llegadas en este turno.
            </td>
        </tr>
    @endforelse
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
