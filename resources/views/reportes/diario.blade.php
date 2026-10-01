@extends('layouts.app')

@section('title', 'Reporte de Turno #' . $turno->id_turno)
@section('page_title', 'Reporte Diario — Turno #' . $turno->id_turno)

@section('content')
@php
    $bc = match($turno->tipo_turno) {
        'matutino'   => 'warning',
        'vespertino' => 'primary',
        'nocturno'   => 'dark',
        default      => 'secondary'
    };
    $isFromVentanilla = request('from') === 'ventanilla';
    $volverUrl = $isFromVentanilla 
        ? route('ventanilla.index', ['turno_id' => $turno->id_turno])
        : (url()->previous() && url()->previous() !== url()->current() ? url()->previous() : route('reportes.index'));
@endphp

{{-- Encabezado del turno --}}
<div class="card mb-3 mb-md-4 shadow-sm border-0">
    <div class="card-body p-3 p-md-4">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-{{ $bc }}-lt text-{{ $bc }} text-capitalize px-2 py-1" style="font-size: 0.78rem;">
                        <i class="ti ti-clock-hour-4 me-1"></i>Turno {{ $turno->tipo_turno }}
                    </span>
                    <span class="badge bg-secondary-lt text-secondary px-2 py-1" style="font-size: 0.78rem;">
                        #{{ $turno->id_turno }}
                    </span>
                </div>
                <h2 class="h3 fw-bold text-dark mb-1 lh-sm" style="font-size: clamp(1.15rem, 3.2vw, 1.55rem);">
                    {{ $turno->fecha->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </h2>
                <div class="text-secondary small d-flex flex-wrap align-items-center gap-2 gap-sm-3 mt-2">
                    <span class="d-inline-flex align-items-center">
                        <i class="ti ti-user-check me-1 text-primary"></i>
                        <strong class="me-1">Responsable:</strong> {{ $turno->nombre_responsable }}
                    </span>
                    <span class="d-none d-sm-inline text-muted">·</span>
                    <span class="d-inline-flex align-items-center">
                        <i class="ti ti-clock me-1 text-muted"></i>
                        {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }} – {{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}
                    </span>
                </div>
            </div>
            <div class="col-12 col-md-auto">
                <div class="d-flex gap-2 flex-wrap flex-sm-nowrap">
                    <a href="{{ $volverUrl }}" class="btn btn-outline-secondary flex-fill flex-md-grow-0">
                        <i class="ti ti-arrow-left me-1"></i>
                        {{ $isFromVentanilla ? 'Ventanilla' : 'Volver' }}
                    </a>
                    <button type="button" 
                            class="btn btn-primary flex-fill flex-md-grow-0 btn-preview-pdf"
                            data-preview-url="{{ route('reportes.pdf', ['turnoId' => $turno->id_turno, 'preview' => 1]) }}"
                            data-download-url="{{ route('reportes.pdf', $turno->id_turno) }}"
                            data-title="Reporte Diario — Turno #{{ $turno->id_turno }}">
                        <i class="ti ti-eye me-1"></i> Previsualizar
                    </button>
                    <a href="{{ route('reportes.pdf', $turno->id_turno) }}" class="btn btn-outline-danger flex-fill flex-md-grow-0" title="Descargar PDF">
                        <i class="ti ti-download me-1"></i> <span class="d-none d-sm-inline">Descargar</span> PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tarjetas de resumen del turno (compactas en móvil) --}}
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-4">
        <div class="card card-sm text-center h-100 shadow-sm border-0">
            <div class="card-body p-2 p-md-3">
                <div class="text-primary fs-2 fs-md-1 fw-bold lh-1 mb-1">{{ $totalPacientes }}</div>
                <div class="text-secondary small fw-medium" style="font-size: clamp(0.72rem, 2.2vw, 0.875rem);">
                    <span class="d-none d-sm-inline">Total Pacientes</span>
                    <span class="d-sm-none">Total</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card card-sm text-center h-100 shadow-sm border-0">
            <div class="card-body p-2 p-md-3">
                <div class="text-green fs-2 fs-md-1 fw-bold lh-1 mb-1">{{ $totalNuevos }}</div>
                <div class="text-secondary small fw-medium" style="font-size: clamp(0.72rem, 2.2vw, 0.875rem);">
                    <span class="d-none d-sm-inline">Pacientes Nuevos</span>
                    <span class="d-sm-none">Nuevos</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card card-sm text-center h-100 shadow-sm border-0">
            <div class="card-body p-2 p-md-3">
                <div class="text-blue fs-2 fs-md-1 fw-bold lh-1 mb-1">{{ $totalRecurrentes }}</div>
                <div class="text-secondary small fw-medium" style="font-size: clamp(0.72rem, 2.2vw, 0.875rem);">
                    <span class="d-none d-sm-inline">Pacientes Recurrentes</span>
                    <span class="d-sm-none">Recurrentes</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Listado de llegadas del turno --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
        <div class="d-flex align-items-center gap-2">
            <h3 class="card-title mb-0 fw-bold">
                <i class="ti ti-list me-1 text-primary"></i> Listado de Llegadas
            </h3>
            <span class="badge bg-blue-lt text-blue rounded-pill px-2">
                {{ $llegadas->count() }}
            </span>
        </div>
        <div class="text-secondary small">
            Generado: {{ $reporte->generado_en->locale('es')->isoFormat('D/MM/YYYY HH:mm') }}
        </div>
    </div>

    @if($llegadas->isEmpty())
        <div class="card-body text-center text-secondary py-5">
            <i class="ti ti-users fs-1 d-block mb-3 opacity-50"></i>
            <p class="mb-0">No se registraron llegadas en este turno.</p>
        </div>
    @else
        {{-- Vista Tabla Escritorio (md y superior) --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-vcenter table-sm card-table table-hover">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Paciente</th>
                        <th>Expediente</th>
                        <th>DPI</th>
                        <th>Hora</th>
                        <th>Condición</th>
                        <th>Familia</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($llegadas as $i => $reg)
                    <tr>
                        <td class="text-secondary fw-semibold">{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs bg-{{ $reg->paciente->sexo === 'F' ? 'pink' : 'blue' }}-lt
                                      text-{{ $reg->paciente->sexo === 'F' ? 'pink' : 'blue' }} rounded-circle">
                                    <i class="ti ti-user{{ $reg->paciente->sexo === 'F' ? '-female' : '' }}" style="font-size:.7rem"></i>
                                </span>
                                <div>
                                    <div class="fw-medium small text-dark">
                                        {{ $reg->paciente->nombres }} {{ $reg->paciente->apellidos }}
                                    </div>
                                    <div class="text-secondary" style="font-size:.75rem">
                                        {{ $reg->paciente->sexo === 'F' ? 'Femenino' : 'Masculino' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-secondary small">
                            <span class="badge bg-light text-dark font-monospace border">
                                {{ $reg->paciente->numero_expediente_fisico ?: '—' }}
                            </span>
                        </td>
                        <td class="text-secondary small font-monospace">{{ $reg->paciente->dpi ?: '—' }}</td>
                        <td>
                            <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($reg->hora_llegada)->format('H:i') }}</span>
                        </td>
                        <td>
                            @if($reg->es_nuevo)
                                <span class="badge bg-green-lt text-green">Nuevo</span>
                            @else
                                <span class="badge bg-blue-lt text-blue">Recurrente</span>
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

        {{-- Vista de Tarjetas para Móviles (d-md-none) --}}
        <div class="divide-y d-md-none">
            @foreach($llegadas as $i => $reg)
                <div class="p-3">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <span class="avatar avatar-sm bg-{{ $reg->paciente->sexo === 'F' ? 'pink' : 'blue' }}-lt
                                  text-{{ $reg->paciente->sexo === 'F' ? 'pink' : 'blue' }} rounded-circle flex-shrink-0">
                                <i class="ti ti-user{{ $reg->paciente->sexo === 'F' ? '-female' : '' }}"></i>
                            </span>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">
                                    {{ $reg->paciente->nombres }} {{ $reg->paciente->apellidos }}
                                </div>
                                <div class="text-secondary small text-truncate" style="font-size: 0.78rem;">
                                    {{ $reg->paciente->sexo === 'F' ? 'Femenino' : 'Masculino' }}
                                    · Fam. #{{ $reg->paciente->familia->numero_familia }} ({{ $reg->paciente->familia->apellido_cabeza }})
                                </div>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0 d-flex flex-column align-items-end gap-1">
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                <i class="ti ti-clock me-1 text-muted"></i>{{ \Carbon\Carbon::parse($reg->hora_llegada)->format('H:i') }}
                            </div>
                            @if($reg->es_nuevo)
                                <span class="badge bg-green-lt text-green py-0 px-2" style="font-size: 0.7rem;">Nuevo</span>
                            @else
                                <span class="badge bg-blue-lt text-blue py-0 px-2" style="font-size: 0.7rem;">Recurrente</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-light rounded p-2 small mt-2 d-flex flex-wrap gap-x-3 gap-y-1 align-items-center justify-content-between text-secondary">
                        <div>
                            <span class="text-muted">Exp:</span>
                            <strong class="text-primary font-monospace">{{ $reg->paciente->numero_expediente_fisico ?: 'Sin exp.' }}</strong>
                        </div>
                        <div>
                            <span class="text-muted">DPI:</span>
                            <span class="font-monospace">{{ $reg->paciente->dpi ?: '—' }}</span>
                        </div>
                        <div>
                            <span class="badge bg-secondary-lt" style="font-size: 0.68rem;">#{{ $i + 1 }} de turno</span>
                        </div>
                    </div>

                    @if($reg->observaciones)
                        <div class="mt-2 text-secondary small fst-italic ps-2 border-start border-2 border-primary-subtle">
                            <strong class="text-muted fst-normal">Obs:</strong> {{ $reg->observaciones }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Modal para previsualización en pantalla del PDF --}}
@include('reportes.preview-modal')

@endsection
