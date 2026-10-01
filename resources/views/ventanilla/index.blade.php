@extends('layouts.app')

@section('title', 'Ventanilla')
@section('page_title', 'Módulo de Ventanilla — Registro de Llegadas')

@section('content')

{{-- Alertas de sesión --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="ti ti-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <i class="ti ti-alert-circle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <div class="d-flex">
        <div><i class="ti ti-alert-triangle me-2 fs-2"></i></div>
        <div>
            <strong>Atención: No se pudo completar el registro:</strong>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">

    {{-- ============================================================
        Selección de turno y Registro
    ============================================================ --}}
    <div class="col-lg-5">

        {{-- Barra del turno del día --}}
        @if($turnosHoy->isEmpty())
            <div class="card mb-2 border-warning-subtle shadow-none bg-warning-lt">
                <div class="card-body p-2 p-md-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="ti ti-user-plus text-warning fs-3"></i>
                        <div>
                            <div class="fw-bold text-dark small">Sin turno activo hoy</div>
                            <div class="text-secondary" style="font-size: 0.75rem;">Selecciona o ingresa quién atenderá en ventanilla:</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('ventanilla.iniciar-turno') }}">
                        @csrf
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="flex-grow-1" style="min-width: 160px;">
                                @if($recepcionistas->isNotEmpty())
                                    <select name="id_recepcionista" id="v_recep" class="form-select form-select-sm" onchange="toggleNuevoRecepVentanilla(this.value)">
                                        <option value="">— Seleccionar recepcionista —</option>
                                        @foreach($recepcionistas as $r)
                                            <option value="{{ $r->id_recepcionista }}">{{ $r->nombre }}</option>
                                        @endforeach
                                        <option value="nuevo">+ Registrar nuevo recepcionista…</option>
                                    </select>
                                @endif
                                <div id="v_nuevo_recep_box" style="{{ $recepcionistas->isEmpty() ? '' : 'display: none;' }}" class="{{ $recepcionistas->isNotEmpty() ? 'mt-1' : '' }}">
                                    <input type="text" id="v_nombre_nuevo" name="nombre_nuevo_recep" class="form-control form-control-sm"
                                           placeholder="Nombre completo (ej: María García)" maxlength="150"
                                           {{ $recepcionistas->isEmpty() ? 'required' : '' }}>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning btn-sm flex-shrink-0 align-self-start">
                                <i class="ti ti-play me-1"></i> Iniciar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($turnosHoy->count() === 1)
            {{-- Turno único --}}
            <div class="card mb-2 shadow-sm border-0">
                <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2 text-truncate" style="min-width: 0;">
                        <span class="status-dot status-dot-animated bg-success flex-shrink-0" title="Turno activo"></span>
                        <span class="text-muted small text-nowrap">Turno:</span>
                        <span class="fw-bold text-dark text-truncate" style="font-size: 0.875rem;">
                            {{ $turnoActivo->nombre_responsable }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <span class="badge bg-blue-lt text-blue px-2 py-1" style="font-size: 0.72rem;">
                            <i class="ti ti-users me-1"></i>{{ $llegadas->count() }} llegadas
                        </span>
                        <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm" style="width: 28px; height: 28px;" title="Abrir otro turno hoy" data-bs-toggle="modal" data-bs-target="#modalNuevoTurnoVentanilla">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        @else
            {{-- Múltiples turnos: Selector  --}}
            <div class="card mb-2 shadow-sm border-0">
                <div class="card-body py-1 px-3 d-flex align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-1 flex-grow-1 text-truncate" style="min-width: 0;">
                        <span class="status-dot status-dot-animated bg-success flex-shrink-0" title="Turno activo"></span>
                        <span class="text-muted small text-nowrap me-1">Turno:</span>
                        <form method="GET" action="{{ route('ventanilla.index') }}" class="m-0 flex-grow-1" style="max-width: 200px;">
                            <select name="turno_id" id="select_turno_id" class="form-select form-select-sm py-0 px-2 border-0 bg-light fw-bold text-dark" style="font-size: 0.85rem; height: 30px; cursor: pointer;" onchange="this.form.submit()">
                                @foreach($turnosHoy as $t)
                                    <option value="{{ $t->id_turno }}"
                                        {{ $turnoActivo && $turnoActivo->id_turno == $t->id_turno ? 'selected' : '' }}>
                                        {{ $t->nombre_responsable }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <span class="badge bg-blue-lt text-blue px-2 py-1" style="font-size: 0.72rem;">
                            <i class="ti ti-users me-1"></i>{{ $llegadas->count() }} llegadas
                        </span>
                        <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm" style="width: 28px; height: 28px;" title="Abrir otro turno hoy" data-bs-toggle="modal" data-bs-target="#modalNuevoTurnoVentanilla">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulario de búsqueda y registro de llegada --}}
        @if($turnoActivo)
        <div class="card">
            <div class="card-header d-none d-md-block">
                <h3 class="card-title mb-0">
                    <i class="ti ti-user-search me-2 text-success"></i> Registrar Llegada de Paciente
                </h3>
            </div>
            <div class="card-body p-2 p-md-3">
                {{-- Barra de búsqueda rápida inteligente --}}
                <div class="position-relative">
                    <div class="input-icon">
                        <span class="input-icon-addon ps-3">
                            <i class="ti ti-search text-primary fs-2"></i>
                        </span>
                        <input type="text" id="buscar_paciente" class="form-control form-control-lg pe-5 ps-5"
                               placeholder="Nombre, DPI, expediente, No. registro…" autocomplete="off"
                               style="border-radius: 10px; font-size: 0.95rem; height: 46px;">
                        <span id="btn_clear_buscar" class="input-icon-addon pe-2" style="display: none; cursor: pointer; pointer-events: all;" title="Limpiar búsqueda">
                            <i class="ti ti-x text-muted fs-2"></i>
                        </span>
                    </div>
                    <div id="search-suggestions" class="search-suggestions-dropdown">
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ============================================================
         COLUMNA DERECHA: Lista de llegadas del turno activo
    ============================================================ --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="card-title mb-0">
                    <i class="ti ti-list-check me-2 text-primary"></i>
                    Llegadas de Este Turno
                </h3>
                @if($turnoActivo && $llegadas->isNotEmpty() && !Auth::user()->esRecepcionista())
                <div class="d-flex gap-1">
                    <button type="button" 
                            class="btn btn-xs btn-outline-info py-1 px-2 btn-preview-pdf"
                            data-preview-url="{{ route('reportes.pdf', ['turnoId' => $turnoActivo->id_turno, 'preview' => 1]) }}"
                            data-download-url="{{ route('reportes.pdf', $turnoActivo->id_turno) }}"
                            data-title="Reporte Diario — Turno #{{ $turnoActivo->id_turno }}"
                            title="Previsualizar PDF en la misma pantalla">
                        <i class="ti ti-eye me-1"></i>
                        <span class="d-none d-sm-inline">Previa PDF</span>
                        <span class="d-inline d-sm-none">Previa</span>
                    </button>
                    <a href="{{ route('reportes.diario', ['turnoId' => $turnoActivo->id_turno, 'from' => 'ventanilla']) }}"
                       class="btn btn-xs btn-outline-success py-1 px-2"
                       title="Ver Reporte Detallado">
                        <i class="ti ti-chart-bar me-1"></i>
                        <span class="d-none d-sm-inline">Ver Reporte</span>
                        <span class="d-inline d-sm-none">Reporte</span>
                    </a>
                </div>
                @endif
            </div>

            @if(!$turnoActivo)
            <div class="card-body text-center text-secondary py-5">
                <i class="ti ti-door-off fs-1 d-block mb-3 opacity-50"></i>
                <p class="mb-1 fw-bold text-dark">No hay un turno activo en este momento</p>
                <p class="small text-muted">Ingrese quién está en ventanilla en el panel izquierdo para comenzar a registrar llegadas.</p>
            </div>
            @elseif($llegadas->isEmpty())
            <div class="card-body text-center text-secondary py-5">
                <i class="ti ti-users fs-1 d-block mb-3 opacity-50"></i>
                <p>Aún no hay llegadas registradas en este turno.</p>
                <p class="small">Use el panel izquierdo para buscar y registrar pacientes.</p>
            </div>
            @else
            <!-- Vista de Tabla para Escritorio -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-vcenter table-sm card-table">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Paciente</th>
                            <th>Expediente</th>
                            <th>Hora</th>
                            <th>Tipo</th>
                            <th class="text-end">Anular</th>
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
                                            Fam. #{{ $reg->paciente->familia->numero_familia }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-secondary small">{{ $reg->paciente->numero_expediente_fisico }}</td>
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
                            <td class="text-end">
                                <button type="button" class="btn btn-xs btn-outline-danger btn-anular" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalAnularLlegada" 
                                        data-action="{{ route('ventanilla.destroy', $reg->id_registro) }}"
                                        title="Anular">
                                    <i class="ti ti-trash" style="font-size:.85rem"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Vista de Tarjetas para Móviles -->
            <div class="divide-y d-md-none">
                @foreach($llegadas as $i => $reg)
                    <div class="p-3">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-sm bg-{{ $reg->paciente->sexo === 'F' ? 'pink' : 'blue' }}-lt
                                      text-{{ $reg->paciente->sexo === 'F' ? 'pink' : 'blue' }} rounded-circle">
                                    <i class="ti ti-user{{ $reg->paciente->sexo === 'F' ? '-female' : '' }}"></i>
                                </span>
                                <div>
                                    <div class="fw-bold" style="font-size: 0.95rem;">
                                        {{ $reg->paciente->nombres }} {{ $reg->paciente->apellidos }}
                                    </div>
                                    <div class="text-secondary small">
                                        Exp: <strong class="text-primary">{{ $reg->paciente->numero_expediente_fisico }}</strong>
                                        · Fam. #{{ $reg->paciente->familia->numero_familia }}
                                    </div>
                                    <div class="text-secondary small" style="font-size: 0.75rem; opacity: 0.8;">
                                        Registro #{{ $i + 1 }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-end d-flex flex-column align-items-end gap-1">
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                    <i class="ti ti-clock me-1 text-muted"></i>{{ \Carbon\Carbon::parse($reg->hora_llegada)->format('H:i') }}
                                </div>
                                <div>
                                    @if($reg->es_nuevo)
                                        <span class="badge bg-green-lt text-green py-0 px-1" style="font-size: 0.7rem;">Nuevo</span>
                                    @else
                                        <span class="badge bg-secondary-lt text-secondary py-0 px-1" style="font-size: 0.7rem;">Recurrente</span>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-danger btn-anular py-1 px-2 mt-1 d-flex align-items-center gap-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalAnularLlegada" 
                                        data-action="{{ route('ventanilla.destroy', $reg->id_registro) }}"
                                        style="font-size: 0.7rem;">
                                    <i class="ti ti-trash"></i> Anular
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL NUEVO PACIENTE (desde ventanilla) --}}
<div class="modal fade" id="modalNuevoPaciente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="ti ti-user-plus me-2"></i> Registrar Nuevo Paciente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pacientes.store') }}" method="POST" id="formNuevoPacienteVentanilla">
                @csrf
                <input type="hidden" name="desde_ventanilla" value="1">
                <input type="hidden" name="turno_id" value="{{ $turnoActivo ? $turnoActivo->id_turno : '' }}">
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
                            <label class="form-label required" for="nv_nacimiento">Fecha Nacimiento</label>
                            <input type="date" id="nv_nacimiento" name="fecha_nacimiento" class="form-control" required>
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
                            <input type="text" id="nv_dpi" name="dpi" class="form-control" maxlength="13" placeholder="Ej: 1987654320101">
                            <span id="msg-nv-dpi" class="form-hint fw-bold"></span>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nv_telefono">Teléfono</label>
                            <input type="text" id="nv_telefono" name="telefono" class="form-control" maxlength="8" placeholder="Ej: 55551234">
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
                                       class="form-control" required autocomplete="off"
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
                    {{-- Datos del Registro Físico --}}
                    <h6 class="text-secondary border-bottom pb-2 mb-3" style="font-size: 0.9rem;">
                        Datos del Registro Físico
                    </h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="nv_numero_registro">No. de Registro</label>
                            <input type="number" id="nv_numero_registro" name="numero_registro" class="form-control" min="0" step="1" placeholder="Ej: 1024">
                            <span id="msg-nv-numero-registro" class="form-hint fw-bold"></span>
                            <span class="form-hint text-muted">Número del registro físico</span>
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
                    <button type="submit" class="btn btn-success w-100 w-sm-auto"><i class="ti ti-login me-1"></i> Guardar y Registrar Primera Llegada</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL ANULAR LLEGADA --}}
<div class="modal fade" id="modalAnularLlegada" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="ti ti-alert-triangle me-2"></i> Confirmar Anulación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="formAnularLlegada">
                @csrf
                @method('DELETE')
                <input type="hidden" name="turno_id" value="{{ $turnoActivo ? $turnoActivo->id_turno : '' }}">
                <div class="modal-body py-4">
                    <p class="mb-0 text-dark">¿Está seguro de que desea anular este registro de llegada de paciente?</p>
                    <p class="small text-secondary mb-0 mt-1">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="ti ti-trash me-1"></i> Confirmar Anulación</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════ MODAL NUEVO TURNO DESDE VENTANILLA ══════════════════ --}}
<div class="modal fade" id="modalNuevoTurnoVentanilla" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="ti ti-plus me-2"></i> Abrir Otro Turno Hoy</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('ventanilla.iniciar-turno') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">RECEPCIONISTA / RESPONSABLE</label>
                        @if($recepcionistas->isNotEmpty())
                            <select name="id_recepcionista" class="form-select form-select-sm mb-2" onchange="toggleModalNuevoRecep(this.value)">
                                <option value="">— Seleccionar recepcionista registrado —</option>
                                @foreach($recepcionistas as $r)
                                    <option value="{{ $r->id_recepcionista }}">{{ $r->nombre }}</option>
                                @endforeach
                                <option value="nuevo">+ Registrar nuevo recepcionista…</option>
                            </select>
                        @endif
                        <div id="modal_v_nuevo_box" style="{{ $recepcionistas->isEmpty() ? '' : 'display: none;' }}">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="ti ti-user-plus"></i></span>
                                <input type="text" id="modal_v_nombre_nuevo" name="nombre_nuevo_recep" class="form-control"
                                       placeholder="Nombre completo (ej: Juan Pérez)" maxlength="150"
                                       {{ $recepcionistas->isEmpty() ? 'required' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ti ti-check me-1"></i> Abrir Turno
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
    const buscarInput   = document.getElementById('buscar_paciente');
    const suggestions   = document.getElementById('search-suggestions');
    const turnoId       = {{ $turnoActivo ? $turnoActivo->id_turno : 'null' }};
    const storePath     = '{{ route('ventanilla.store') }}';
    const buscarPath    = '{{ route('ventanilla.buscar') }}';
    const csrfToken     = '{{ csrf_token() }}';
    let timer;

    // ── Buscador inteligente ───────────────────────────────────────
    const btnClearBuscar = document.getElementById('btn_clear_buscar');
    if (buscarInput && suggestions) {
        document.addEventListener('click', e => {
            if (buscarInput && suggestions && !buscarInput.contains(e.target) && !suggestions.contains(e.target)) {
                suggestions.style.display = 'none';
            }
        });
        suggestions.addEventListener('click', e => e.stopPropagation());

        buscarInput.addEventListener('input', function () {
            const q = this.value.trim();
            if (btnClearBuscar) {
                btnClearBuscar.style.display = q.length > 0 ? 'flex' : 'none';
            }
            if (q.length < 2) { suggestions.style.display = 'none'; return; }

            clearTimeout(timer);
            timer = setTimeout(() => {
                fetch(`${buscarPath}?q=${encodeURIComponent(q)}&turno_id=${turnoId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => renderSuggestions(data, q));
            }, 180);
        });

        if (btnClearBuscar) {
            btnClearBuscar.addEventListener('click', function () {
                buscarInput.value = '';
                buscarInput.focus();
                btnClearBuscar.style.display = 'none';
                suggestions.style.display = 'none';
            });
        }
    }

    // ── Ocultar sugerencias al abrir modal ──────────────────────────
    const modalNuevoPaciente = document.getElementById('modalNuevoPaciente');
    if (modalNuevoPaciente && suggestions) {
        modalNuevoPaciente.addEventListener('show.bs.modal', () => {
            suggestions.style.display = 'none';
        });
    }

    function renderSuggestions(data, q) {
        suggestions.innerHTML = '';

        if (!data.length) {
            // ─ Sin resultados: ofrecer registro de nuevo paciente ─
            suggestions.innerHTML = `
                <div class="p-3 text-center border-bottom bg-white">
                    <i class="ti ti-user-off d-block fs-2 text-muted mb-2 opacity-50"></i>
                    <p class="text-secondary small mb-2">No se encontró ningún paciente con <strong>"${q}"</strong></p>
                    <button type="button" class="btn btn-sm btn-primary w-100 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoPaciente">
                        <i class="ti ti-user-plus me-1"></i>Registrar nuevo paciente
                    </button>
                </div>`;
            suggestions.style.display = 'block';
            return;
        }

        data.forEach(p => {
            const sexColor = p.sexo === 'F' ? 'pink' : 'blue';
            const sexIcon  = p.sexo === 'F' ? 'user-female' : 'user';
            const nowHHMM  = new Date().toTimeString().slice(0,5);

            let actionBlock;
            if (p.ya_registrado) {
                actionBlock = `
                    <div class="mt-2 pt-2 border-top">
                        <span class="badge bg-success-lt text-success w-100 py-2 d-inline-flex align-items-center justify-content-center fw-medium" style="font-size: 0.8rem; border-radius: 6px;">
                            <i class="ti ti-check me-1"></i> Ya registrado en este turno
                        </span>
                    </div>`;
            } else {
                actionBlock = `
                    <form action="${storePath}" method="POST" class="mt-2 pt-2 border-top">
                        <input type="hidden" name="_token"      value="${csrfToken}">
                        <input type="hidden" name="id_turno"    value="${turnoId}">
                        <input type="hidden" name="id_paciente" value="${p.id_paciente}">
                        <div class="row g-2 align-items-center">
                            <div class="col-5 col-sm-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text px-1 px-sm-2 bg-light text-muted border-end-0">
                                        <i class="ti ti-clock"></i>
                                    </span>
                                    <input type="time" name="hora_llegada" value="${nowHHMM}" 
                                           class="form-control form-control-sm border-start-0 px-1 text-center font-monospace fw-bold" 
                                           style="font-size: 0.84rem; min-width: 0;" required>
                                </div>
                            </div>
                            <div class="col-7 col-sm-8">
                                <button type="submit" class="btn btn-sm btn-success w-100 fw-bold shadow-sm py-1 d-flex align-items-center justify-content-center" style="font-size: 0.84rem;">
                                    <i class="ti ti-login me-1"></i>
                                    <span class="text-nowrap">Registrar Llegada</span>
                                </button>
                            </div>
                        </div>
                    </form>`;
            }

            const famInfo = p.familia_numero
                ? `<span class="badge bg-blue-lt text-blue me-1">Fam. ${p.familia_numero}</span><span>${p.comunidad ?? ''}</span>`
                : '<span class="text-muted">Sin núcleo familiar</span>';

            const item = document.createElement('div');
            item.className = 'suggestion-patient-item p-3 border-bottom' + (p.ya_registrado ? ' bg-success-lt' : ' bg-white');
            item.innerHTML = `
                <div class="d-flex align-items-start gap-2 gap-sm-3">
                    <span class="avatar avatar-sm bg-${sexColor}-lt text-${sexColor} rounded-circle flex-shrink-0 mt-1">
                        <i class="ti ti-${sexIcon}"></i>
                    </span>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="d-flex align-items-baseline justify-content-between flex-wrap gap-1">
                            <span class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">${p.nombres} ${p.apellidos}</span>
                            <span class="badge bg-secondary-lt text-secondary" style="font-size: 0.72rem;">${p.edad ?? '?'} años · ${p.sexo}</span>
                        </div>
                        <div class="small text-secondary my-1 d-flex flex-wrap align-items-center gap-1" style="font-size: 0.78rem;">
                            <span>Exp: <strong>${p.numero_expediente_fisico}</strong></span>
                            ${p.numero_registro ? '<span class="badge bg-purple-lt text-purple fw-bold">Reg. #' + p.numero_registro + '</span>' : ''}
                            ${p.dpi ? '<span class="text-muted">· DPI: ' + p.dpi + '</span>' : ''}
                        </div>
                        <div class="small text-secondary" style="font-size: 0.78rem;">${famInfo}</div>
                        ${actionBlock}
                    </div>
                </div>`;
            suggestions.appendChild(item);
        });

        // Botón al final para nuevo paciente si no se encontró lo que se busca
        const footer = document.createElement('div');
        footer.className = 'p-2 text-center bg-light border-top sticky-bottom';
        footer.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-primary w-100 fw-medium" data-bs-toggle="modal" data-bs-target="#modalNuevoPaciente">
                <i class="ti ti-user-plus me-1"></i>¿No está en la lista? Registrar nuevo paciente
            </button>`;
        suggestions.appendChild(footer);

        suggestions.style.display = 'block';
    }

    // ── Modal de Confirmación de Anulación 
    document.querySelectorAll('.btn-anular').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = document.getElementById('formAnularLlegada');
            if (form) form.action = this.getAttribute('data-action');
        });
    });

    //  Modal Nuevo Paciente: ubicación cascada
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

    // ── Modal Nuevo Paciente: autocompletado de familia 
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
        // Ocultar dropdown al clic fuera
        document.addEventListener('click', e => {
            if (!nvNumFamInput.contains(e.target) && !nvFamSug.contains(e.target)) {
                nvFamSug.style.display = 'none';
            }
        });
        nvFamSug.addEventListener('click', e => e.stopPropagation());

        nvNumFamInput.addEventListener('input', function () {
            const q = this.value.trim();
            nvIdFamInput.value = ''; // limpiar id previo

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
            // No existe: se creará una nueva familia
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

        // Estado del campo
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

    // ── Modal Nuevo Paciente: verificar duplicados (DPI y No. Registro)
    const nvDpi = document.getElementById('nv_dpi');
    const nvDpiMsg = document.getElementById('msg-nv-dpi');
    const nvNumReg = document.getElementById('nv_numero_registro');
    const nvNumRegMsg = document.getElementById('msg-nv-numero-registro');
    const formNuevoPaciente = document.getElementById('formNuevoPacienteVentanilla');

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

    if (formNuevoPaciente) {
        formNuevoPaciente.addEventListener('submit', function (e) {
            if (nvDpiDuplicado) {
                e.preventDefault();
                alert('No se puede registrar: El DPI ingresado ya está asignado a otro registro.');
                nvDpi.focus();
                return false;
            }
            if (nvNumRegDuplicado) {
                e.preventDefault();
                alert('No se puede registrar: El No. de Registro ya está asignado a otro paciente.');
                nvNumReg.focus();
                return false;
            }
        });
    }

    @if($errors->any() && old('desde_ventanilla'))
    const modalNvEl = document.getElementById('modalNuevoPaciente');
    if (modalNvEl) {
        const modalNv = new bootstrap.Modal(modalNvEl);
        modalNv.show();
    }
    @endif
});

function toggleNuevoRecepVentanilla(val) {
    const box   = document.getElementById('v_nuevo_recep_box');
    const input = document.getElementById('v_nombre_nuevo');
    if (!box) return;
    if (val === 'nuevo' || val === '') {
        box.style.display = 'block';
        if (input && val === 'nuevo') {
            input.required = true;
            input.focus();
        }
    } else {
        box.style.display = 'none';
        if (input) {
            input.required = false;
            input.value = '';
        }
    }
}

function toggleModalNuevoRecep(val) {
    const box   = document.getElementById('modal_v_nuevo_box');
    const input = document.getElementById('modal_v_nombre_nuevo');
    if (!box) return;
    if (val === 'nuevo' || val === '') {
        box.style.display = 'block';
        if (input && val === 'nuevo') {
            input.required = true;
            input.focus();
        }
    } else {
        box.style.display = 'none';
        if (input) {
            input.required = false;
            input.value = '';
        }
    }
}
</script>

{{-- Modal para previsualización en pantalla del PDF --}}
@if(!Auth::user()->esRecepcionista())
@include('reportes.preview-modal')
@endif

@endsection
