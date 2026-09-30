@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Panel de Control')

@section('content')

{{-- ══════════════════════════════════════════════════════
     HERO MÓVIL — Solo visible en teléfonos (< 768px)
     ══════════════════════════════════════════════════════ --}}
<div class="d-md-none mb-3">

    {{-- Tarjeta de turno actual --}}
    <div class="mob-turno-card mb-3">
        <div class="mob-turno-top">
            <div class="mob-turno-icon">
                <i class="ti ti-clock"></i>
            </div>
            <div class="mob-turno-info">
                <div class="mob-turno-label">
                    TURNO ACTUAL
                    @if($turnoHoy)
                        <span class="mob-badge-activo">Activo</span>
                    @else
                        <span class="mob-badge-inactivo">Sin turno</span>
                    @endif
                </div>
                <div class="mob-turno-nombre">
                    @if($turnoHoy)
                        Turno del día
                        <div class="mob-turno-responsable text-truncate mt-1" style="font-size: 0.85rem; font-weight: 500; color: #e2e8f0; display: flex; align-items: center; gap: 5px;">
                            <i class="ti ti-user text-warning" style="font-size: 0.95rem;"></i>
                            <span>{{ $turnoHoy->nombre_responsable }}</span>
                        </div>
                    @else
                        No asignado hoy
                        <div class="mt-1" style="font-size: 0.8rem; font-weight: 400; color: #94a3b8;">
                            Sin personal asignado
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="mob-turno-bottom">
            <span class="mob-turno-fecha">
                <i class="ti ti-calendar me-1"></i>
                {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM YYYY') }}
            </span>
            <span class="mob-turno-modo">Modo&nbsp; CAP-Ventanilla</span>
        </div>
    </div>

    {{-- Encabezado de sección --}}
    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <h2 class="mob-panel-title">Resumen de Hoy</h2>
        <span class="mob-panel-updated">
            Actualizado {{ now()->format('H:i') }}
        </span>
    </div>

    {{-- Tarjetas de métricas --}}
    <div class="mob-metrics-grid">

        {{-- Pacientes --}}
        <div class="mob-metric-card">
            <div class="mob-metric-header">
                <span class="mob-metric-label">Pacientes Reg.</span>
                <span class="mob-metric-icon mob-icon-primary">
                    <i class="ti ti-users"></i>
                </span>
            </div>
            <div class="mob-metric-value">{{ $totalPacientes }}</div>
        </div>

        {{-- Familias --}}
        <div class="mob-metric-card">
            <div class="mob-metric-header">
                <span class="mob-metric-label">Familias Reg.</span>
                <span class="mob-metric-icon mob-icon-warning">
                    <i class="ti ti-users-group"></i>
                </span>
            </div>
            <div class="mob-metric-value">{{ $familias->count() }}</div>
        </div>

        {{-- Llegadas hoy (destacado ancho) --}}
        <div class="mob-metric-card" style="grid-column: span 2;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="mob-metric-label d-block mb-1">Llegadas de Hoy</span>
                    <div class="mob-metric-value">{{ $llegadasHoy }}</div>
                </div>
                <span class="mob-metric-icon mob-icon-success" style="width: 38px; height: 38px; font-size: 1.15rem;">
                    <i class="ti ti-calendar-event"></i>
                </span>
            </div>
        </div>

    </div>
</div>

{{-- ── Tarjetas de estadísticas (Desktop) ─────────────────── --}}
<div class="row row-cards mb-4 d-none d-md-flex">
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

    {{-- Núcleos Familiares --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-warning-lt rounded">
                            <i class="ti ti-users-group fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium fs-2 text-dark">{{ $familias->count() }}</div>
                        <div class="text-secondary">Núcleos Familiares</div>
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
                            <div class="font-weight-medium text-dark">
                                Turno del día
                            </div>
                            <div class="text-secondary small">
                                {{ $turnoHoy->nombre_responsable }}
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

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ti ti-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Atención: Revise los siguientes errores:</strong>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── Panel de Acciones Rápidas ────────────────────────── --}}
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3">
        <h3 class="card-title fw-bold mb-0" style="color: #0b1c30;">
            Acciones Rápidas
        </h3>
        <span class="text-secondary small d-none d-sm-inline">Operación diaria</span>
    </div>
    <div class="card-body">
        <div class="row g-2 g-sm-3">
            {{-- Acción 1: Ir a Ventanilla (Color Azul de Ventanilla) --}}
            @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
                <div class="col-12 col-md-6">
                    <a href="{{ $turnoHoy ? route('ventanilla.index', ['turno_id' => $turnoHoy->id_turno]) : route('ventanilla.index') }}" 
                       class="quick-action-tile quick-action-blue">
                        <div class="quick-action-icon bg-blue-subtle">
                            <i class="ti ti-building-hospital"></i>
                        </div>
                        <div class="quick-action-info">
                            <span class="quick-action-title">Ir a mi Ventanilla</span>
                            <span class="quick-action-subtitle">
                                @if($turnoHoy)
                                    Turno: {{ $turnoHoy->nombre_responsable }}
                                @else
                                    Recepción y control de llegadas
                                @endif
                            </span>
                        </div>
                        <div class="quick-action-badge">
                            @if($turnoHoy)
                                <span class="nav-badge-pill">Activo</span>
                            @else
                                <i class="ti ti-chevron-right quick-action-arrow"></i>
                            @endif
                        </div>
                    </a>
                </div>

                {{-- Acción 2: Nuevo Paciente (Color Púrpura de Pacientes) --}}
                <div class="col-12 col-md-6">
                    <button type="button" class="quick-action-tile quick-action-purple w-100 text-start" data-bs-toggle="modal" data-bs-target="#modalCrearPaciente">
                        <div class="quick-action-icon bg-purple-subtle">
                            <i class="ti ti-user-plus"></i>
                        </div>
                        <div class="quick-action-info">
                            <span class="quick-action-title">Registrar Paciente</span>
                            <span class="quick-action-subtitle">Nuevo expediente clínico</span>
                        </div>
                        <div class="quick-action-badge">
                            <span class="badge-purple-pill">+ Nuevo</span>
                        </div>
                    </button>
                </div>
            @endif

            {{-- Acción 3: Crear turno si no hay turno asignado hoy y es administrador (Color Ámbar de Turnos) --}}
            @if(Auth::user()->esAdministrador() && !$turnoHoy)
                <div class="col-12 col-md-6">
                    <button type="button" class="quick-action-tile quick-action-amber w-100 text-start" data-bs-toggle="modal" data-bs-target="#modalCrearTurno">
                        <div class="quick-action-icon bg-amber-subtle">
                            <i class="ti ti-clock-plus"></i>
                        </div>
                        <div class="quick-action-info">
                            <span class="quick-action-title">Asignar Turno</span>
                            <span class="quick-action-subtitle">Sin turno asignado hoy</span>
                        </div>
                        <div class="quick-action-badge">
                            <i class="ti ti-chevron-right quick-action-arrow"></i>
                        </div>
                    </button>
                </div>
            @endif

            {{-- Para Director: Acceso directo a reportes (Color Teal de Reportes) --}}
            @if(Auth::user()->esDirector())
                <div class="col-12 col-md-6">
                    <a href="{{ route('reportes.index') }}" class="quick-action-tile quick-action-teal">
                        <div class="quick-action-icon bg-teal-subtle">
                            <i class="ti ti-chart-bar"></i>
                        </div>
                        <div class="quick-action-info">
                            <span class="quick-action-title">Ver Reportes</span>
                            <span class="quick-action-subtitle">Estadísticas y métricas del CAP</span>
                        </div>
                        <div class="quick-action-badge">
                            <i class="ti ti-chevron-right quick-action-arrow"></i>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL CREAR TURNO --}}
@if(Auth::user()->esAdministrador())
<div class="modal fade" id="modalCrearTurno" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="ti ti-clock-plus me-2"></i> Crear Nuevo Turno</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('turnos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="create_turno_fecha" class="form-label required">Fecha del Turno</label>
                            <input type="date" id="create_turno_fecha" name="fecha" class="form-control" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="col-12">
                            <label for="create_turno_obs" class="form-label">Observaciones</label>
                            <textarea id="create_turno_obs" name="observaciones" class="form-control" rows="3" placeholder="Notas adicionales sobre el turno…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Guardar Turno</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- MODAL CREAR PACIENTE --}}
<div class="modal fade" id="modalCrearPaciente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="ti ti-user-plus me-2"></i> Registrar Nuevo Paciente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pacientes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <h6 class="text-secondary border-bottom pb-2 mb-3">1. Adscripción al Núcleo Familiar</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label required" for="create_id_family">Núcleo Familiar</label>
                            <select id="create_id_family" name="id_family" class="form-select" required>
                                <option value="">-- Seleccione un núcleo familiar --</option>
                                @foreach($familias as $fam)
                                    <option value="{{ $fam->id_family }}" data-numero-familia="{{ $fam->numero_familia }}">
                                        No. Familia: {{ $fam->numero_familia }} — Cabeza: {{ $fam->apellido_cabeza }} ({{ $fam->comunidad->nombre ?? 'Sin comunidad' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3">2. Información Personal del Paciente</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="create_nombres">Nombres</label>
                            <input type="text" id="create_nombres" name="nombres" class="form-control" required placeholder="Ej: María Mercedes">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="create_apellidos">Apellidos</label>
                            <input type="text" id="create_apellidos" name="apellidos" class="form-control" required placeholder="Ej: Gómez Pérez">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label required" for="create_fecha_nacimiento">Fecha Nacimiento</label>
                            <input type="date" id="create_fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="create_sexo">Sexo</label>
                            <select id="create_sexo" name="sexo" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                <option value="M">Masculino (M)</option>
                                <option value="F">Femenino (F)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="create_dpi">DPI (13 dígitos)</label>
                            <input type="text" id="create_dpi" name="dpi" class="form-control" maxlength="13" placeholder="Ej: 1987654320101">
                            <span id="msg-dup-dpi" class="form-hint fw-bold"></span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="create_telefono">Teléfono de Contacto (8 dígitos)</label>
                            <input type="text" id="create_telefono" name="telefono" class="form-control" maxlength="8" placeholder="Ej: 55551234">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Guardar Paciente</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Autocompletado de horas de turno
    const createTipoSelect = document.getElementById('create_tipo_turno');
    if (createTipoSelect) {
        createTipoSelect.addEventListener('change', function () {
            const horarios = {
                matutino:   { inicio: '06:00', fin: '14:00' },
                vespertino: { inicio: '14:00', fin: '22:00' },
                nocturno:   { inicio: '22:00', fin: '06:00' },
            };
            const sel = horarios[this.value];
            if (sel) {
                document.getElementById('create_hora_inicio').value = sel.inicio;
                document.getElementById('create_hora_fin').value    = sel.fin;
            }
        });
    }


    // Validación de DPI en tiempo real
    const dpiInput = document.getElementById('create_dpi');
    const msgEl = document.getElementById('msg-dup-dpi');
    if (dpiInput) {
        dpiInput.addEventListener('blur', function () {
            const val = this.value.trim();
            if (!val) {
                msgEl.textContent = '';
                return;
            }

            fetch(`/pacientes/verificar-duplicado?tipo=dpi&valor=${encodeURIComponent(val)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.duplicate) {
                        msgEl.className = 'form-hint text-danger fw-bold';
                        msgEl.textContent = '⚠ ' + data.message;
                    } else {
                        msgEl.className = 'form-hint text-success fw-bold';
                        msgEl.textContent = '✓ Disponible';
                    }
                });
        });
    }
});
</script>
@endsection
