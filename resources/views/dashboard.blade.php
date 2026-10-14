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

{{-- MODAL CREAR PACIENTE (idéntico al de ventanilla) --}}
<div class="modal fade" id="modalCrearPaciente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="ti ti-user-plus me-2"></i> Registrar Nuevo Paciente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pacientes.store') }}" method="POST" id="formCrearPacienteDashboard">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="nv_nombres">Nombres</label>
                            <input type="text" id="nv_nombres" name="nombres" class="form-control" required placeholder="Ej: María Mercedes">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="nv_apellidos">Apellidos</label>
                            <input type="text" id="nv_apellidos" name="apellidos" class="form-control" required placeholder="Ej: Gómez Pérez">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label required d-flex justify-content-between align-items-center mb-1" for="nv_nacimiento">
                                <span>Fecha Nacimiento</span>
                                <span id="nv_edad_badge" class="badge bg-blue-subtle text-primary fw-medium" style="display:none; font-size:0.75rem;"></span>
                            </label>
                            <input type="date" id="nv_nacimiento" name="fecha_nacimiento" class="form-control" max="{{ date('Y-m-d') }}" value="{{ old('fecha_nacimiento') }}" required>
                            <span id="nv_fecha_error" class="form-hint text-danger fw-bold mt-1" style="display:none;"></span>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="nv_sexo">Sexo</label>
                            <select id="nv_sexo" name="sexo" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                <option value="M">Masculino (M)</option>
                                <option value="F">Femenino (F)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="nv_dpi">DPI (13 dígitos)</label>
                            <input type="text" id="nv_dpi" name="dpi" class="form-control" inputmode="numeric" pattern="[0-9]*" maxlength="13" placeholder="Ej: 1987654320101">
                            <span id="msg-nv-dpi" class="form-hint fw-bold"></span>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nv_telefono">Teléfono</label>
                            <input type="text" id="nv_telefono" name="telefono" class="form-control" inputmode="numeric" pattern="[0-9]*" maxlength="8" placeholder="Ej: 55551234">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="nv_direccion">Dirección</label>
                            <input type="text" id="nv_direccion" name="direccion" class="form-control" maxlength="255" placeholder="Ej: Caserío El Centro, Sector 2">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label required" for="nv_numero_familia">Número de Familia</label>
                            {{-- Campo oculto para id si la familia existe --}}
                            <input type="hidden" id="nv_id_family" name="id_family">
                            <div class="position-relative">
                                <input type="text" id="nv_numero_familia" name="numero_familia"
                                       class="form-control" inputmode="numeric" pattern="[0-9]*" required autocomplete="off"
                                       placeholder="Ej: 115 (existente o nuevo)">
                                <div id="nv_familia_suggestions"
                                     style="display:none; position:absolute; top:100%; left:0; right:0;
                                            z-index:1080; background:#fff; border:1px solid #cbd5e1;
                                            border-radius:0 0 8px 8px; box-shadow:0 6px 18px rgba(0,0,0,.1);
                                            max-height:220px; overflow-y:auto;">
                                </div>
                            </div>
                            <span id="nv_familia_status" class="form-hint fw-bold mt-1 d-block"></span>
                        </div>
                    </div>
                    {{-- Datos de Ficha / Expediente Físico --}}
                    <h6 class="text-secondary border-bottom pb-2 mb-3" style="font-size: 0.9rem;">
                        Datos de Ficha / Expediente Físico
                    </h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="nv_numero_registro"># Ficha o Expediente</label>
                            <input type="text" id="nv_numero_registro" name="numero_registro" class="form-control" inputmode="numeric" pattern="[0-9]*" placeholder="Ej: 1024">
                            <span id="msg-nv-numero-registro" class="form-hint fw-bold"></span>
                            <span class="form-hint text-muted">Número de ficha o expediente físico</span>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="nv_descripcion_registro">Descripción / Ubicación del Registro</label>
                            <textarea id="nv_descripcion_registro" name="descripcion_registro" class="form-control auto-expand-textarea" rows="1" maxlength="150" placeholder="Ej: Archivero 3, Cajón B, Folder amarillo"></textarea>
                            <span class="form-hint">Máx. 150 caracteres</span>
                        </div>
                    </div>

                    {{-- Ubicación de nueva familia (se muestra dinámicamente si no existe la familia) --}}
                    <div id="nv_family_location_container" style="display: none;" class="card bg-light border-0 p-3 mb-3">
                        <h6 class="text-secondary border-bottom pb-1 mb-2" style="font-size: 0.85rem;">
                            <i class="ti ti-map-pin me-1 text-primary"></i> Ubicación del Nuevo Núcleo Familiar
                        </h6>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label required small mb-1" for="nv_id_depto">Departamento</label>
                                <select id="nv_id_depto" class="form-select form-select-sm">
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($departamentos as $dep)
                                        <option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required small mb-1" for="nv_id_muni">Municipio</label>
                                <select id="nv_id_muni" name="id_municipio" class="form-select form-select-sm" disabled>
                                    <option value="">-- Depto Primero --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required small mb-1" for="nv_id_comunidad">Comunidad</label>
                                <select id="nv_id_comunidad" name="id_comunidad" class="form-select form-select-sm" disabled>
                                    <option value="">-- Muni Primero --</option>
                                </select>
                                <div id="nv_new_comunidad_wrapper" style="display: none;" class="mt-2">
                                    <label class="form-label required small mb-1" for="nv_nueva_comunidad">Nombre de Nueva Comunidad</label>
                                    <input type="text" id="nv_nueva_comunidad" name="nueva_comunidad" class="form-control form-control-sm" placeholder="Nombre de nueva comunidad...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex flex-column-reverse flex-sm-row justify-content-sm-end gap-2">
                    <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary w-100 w-sm-auto">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Paciente
                    </button>
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

    //  Modal Nuevo Paciente: ubicación cascada (idéntico a ventanilla)
    function setupCascadingUbicaciones(deptoId, muniId, comId, wrapperId, inputId) {
        const deptoSelect = document.getElementById(deptoId);
        const muniSelect = document.getElementById(muniId);
        const comSelect = document.getElementById(comId);
        const wrapper = document.getElementById(wrapperId);
        const input = document.getElementById(inputId);

        if (deptoSelect && muniSelect && comSelect) {
            deptoSelect.addEventListener('change', function () {
                const val = this.value;
                muniSelect.innerHTML = '<option value="">-- Cargando... --</option>';
                muniSelect.disabled = true;
                comSelect.innerHTML = '<option value="">-- Depto Primero --</option>';
                comSelect.disabled = true;
                if (wrapper && input) {
                    wrapper.style.display = 'none';
                    input.required = false;
                    input.value = '';
                }
                if (!val) return;

                fetch(`/api/ubicaciones/municipios/${val}`)
                    .then(res => res.json())
                    .then(data => {
                        muniSelect.innerHTML = '<option value="">-- Seleccionar Municipio --</option>';
                        data.forEach(m => {
                            muniSelect.innerHTML += `<option value="${m.id_municipio}">${m.nombre}</option>`;
                        });
                        muniSelect.disabled = false;
                    });
            });
        }

        if (muniSelect) {
            muniSelect.addEventListener('change', function () {
                const val = this.value;
                comSelect.innerHTML = '<option value="">-- Cargando... --</option>';
                comSelect.disabled = true;
                if (wrapper && input) {
                    wrapper.style.display = 'none';
                    input.required = false;
                    input.value = '';
                }
                if (!val) return;

                fetch(`/api/ubicaciones/comunidades/${val}`)
                    .then(res => res.json())
                    .then(data => {
                        comSelect.innerHTML = '<option value="">-- Seleccionar Comunidad --</option>';
                        data.forEach(c => {
                            const zonaText = c.zona ? ` (${c.zona})` : '';
                            comSelect.innerHTML += `<option value="${c.id_comunidad}">${c.nombre}${zonaText}</option>`;
                        });
                        comSelect.innerHTML += '<option value="OTRO" style="font-weight:bold; color:var(--primary);">+ Registrar Nueva Comunidad</option>';
                        comSelect.disabled = false;
                    });
            });
        }

        if (comSelect) {
            comSelect.addEventListener('change', function () {
                if (wrapper && input) {
                    if (this.value === 'OTRO') {
                        wrapper.style.display = 'block';
                        input.required = true;
                        input.focus();
                    } else {
                        wrapper.style.display = 'none';
                        input.required = false;
                        input.value = '';
                    }
                }
            });
        }
    }

    setupCascadingUbicaciones('nv_id_depto', 'nv_id_muni', 'nv_id_comunidad', 'nv_new_comunidad_wrapper', 'nv_nueva_comunidad');

    // ── Cálculo dinámico de edad y validación de fecha ──
    function setupDateAndAgeHelper(inputId, badgeId, errorId) {
        const inputEl = document.getElementById(inputId);
        const badgeEl = document.getElementById(badgeId);
        const errorEl = document.getElementById(errorId);

        if (!inputEl) return;

        function updateAge() {
            const val = inputEl.value;
            if (!val) {
                if (badgeEl) {
                    badgeEl.style.display = 'none';
                    badgeEl.textContent = '';
                }
                if (errorEl) {
                    errorEl.style.display = 'none';
                    errorEl.textContent = '';
                }
                inputEl.classList.remove('is-invalid');
                return true;
            }

            const parts = val.split('-');
            if (parts.length !== 3) return false;

            const y = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10);
            const d = parseInt(parts[2], 10);

            const birthDate = new Date(y, m - 1, d);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (birthDate > today) {
                if (errorEl) {
                    errorEl.textContent = '⚠ La fecha de nacimiento no puede ser futura';
                    errorEl.style.display = 'block';
                }
                inputEl.classList.add('is-invalid');
                if (badgeEl) {
                    badgeEl.style.display = 'none';
                    badgeEl.textContent = '';
                }
                return false;
            }

            if (errorEl) {
                errorEl.textContent = '';
                errorEl.style.display = 'none';
            }
            inputEl.classList.remove('is-invalid');

            let years = today.getFullYear() - birthDate.getFullYear();
            let months = today.getMonth() - birthDate.getMonth();
            let days = today.getDate() - birthDate.getDate();

            if (days < 0) {
                months--;
                const prevMonthDays = new Date(today.getFullYear(), today.getMonth(), 0).getDate();
                days += prevMonthDays;
            }
            if (months < 0) {
                years--;
                months += 12;
            }

            let ageText = '';
            if (years >= 1) {
                ageText = years === 1 ? '1 año' : `${years} años`;
                if (years < 3 && months > 0) {
                    ageText += ` con ${months} ${months === 1 ? 'mes' : 'meses'}`;
                }
            } else if (months >= 1) {
                ageText = `${months} ${months === 1 ? 'mes' : 'meses'}`;
                if (days > 0) {
                    ageText += ` con ${days} ${days === 1 ? 'día' : 'días'}`;
                }
            } else {
                ageText = days <= 1 ? 'Recién nacido' : `${days} días`;
            }

            if (badgeEl) {
                badgeEl.textContent = 'Edad: ' + ageText;
                badgeEl.style.display = 'inline-block';
            }

            return true;
        }

        inputEl.addEventListener('input', updateAge);
        inputEl.addEventListener('change', updateAge);

        // Pre-cargar si ya tiene valor
        if (inputEl.value) {
            updateAge();
        }
    }

    setupDateAndAgeHelper('nv_nacimiento', 'nv_edad_badge', 'nv_fecha_error');


    // ── Modal Nuevo Paciente: autocompletado de familia (idéntico a ventanilla)
    const nvNumFamInput  = document.getElementById('nv_numero_familia');
    const nvIdFamInput   = document.getElementById('nv_id_family');
    const nvFamSug       = document.getElementById('nv_familia_suggestions');
    const nvFamStatus    = document.getElementById('nv_familia_status');
    const buscarFamPath  = '{{ route('api.familias.buscar') }}';
    
    const nvFamLocCont   = document.getElementById('nv_family_location_container');
    const nvDepto        = document.getElementById('nv_id_depto');
    const nvMuni         = document.getElementById('nv_id_muni');
    const nvComunidad    = document.getElementById('nv_id_comunidad');

    let famTimer;

    const showLocationFields = () => {
        if (nvFamLocCont) nvFamLocCont.style.display = 'block';
        if (nvDepto) nvDepto.setAttribute('required', 'required');
        if (nvMuni) nvMuni.setAttribute('required', 'required');
        if (nvComunidad) nvComunidad.setAttribute('required', 'required');
    };

    const hideLocationFields = () => {
        if (nvFamLocCont) nvFamLocCont.style.display = 'none';
        if (nvDepto) {
            nvDepto.removeAttribute('required');
            nvDepto.value = '';
        }
        if (nvMuni) {
            nvMuni.removeAttribute('required');
            nvMuni.innerHTML = '<option value="">-- Depto Primero --</option>';
            nvMuni.disabled = true;
        }
        if (nvComunidad) {
            nvComunidad.removeAttribute('required');
            nvComunidad.innerHTML = '<option value="">-- Muni Primero --</option>';
            nvComunidad.disabled = true;
        }
    };

    if (nvNumFamInput && nvFamSug) {
        document.addEventListener('click', e => {
            if (!nvNumFamInput.contains(e.target) && !nvFamSug.contains(e.target)) {
                nvFamSug.style.display = 'none';
            }
        });
        nvFamSug.addEventListener('click', e => e.stopPropagation());

        nvNumFamInput.addEventListener('input', function () {
            const q = this.value.trim();
            nvIdFamInput.value = '';

            if (!q) {
                nvFamSug.style.display = 'none';
                nvFamStatus.textContent = '';
                hideLocationFields();
                return;
            }

            clearTimeout(famTimer);
            famTimer = setTimeout(() => {
                fetch(`${buscarFamPath}?q=${encodeURIComponent(q)}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => renderFamilySuggestions(data, q));
            }, 200);
        });
    }

    function renderFamilySuggestions(data, q) {
        nvFamSug.innerHTML = '';

        if (!data.coincidencias.length) {
            nvIdFamInput.value = '';
            nvFamStatus.className = 'form-hint text-info fw-bold mt-1 d-block';
            nvFamStatus.innerHTML = `<i class="ti ti-plus-circle me-1"></i>Se creará la familia <strong>${q}</strong> automáticamente`;
            nvFamSug.style.display = 'none';
            showLocationFields();
            return;
        }

        data.coincidencias.forEach(f => {
            const isExacta = f.exacta;
            const item = document.createElement('div');
            item.className = 'px-3 py-2 border-bottom cursor-pointer';
            item.style.cssText = 'cursor:pointer; transition:background .12s';
            item.onmouseover = () => item.style.background = '#f1f5ff';
            item.onmouseout  = () => item.style.background = '';
            item.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <span class="badge ${isExacta ? 'bg-success' : 'bg-secondary-lt text-secondary'}">
                        ${isExacta ? '&#10003; Exacta' : 'Coincide'}
                    </span>
                    <div>
                        <strong>${f.numero_familia}</strong>
                        ${f.apellido_cabeza ? '&mdash; ' + f.apellido_cabeza : ''}
                        ${f.comunidad ? '<small class="text-muted ms-1">' + f.comunidad + '</small>' : ''}
                    </div>
                </div>`;
            item.addEventListener('click', () => {
                nvNumFamInput.value = f.numero_familia;
                nvIdFamInput.value  = f.id_family;
                nvFamSug.style.display = 'none';
                nvFamStatus.className = 'form-hint text-success fw-bold mt-1 d-block';
                nvFamStatus.innerHTML = `<i class="ti ti-check me-1"></i>Familia existente seleccionada (ID ${f.id_family})`;
                hideLocationFields();
            });
            nvFamSug.appendChild(item);
        });

        if (data.existe) {
            nvIdFamInput.value = data.id_family;
            nvFamStatus.className = 'form-hint text-success fw-bold mt-1 d-block';
            nvFamStatus.innerHTML = `<i class="ti ti-check me-1"></i>Familia existente &mdash; se vinculará automáticamente`;
            hideLocationFields();
        } else {
            nvIdFamInput.value = '';
            nvFamStatus.className = 'form-hint text-info fw-bold mt-1 d-block';
            nvFamStatus.innerHTML = `<i class="ti ti-plus-circle me-1"></i>Número no registrado &mdash; se creará la familia`;
            showLocationFields();
        }

        nvFamSug.style.display = 'block';
    }

    // ── Modal Nuevo Paciente: verificar duplicados (DPI y # Ficha o Expediente)
    const nvDpi = document.getElementById('nv_dpi');
    const nvDpiMsg = document.getElementById('msg-nv-dpi');
    const nvNumReg = document.getElementById('nv_numero_registro');
    const nvNumRegMsg = document.getElementById('msg-nv-numero-registro');
    const formCrearPaciente = document.getElementById('formCrearPacienteDashboard');

    let nvDpiDuplicado = false;
    let nvNumRegDuplicado = false;

    if (nvDpi && nvDpiMsg) {
        nvDpi.addEventListener('input', function() {
            if (nvDpiDuplicado) {
                nvDpiDuplicado = false;
                nvDpi.classList.remove('is-invalid');
                nvDpiMsg.textContent = '';
            }
        });

        nvDpi.addEventListener('blur', function () {
            const val = this.value.trim();
            if (!val) { 
                nvDpiMsg.textContent = ''; 
                nvDpiDuplicado = false;
                nvDpi.classList.remove('is-invalid');
                return; 
            }
            fetch(`/pacientes/verificar-duplicado?tipo=dpi&valor=${encodeURIComponent(val)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.duplicate) {
                        nvDpiDuplicado = true;
                        nvDpi.classList.add('is-invalid');
                        nvDpiMsg.className = 'form-hint text-danger fw-bold';
                        nvDpiMsg.textContent = '⚠ ' + data.message;
                    } else {
                        nvDpiDuplicado = false;
                        nvDpi.classList.remove('is-invalid');
                        nvDpiMsg.className = 'form-hint text-success fw-bold';
                        nvDpiMsg.textContent = '✓ Disponible';
                    }
                });
        });
    }

    if (nvNumReg && nvNumRegMsg) {
        nvNumReg.addEventListener('input', function() {
            if (nvNumRegDuplicado) {
                nvNumRegDuplicado = false;
                nvNumReg.classList.remove('is-invalid');
                nvNumRegMsg.textContent = '';
            }
        });

        nvNumReg.addEventListener('blur', function () {
            const val = this.value.trim();
            if (!val) { 
                nvNumRegMsg.textContent = ''; 
                nvNumRegDuplicado = false;
                nvNumReg.classList.remove('is-invalid');
                return; 
            }
            fetch(`/pacientes/verificar-duplicado?tipo=numero_registro&valor=${encodeURIComponent(val)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.duplicate) {
                        nvNumRegDuplicado = true;
                        nvNumReg.classList.add('is-invalid');
                        nvNumRegMsg.className = 'form-hint text-danger fw-bold';
                        nvNumRegMsg.textContent = '⚠ ' + data.message;
                    } else {
                        nvNumRegDuplicado = false;
                        nvNumReg.classList.remove('is-invalid');
                        nvNumRegMsg.className = 'form-hint text-success fw-bold';
                        nvNumRegMsg.textContent = '✓ Disponible';
                    }
                });
        });
    }

    if (formCrearPaciente) {
        formCrearPaciente.addEventListener('submit', function (e) {
            const hiddenNac = document.getElementById('nv_nacimiento');
            const fechaErr = document.getElementById('nv_fecha_error');
            if (hiddenNac && (!hiddenNac.value || (fechaErr && fechaErr.style.display !== 'none' && fechaErr.textContent))) {
                e.preventDefault();
                alert('Por favor verifique la fecha de nacimiento ingresada.');
                hiddenNac.focus();
                return false;
            }
            if (nvDpiDuplicado) {
                e.preventDefault();
                alert('No se puede registrar: El DPI ingresado ya está asignado a otro registro.');
                nvDpi.focus();
                return false;
            }
            if (nvNumRegDuplicado) {
                e.preventDefault();
                alert('No se puede registrar: El # de ficha o expediente ya está asignado a otro paciente.');
                nvNumReg.focus();
                return false;
            }
        });
    }
});
</script>
@endsection
