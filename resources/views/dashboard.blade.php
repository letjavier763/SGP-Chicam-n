@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Panel de Control General')

@section('content')
{{-- ── Tarjetas de estadísticas ─────────────────────────── --}}
<div class="row row-cards mb-4">
    {{-- Total Pacientes --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-primary-lt rounded">
                            <i class="ti ti-users fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium fs-2 text-dark">{{ $totalPacientes }}</div>
                        <div class="text-secondary">Pacientes Registrados</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Llegadas de Hoy --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-green-lt rounded">
                            <i class="ti ti-calendar-event fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium fs-2 text-dark">{{ $llegadasHoy }}</div>
                        <div class="text-secondary">Llegadas de Hoy</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertas de Duplicidad --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-danger-lt rounded">
                            <i class="ti ti-alert-triangle fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium fs-2 text-dark">{{ $alertasDuplicado }}</div>
                        <div class="text-secondary">Alertas de Duplicidad</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Turno de Hoy --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        @if($turnoHoy)
                        <span class="avatar bg-warning-lt rounded">
                            <i class="ti ti-clock fs-2"></i>
                        </span>
                        @else
                        <span class="avatar bg-secondary-lt rounded">
                            <i class="ti ti-clock-off fs-2"></i>
                        </span>
                        @endif
                    </div>
                    <div class="col">
                        @if($turnoHoy)
                            <div class="font-weight-medium text-dark text-capitalize">
                                {{ $turnoHoy->tipo_turno }}
                            </div>
                            <div class="text-secondary small">
                                {{ \Carbon\Carbon::parse($turnoHoy->hora_inicio)->format('H:i') }}
                                – {{ \Carbon\Carbon::parse($turnoHoy->hora_fin)->format('H:i') }}
                            </div>
                        @else
                            <div class="font-weight-medium text-secondary">Sin turno</div>
                            <div class="text-secondary small">No asignado hoy</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Panel de Accesos Rápidos ────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title text-primary">
            <i class="ti ti-keyframe me-2"></i> Accesos Rápidos del Sistema
        </h3>
    </div>
    <div class="card-body">
        <p class="text-secondary">
            Bienvenido, <strong>{{ Auth::user()->nombre_completo }}</strong>.
            Seleccione una de las siguientes opciones para comenzar su gestión diaria en el CAP Chicamán.
        </p>

        {{-- Ventanilla --}}
        @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
        <h4 class="text-uppercase text-secondary small fw-bold mt-4 mb-2">
            <i class="ti ti-door-enter me-1"></i> Ventanilla
        </h4>
        <div class="d-flex flex-wrap gap-2 mb-3">
            @if($turnoHoy)
            <a href="{{ route('ventanilla.index', ['turno_id' => $turnoHoy->id_turno]) }}"
               class="btn btn-primary">
                <i class="ti ti-door-enter me-1"></i> Ir a mi Ventanilla (Turno {{ ucfirst($turnoHoy->tipo_turno) }})
            </a>
            @else
            <a href="{{ route('ventanilla.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-door-enter me-1"></i> Ir a Ventanilla
            </a>
            @endif
            <a href="{{ route('turnos.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-clock me-1"></i> Ver Turnos
            </a>
            @if(Auth::user()->esAdministrador())
            <a href="{{ route('turnos.create') }}" class="btn btn-outline-secondary">
                <i class="ti ti-plus me-1"></i> Nuevo Turno
            </a>
            @endif
        </div>
        @endif

        {{-- Registros --}}
        @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
        <h4 class="text-uppercase text-secondary small fw-bold mt-3 mb-2">
            <i class="ti ti-users me-1"></i> Registros
        </h4>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('pacientes.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-users me-1"></i> Pacientes
            </a>
            <a href="{{ route('pacientes.create') }}" class="btn btn-outline-primary">
                <i class="ti ti-user-plus me-1"></i> Nuevo Paciente
            </a>
            <a href="{{ route('familias.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-home-heart me-1"></i> Núcleos Familiares
            </a>
        </div>
        @endif

        {{-- Reportes --}}
        @if(Auth::user()->esAdministrador() || Auth::user()->esDirector())
        <h4 class="text-uppercase text-secondary small fw-bold mt-3 mb-2">
            <i class="ti ti-chart-bar me-1"></i> Reportería
        </h4>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-success">
                <i class="ti ti-chart-line me-1"></i> Estadísticas y Reportes
            </a>
        </div>
        @endif

        {{-- Administración --}}
        @if(Auth::user()->esAdministrador())
        <h4 class="text-uppercase text-secondary small fw-bold mt-3 mb-2">
            <i class="ti ti-shield me-1"></i> Administración
        </h4>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('alertas.index') }}" class="btn btn-outline-danger">
                <i class="ti ti-alert-circle me-1"></i> Monitorear Alertas
            </a>
            <a href="{{ route('bitacora.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-shield-check me-1"></i> Bitácora
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

