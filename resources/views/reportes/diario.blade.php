@extends('layouts.app')

@section('title', 'Reporte de Turno #' . $turno->id_turno)
@section('page_title', 'Reporte Diario — Turno #' . $turno->id_turno)

@section('content')
{{-- Encabezado del turno --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center g-3">
            <div class="col">
                <h2 class="mb-1">
                    @php
                        $bc = match($turno->tipo_turno) {
                            'matutino'   => 'warning',
                            'vespertino' => 'primary',
                            'nocturno'   => 'dark',
                            default      => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $bc }}-lt text-{{ $bc }} text-capitalize me-2 fs-5">
                        {{ $turno->tipo_turno }}
                    </span>
                    {{ $turno->fecha->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </h2>
                <div class="text-secondary">
                    <i class="ti ti-user me-1"></i> {{ $turno->usuario->nombre_completo }}
                    &nbsp;·&nbsp;
                    <i class="ti ti-clock me-1"></i>
                    {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}
                    – {{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}
                </div>
            </div>
            <div class="col-auto d-flex gap-2 flex-wrap">
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Volver
                </a>
                <a href="{{ route('reportes.pdf', $turno->id_turno) }}" class="btn btn-danger">
                    <i class="ti ti-file-type-pdf me-1"></i> Exportar PDF
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Tarjetas de resumen del turno --}}
<div class="row row-cards mb-4">
    <div class="col-sm-4">
        <div class="card card-sm text-center">
            <div class="card-body">
                <div class="text-primary fs-1 fw-bold">{{ $totalPacientes }}</div>
                <div class="text-secondary">Total Pacientes Atendidos</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-sm text-center">
            <div class="card-body">
                <div class="text-green fs-1 fw-bold">{{ $totalNuevos }}</div>
                <div class="text-secondary">Pacientes Nuevos</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-sm text-center">
            <div class="card-body">
                <div class="text-blue fs-1 fw-bold">{{ $totalRecurrentes }}</div>
                <div class="text-secondary">Pacientes Recurrentes</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabla de llegadas del turno --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-list me-2"></i> Listado de Llegadas
        </h3>
        <div class="card-options text-secondary small">
            Generado: {{ $reporte->generado_en->locale('es')->isoFormat('D/MM/YYYY HH:mm') }}
        </div>
    </div>
    @if($llegadas->isEmpty())
        <div class="card-body text-center text-secondary py-5">
            <i class="ti ti-users-off fs-2 d-block mb-2"></i>
            No se registraron llegadas en este turno.
        </div>
    @else
    <div class="table-responsive">
        <table class="table table-vcenter table-sm card-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Paciente</th>
                    <th>Expediente</th>
                    <th>DPI</th>
                    <th>Hora</th>
                    <th>Tipo</th>
                    <th>Familia</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
            @foreach($llegadas as $i => $reg)
                <tr>
                    <td class="text-secondary">{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-xs bg-{{ $reg->paciente->sexo === 'F' ? 'pink' : 'blue' }}-lt
                                  text-{{ $reg->paciente->sexo === 'F' ? 'pink' : 'blue' }} rounded-circle">
                                <i class="ti ti-user{{ $reg->paciente->sexo === 'F' ? '-female' : '' }}" style="font-size:.7rem"></i>
                            </span>
                            <div>
                                <div class="fw-medium small">
                                    {{ $reg->paciente->nombres }} {{ $reg->paciente->apellidos }}
                                </div>
                                <div class="text-secondary" style="font-size:.75rem">
                                    {{ $reg->paciente->sexo === 'F' ? 'Femenino' : 'Masculino' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-secondary small">{{ $reg->paciente->numero_expediente_fisico }}</td>
                    <td class="text-secondary small">{{ $reg->paciente->dpi ?: '—' }}</td>
                    <td>
                        <span class="fw-medium">{{ \Carbon\Carbon::parse($reg->hora_llegada)->format('H:i') }}</span>
                    </td>
                    <td>
                        @if($reg->es_nuevo)
                            <span class="badge bg-green-lt text-green">Nuevo</span>
                        @else
                            <span class="badge bg-secondary-lt text-secondary">Recurrente</span>
                        @endif
                    </td>
                    <td class="text-secondary small">
                        #{{ $reg->paciente->familia->numero_familia }}
                        {{ $reg->paciente->familia->apellido_cabeza }}
                    </td>
                    <td class="text-secondary small">{{ $reg->observaciones ?: '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
