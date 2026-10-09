@extends('layouts.app')

@section('title', 'Gestión de Turnos')
@section('page_title', 'Módulo de Ventanilla — Gestión de Turnos')

@section('content')

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
    <strong>Errores:</strong>
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@php
    $prevMes  = $mes == 1  ? 12 : $mes - 1;
    $prevAnio = $mes == 1  ? $anio - 1 : $anio;
    $nextMes  = $mes == 12 ?  1 : $mes + 1;
    $nextAnio = $mes == 12 ? $anio + 1 : $anio;
    $nombreMes = \Carbon\Carbon::createFromDate($anio, $mes, 1)->locale('es')->isoFormat('MMMM YYYY');

    $totalDias    = $inicio->daysInMonth;
    $primerDia    = $inicio->copy()->startOfMonth();
    $offsetInicio = ($primerDia->dayOfWeek === 0) ? 6 : $primerDia->dayOfWeek - 1;
@endphp

{{-- ══════════════════ CALENDARIO ══════════════════ --}}
<div class="card border-0 shadow-sm mb-3">

    {{-- Cabecera --}}
    <div class="card-header d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between gap-2 py-2 py-md-3 px-2 px-md-3 bg-white border-bottom">
        <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-1 gap-sm-2">
            <a href="{{ route('turnos.index', ['mes' => $prevMes, 'anio' => $prevAnio]) }}"
               class="btn btn-ghost-secondary btn-icon btn-sm" title="Mes anterior">
                <i class="ti ti-chevron-left"></i>
            </a>
            <h5 class="mb-0 fw-bold text-capitalize text-center text-truncate px-1" style="min-width: 140px; font-size: 1rem;">
                {{ $nombreMes }}
            </h5>
            <a href="{{ route('turnos.index', ['mes' => $nextMes, 'anio' => $nextAnio]) }}"
               class="btn btn-ghost-secondary btn-icon btn-sm" title="Mes siguiente">
                <i class="ti ti-chevron-right"></i>
            </a>
            <a href="{{ route('turnos.index', ['mes' => now()->month, 'anio' => now()->year]) }}"
               class="btn btn-sm btn-outline-primary py-1 px-2 ms-1">Hoy</a>
        </div>
        @if(Auth::user()->esAdministrador())
        <div class="d-flex align-items-center gap-2 w-100 w-md-auto flex-wrap flex-md-nowrap">
            <button type="button" class="btn btn-sm btn-outline-primary flex-fill flex-md-grow-0 d-inline-flex align-items-center justify-content-center"
                    data-bs-toggle="modal" data-bs-target="#modalEscanearRol"
                    title="Escanear rol de turnos">
                <i class="ti ti-camera me-1"></i>
                <span class="text-nowrap">Escanear Rol</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill flex-md-grow-0 d-inline-flex align-items-center justify-content-center"
                    data-bs-toggle="modal" data-bs-target="#modalRecepcionistas"
                    title="Gestionar Recepcionistas">
                <i class="ti ti-users me-1"></i>
                <span class="text-nowrap">Recepcionistas</span>
            </button>
            <button type="button" class="btn btn-sm btn-primary flex-fill flex-md-grow-0 d-inline-flex align-items-center justify-content-center"
                    id="btnNuevoTurnoHeader"
                    data-bs-toggle="modal" data-bs-target="#modalCrearTurno">
                <i class="ti ti-plus me-1"></i>
                <span class="text-nowrap">Nuevo Turno</span>
            </button>
        </div>
        @endif
    </div>

    {{-- Días de la semana --}}
    <div class="cal-grid cal-header border-bottom">
        @foreach(['L','M','X','J','V','S','D'] as $d)
        <div class="cal-cell cal-hd">{{ $d }}</div>
        @endforeach
    </div>

    {{-- Celdas del mes --}}
    <div class="cal-grid cal-body">
        @for($pad = 0; $pad < $offsetInicio; $pad++)
        <div class="cal-cell cal-empty"></div>
        @endfor

        @for($d = 1; $d <= $totalDias; $d++)
            @php
                $fecha     = \Carbon\Carbon::createFromDate($anio, $mes, $d)->toDateString();
                $turnosDia = $turnos[$fecha] ?? collect();
                $esHoy     = $fecha === today()->toDateString();
                $esPasado  = $fecha < today()->toDateString();
                $esFin     = in_array(\Carbon\Carbon::createFromDate($anio, $mes, $d)->dayOfWeek, [0, 6]);
            @endphp
            <div class="cal-cell cal-day
                        {{ $esHoy ? 'cal-hoy' : '' }}
                        {{ $esFin ? 'cal-fin' : '' }}
                        {{ ($esPasado && $turnosDia->isEmpty()) ? 'cal-pasado' : '' }}"
                 data-fecha="{{ $fecha }}"
                 onclick="openDayModal('{{ $fecha }}', {{ $d }})">

                <div class="cal-num {{ $esHoy ? 'cal-num-hoy' : '' }}">{{ $d }}</div>

                {{-- Un punto azul por cada turno registrado en el día --}}
                <div class="cal-dots">
                    @foreach($turnosDia as $t)
                        <span class="cal-dot" title="{{ $t->nombre_responsable }}"></span>
                    @endforeach
                </div>
            </div>
        @endfor
    </div>

    {{-- Pie --}}
    <div class="card-footer bg-white border-top py-2 px-2 px-md-3 d-flex flex-wrap align-items-center justify-content-between gap-1">
        <span class="d-flex align-items-center gap-1 text-secondary" style="font-size:0.72rem;">
            <span class="cal-dot" style="flex-shrink:0;"></span>
            Turno asignado
        </span>
        <span class="text-secondary" style="font-size:0.72rem;">
            <i class="ti ti-hand-click me-1"></i>Toca un día para ver o agregar turnos
        </span>
    </div>
</div>

{{-- ══════════════════ MODAL DÍA ══════════════════ --}}
<div class="modal fade" id="modalDia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 440px;">
        <div class="modal-content">
            <div class="modal-header" id="modalDiaHeader">
                <h5 class="modal-title fw-bold" id="modalDiaTitle">—</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3" id="modalDiaBody">
                <div class="text-center text-secondary py-3 fst-italic" id="modalDiaSinTurnos">
                    <i class="ti ti-calendar-off d-block fs-2 mb-1 opacity-40"></i>
                    Sin turnos registrados este día.
                </div>
                <div id="modalDiaLista"></div>
            </div>
            <div class="modal-footer bg-light justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                @if(Auth::user()->esAdministrador())
                <button type="button" class="btn btn-primary btn-sm" id="btnAgregarDesdeDia"
                        data-bs-dismiss="modal"
                        data-bs-toggle="modal" data-bs-target="#modalCrearTurno">
                    <i class="ti ti-plus me-1"></i> Agregar Turno
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->esAdministrador())

{{-- ══════════════════ MODAL CREAR TURNO ══════════════════ --}}
<div class="modal fade" id="modalCrearTurno" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="ti ti-calendar-plus me-2"></i> Nuevo Turno</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('turnos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="c_fecha" class="form-label required">Fecha</label>
                            <input type="date" id="c_fecha" name="fecha" class="form-control form-control-sm"
                                   value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="col-12">
                            <label for="c_recep" class="form-label">Recepcionista Asignado</label>
                            <select id="c_recep" name="id_recepcionista" class="form-select form-select-sm">
                                <option value="">— Nuevo o sin asignar —</option>
                                @foreach($recepcionistas as $r)
                                    <option value="{{ $r->id_recepcionista }}">{{ $r->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12" id="c_nuevo_recep_wrapper" style="display:none;">
                            <label for="c_nombre_nuevo" class="form-label">Nombre del nuevo recepcionista</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="ti ti-user-plus"></i></span>
                                <input type="text" id="c_nombre_nuevo" name="nombre_nuevo_recep"
                                       class="form-control" placeholder="Ej: María García" maxlength="150">
                            </div>
                            <div class="form-hint text-info small mt-1">
                                <i class="ti ti-info-circle me-1"></i>Se guardará como recepcionista registrado.
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="c_obs" class="form-label">Observaciones</label>
                            <textarea id="c_obs" name="observaciones" class="form-control form-control-sm" rows="2"
                                      placeholder="Notas adicionales…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Turno
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════ MODAL EDITAR TURNO ══════════════════ --}}
<div class="modal fade" id="modalEditarTurno" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="ti ti-edit me-2"></i> Editar Turno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Formulario oculto para eliminar turno -->
            <form id="formEliminarTurno" method="POST" action="" class="d-none">
                @csrf
                @method('DELETE')
            </form>

            <form action="" method="POST" id="formEditarTurno">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="e_fecha" class="form-label required">Fecha</label>
                            <input type="date" id="e_fecha" name="fecha" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12">
                            <label for="e_recep" class="form-label">Recepcionista</label>
                            <select id="e_recep" name="id_recepcionista" class="form-select form-select-sm">
                                <option value="">— Nuevo o sin asignar —</option>
                                @foreach($recepcionistas as $r)
                                    <option value="{{ $r->id_recepcionista }}">{{ $r->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12" id="e_nuevo_recep_wrapper" style="display:none;">
                            <label for="e_nombre_nuevo" class="form-label">Nombre del nuevo recepcionista</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="ti ti-user-plus"></i></span>
                                <input type="text" id="e_nombre_nuevo" name="nombre_nuevo_recep"
                                       class="form-control" placeholder="Ej: María García" maxlength="150">
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="e_obs" class="form-label">Observaciones</label>
                            <textarea id="e_obs" name="observaciones" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="display: flex !important; flex-wrap: nowrap !important; justify-content: flex-end !important; align-items: center !important; gap: 0.5rem !important;">
                    <button type="submit" form="formEliminarTurno" class="btn btn-outline-danger btn-sm text-nowrap"
                            style="margin-right: auto !important; padding: 0.35rem 0.65rem !important;"
                            onclick="return confirm('¿Eliminar este turno?')">
                        <i class="ti ti-trash me-1"></i> Eliminar
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm text-nowrap"
                            style="padding: 0.35rem 0.65rem !important;"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning btn-sm text-nowrap"
                            style="padding: 0.35rem 0.65rem !important;">
                        <i class="ti ti-device-floppy me-1"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════ MODAL RECEPCIONISTAS ══════════════════ --}}
<div class="modal fade" id="modalRecepcionistas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="ti ti-users me-2"></i> Recepcionistas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 mb-3">
                    <input type="text" id="nuevoRecepNombre" class="form-control form-control-sm"
                           placeholder="Nombre del recepcionista…" maxlength="150">
                    <button type="button" id="btnGuardarRecep" class="btn btn-primary btn-sm flex-shrink-0">
                        <i class="ti ti-plus"></i> Agregar
                    </button>
                </div>
                <div id="listaRecepcionistas">
                    @foreach($recepcionistas as $r)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom" data-id="{{ $r->id_recepcionista }}">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-xs bg-blue-lt text-blue rounded-circle fw-bold">
                                {{ strtoupper(substr($r->nombre, 0, 1)) }}
                            </span>
                            <span style="font-size:0.9rem;">{{ $r->nombre }}</span>
                        </div>
                        <form action="{{ route('recepcionistas.destroy', $r->id_recepcionista) }}" method="POST"
                              onsubmit="return confirm('¿Desactivar a {{ addslashes($r->nombre) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                    @if($recepcionistas->isEmpty())
                    <div class="text-center text-secondary py-3" id="sinRecepMsg">
                        <i class="ti ti-users-off d-block fs-2 mb-1 opacity-50"></i>
                        No hay recepcionistas registrados.
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════ MODAL ESCANEAR ROL DE TURNOS ══════════════════ --}}
<div class="modal fade" id="modalEscanearRol" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalEscanearRolLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            
            {{-- Modal Header --}}
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalEscanearRolLabel">
                    <i class="ti ti-calendar-event fs-3"></i>
                    <span>Escanear Rol de Turnos</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar" id="btnCerrarModalEscanear"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-3 p-md-4">
                
                {{-- Alerta informativa inicial --}}
                <div class="alert alert-info d-flex align-items-start gap-2 mb-3 py-2 px-3" role="alert">
                    <i class="ti ti-info-circle fs-3 text-info flex-shrink-0 mt-1"></i>
                    <div style="font-size: 0.88rem;">
                        <strong>¿Cómo funciona?</strong> Toma una foto con tu teléfono o sube una imagen del rol de ventanilla. El sistema extraerá automáticamente los nombres, fechas y días para agregarlos al calendario.
                    </div>
                </div>

                {{-- Contenedor de Error --}}
                <div class="alert alert-danger d-none align-items-center gap-2 mb-3" id="ocrErrorAlert" role="alert">
                    <i class="ti ti-alert-triangle fs-3 flex-shrink-0"></i>
                    <div id="ocrErrorMessage" style="font-size: 0.88rem;"></div>
                </div>

                {{-- PASO 1: Subida de Imagen / Captura --}}
                <div id="pasoSubirImagen">
                    <div class="ocr-dropzone p-3 p-md-4 text-center border border-2 border-dashed rounded-3 bg-light position-relative mb-2" id="ocrDropzone" style="cursor: pointer;">
                        <input type="file" id="inputArchivoOcr" accept="image/*" class="d-none">
                        <input type="file" id="inputCamaraOcr" accept="image/*" capture="environment" class="d-none">

                        <div id="ocrPromptZone">
                            <i class="ti ti-camera-plus text-primary fs-1 mb-1 d-block"></i>
                            <h6 class="fw-bold mb-1">Arrastra la imagen aquí o elige una opción:</h6>
                            <p class="text-muted small mb-2">Soporta JPG, PNG o WEBP (hasta 15 MB)</p>
                            
                            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                                <button type="button" class="btn btn-primary d-inline-flex align-items-center justify-content-center py-2 px-3" id="btnDispararCamara">
                                    <i class="ti ti-camera me-1"></i> Tomar Foto con Cámara
                                </button>
                                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center py-2 px-3" id="btnDispararArchivo">
                                    <i class="ti ti-folder-open me-1"></i> Seleccionar Archivo
                                </button>
                            </div>
                        </div>

                        {{-- Previsualización de la imagen cargada --}}
                        <div id="ocrPreviewZone" class="d-none">
                            <div class="position-relative d-inline-block mb-2">
                                <img id="ocrPreviewImg" src="" alt="Previsualización del rol" class="rounded shadow-sm" style="max-height: 230px; max-width: 100%; object-fit: contain;">
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="badge bg-secondary-lt text-truncate" id="ocrPreviewNombre" style="max-width: 250px;"></span>
                                <button type="button" class="btn btn-xs btn-outline-danger" id="btnQuitarImagen" title="Cambiar imagen">
                                    <i class="ti ti-trash me-1"></i> Cambiar
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Animación / Estado de Carga --}}
                    <div id="ocrLoadingZone" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <h6 class="fw-bold text-primary mb-1">Procesando imagen...</h6>
                        <p class="text-muted small mb-0">Detectando tabla de turnos, nombres y fechas del calendario.</p>
                    </div>

                    {{-- Botón de Acción Paso 1 --}}
                    <div class="modal-ocr-actions mt-3 pt-3 border-top" id="ocrAccionesPaso1">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btnComenzarAnalisis" disabled>
                            <i class="ti ti-sparkles me-1"></i> Escanear y Extraer Turnos
                        </button>
                    </div>
                </div>

                {{-- PASO 2: Revisión y Confirmación de Turnos Detectados --}}
                <div id="pasoRevisionTurnos" class="d-none">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 p-2 px-3 bg-light rounded-3 mb-3 border">
                        <div>
                            <span class="badge bg-primary text-uppercase" id="ocrTituloDetectado">Rol Detectado</span>
                            <span class="badge bg-success-lt ms-1" id="ocrTotalTurnosBadge">0 turnos</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="checkSobrescribir" checked>
                            <label class="form-check-label small fw-semibold" for="checkSobrescribir">
                                Actualizar si ya hay turno en esa fecha
                            </label>
                        </div>
                    </div>

                    <p class="text-muted small mb-2">
                        <i class="ti ti-check-double text-success me-1"></i> Revisa los turnos detectados antes de guardarlos. Puedes cambiar de recepcionista o descartar filas:
                    </p>

                    <div class="table-responsive border rounded-3 mb-3" style="max-height: 340px; overflow-y: auto;">
                        <table class="table table-sm table-hover table-striped align-middle mb-0" id="tablaTurnosOcr">
                            <thead class="table-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th style="width: 45px;" class="text-center">#</th>
                                    <th style="min-width: 130px;">Fecha / Día</th>
                                    <th style="min-width: 220px;">Recepcionista Asignado</th>
                                    <th style="min-width: 140px;">Horario</th>
                                    <th style="width: 50px;" class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="tbodyTurnosOcr">
                                {{-- Filas generadas con JS --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Indicador de Guardado --}}
                    <div id="ocrSavingZone" class="text-center py-2 d-none">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        <span class="small fw-semibold text-primary">Guardando turnos en el calendario...</span>
                    </div>

                    {{-- Botones de Acción Paso 2 --}}
                    <div class="modal-ocr-actions justify-between-sm mt-3 pt-3 border-top" id="ocrAccionesPaso2">
                        <button type="button" class="btn btn-outline-secondary" id="btnVolverPaso1">
                            <i class="ti ti-arrow-left me-1"></i> Volver a tomar foto
                        </button>
                        <button type="button" class="btn btn-success" id="btnConfirmarImportar">
                            <i class="ti ti-calendar-plus me-1"></i> Confirmar e Importar Turnos
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endif

{{-- Datos de turnos para JS --}}
@php
    $turnosJson = [];
    foreach($turnos as $fecha => $dias) {
        foreach($dias as $t) {
            $turnosJson[$fecha][] = [
                'id'     => $t->id_turno,
                'nombre' => $t->nombre_responsable,
                'recep'  => $t->id_recepcionista,
                'obs'    => $t->observaciones ?? '',
                'fecha'  => $t->fecha->format('Y-m-d'),
            ];
        }
    }
@endphp
<script id="turnosData" type="application/json">@json($turnosJson)</script>

@endsection

@section('scripts')
<style>
/* ── Calendario compacto ── */
.cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}
.cal-hd {
    padding: 6px 2px;
    text-align: center;
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6b7280;
}
.cal-cell {
    border: 1px solid #e5e7eb;
    min-height: 58px;
    position: relative;
    cursor: pointer;
    transition: background .12s;
    padding: 4px 4px 3px 4px;
    box-sizing: border-box;
}
.cal-empty  { background: #f9fafb; cursor: default; border-color: #f3f4f6; }
.cal-fin    { background: #fafcff; }
.cal-day:hover { background: #eff6ff; }
.cal-hoy    { background: #eff6ff !important; }
.cal-pasado { opacity: 0.55; }
.cal-num {
    font-size: 0.82rem;
    font-weight: 700;
    color: #1f2937;
    text-align: right;
    line-height: 1;
    margin-bottom: 3px;
}
.cal-num-hoy {
    background: #3b82f6;
    color: #fff;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    margin-left: auto;
}
.cal-dots {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
}
.cal-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #3b82f6;
    flex-shrink: 0;
}

/* Tarjeta de turno en modal día */
.turno-card {
    border-radius: 10px;
    padding: 11px 14px;
    margin-bottom: 8px;
    background: #ffffff;
    border: 1.5px solid #dbeafe;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.10), 0 1px 2px rgba(15, 23, 42, 0.06);
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
}
/* ── Acciones de modal de escaneo ── */
.modal-ocr-actions {
    display: flex;
    flex-direction: column-reverse;
    gap: 0.625rem;
}
.modal-ocr-actions .btn {
    width: 100%;
    min-height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
}
@media (min-width: 576px) {
    .modal-ocr-actions {
        flex-direction: row;
        justify-content: flex-end;
        align-items: center;
    }
    .modal-ocr-actions .btn {
        width: auto;
        min-height: 38px;
    }
    .modal-ocr-actions.justify-between-sm {
        justify-content: space-between;
    }
}
</style>

<script>
(function () {
    const turnosData = JSON.parse(document.getElementById('turnosData').textContent || '{}');
    const isAdmin    = {{ Auth::user()->esAdministrador() ? 'true' : 'false' }};
    const diasEs     = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    const mesesEs    = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

    // ── Modal Día ──────────────────────────────────────────────
    window.openDayModal = function(fecha, dia) {
        const turnos = turnosData[fecha] || [];
        const d      = new Date(fecha + 'T00:00:00');
        const label  = `${diasEs[d.getDay()]} ${dia} de ${mesesEs[d.getMonth()]}`;

        document.getElementById('modalDiaTitle').textContent =
            label.charAt(0).toUpperCase() + label.slice(1);

        const hoy    = new Date().toISOString().split('T')[0];
        const header = document.getElementById('modalDiaHeader');
        header.className = 'modal-header ' + (fecha === hoy ? 'bg-primary text-white' : 'bg-light');
        // Ajustar color del btn-close para header oscuro
        const closeBtn = header.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.classList.toggle('btn-close-white', fecha === hoy);
        }

        const lista     = document.getElementById('modalDiaLista');
        const sinTurnos = document.getElementById('modalDiaSinTurnos');
        lista.innerHTML = '';

        if (turnos.length === 0) {
            sinTurnos.style.display = '';
        } else {
            sinTurnos.style.display = 'none';
            turnos.forEach(t => {
                const card = document.createElement('div');
                card.className = 'turno-card';
                card.innerHTML = `
                    <div style="min-width:0;">
                        <div class="fw-semibold mb-1" style="font-size:.9rem;">
                            <i class="ti ti-calendar-event me-1 text-primary"></i>Turno del día
                        </div>
                        <div style="font-size:.84rem; color:#374151;">
                            <i class="ti ti-user me-1 text-muted"></i>${t.nombre}
                        </div>
                        ${t.obs ? `<div style="font-size:.75rem;color:#9ca3af;margin-top:3px;font-style:italic;">${t.obs}</div>` : ''}
                    </div>
                    <div class="d-flex flex-column gap-1 flex-shrink-0">
                        <a href="/ventanilla?turno_id=${t.id}"
                           class="btn btn-xs btn-outline-primary" title="Ir a Ventanilla">
                            <i class="ti ti-door-enter"></i>
                        </a>
                        ${isAdmin ? `
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-edit-turno"
                                data-id="${t.id}" data-fecha="${t.fecha}"
                                data-recep="${t.recep || ''}" data-obs="${t.obs || ''}">
                            <i class="ti ti-edit"></i>
                        </button>` : ''}
                    </div>`;
                lista.appendChild(card);
            });

            lista.querySelectorAll('.btn-edit-turno').forEach(btn => {
                btn.addEventListener('click', () => {
                    openEditModal(btn.dataset.id, btn.dataset.fecha, btn.dataset.recep, btn.dataset.obs);
                });
            });
        }

        // Pre-cargar fecha en modal crear
        const btnAgregar = document.getElementById('btnAgregarDesdeDia');
        if (btnAgregar) {
            btnAgregar.onclick = () => {
                const cFecha = document.getElementById('c_fecha');
                if (cFecha) cFecha.value = fecha;
            };
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDia')).show();
    };

    // ── Abrir modal Editar ──────────────────────────────────────
    function openEditModal(id, fecha, recep, obs) {
        const diaModal = bootstrap.Modal.getInstance(document.getElementById('modalDia'));
        if (diaModal) diaModal.hide();

        document.getElementById('formEditarTurno').action = `/turnos/${id}`;
        const delForm = document.getElementById('formEliminarTurno');
        if (delForm) delForm.action = `/turnos/${id}`;

        document.getElementById('e_fecha').value = fecha;
        document.getElementById('e_obs').value   = obs || '';

        const recepSel = document.getElementById('e_recep');
        if (recepSel) {
            recepSel.value = recep || '';
            recepSel.dispatchEvent(new Event('change'));
        }

        setTimeout(() => {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarTurno')).show();
        }, 300);
    }

    // ── Mostrar campo nuevo recepcionista ──
    function bindRecepSel(selId, wrapperId) {
        const sel = document.getElementById(selId);
        const wr  = document.getElementById(wrapperId);
        if (!sel || !wr) return;
        const update = () => wr.style.display = sel.value === '' ? 'block' : 'none';
        sel.addEventListener('change', update);
        update();
    }
    bindRecepSel('c_recep', 'c_nuevo_recep_wrapper');
    bindRecepSel('e_recep', 'e_nuevo_recep_wrapper');

    // ── Agregar recepcionista AJAX ──
    const btnGuardar = document.getElementById('btnGuardarRecep');
    if (btnGuardar) {
        btnGuardar.addEventListener('click', () => {
            const nombre = document.getElementById('nuevoRecepNombre').value.trim();
            if (!nombre) return;
            fetch('{{ route("recepcionistas.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ nombre }),
            })
            .then(r => r.json())
            .then(data => {
                ['c_recep', 'e_recep'].forEach(id => {
                    const sel = document.getElementById(id);
                    if (!sel) return;
                    const opt = document.createElement('option');
                    opt.value = data.id_recepcionista;
                    opt.text  = data.nombre;
                    sel.appendChild(opt);
                });
                const lista  = document.getElementById('listaRecepcionistas');
                const sinMsg = document.getElementById('sinRecepMsg');
                if (sinMsg) sinMsg.remove();
                const div = document.createElement('div');
                div.className = 'd-flex align-items-center justify-content-between py-2 border-bottom';
                div.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-xs bg-blue-lt text-blue rounded-circle fw-bold">
                            ${data.nombre.charAt(0).toUpperCase()}
                        </span>
                        <span style="font-size:.9rem;">${data.nombre}</span>
                    </div>
                    <span class="badge bg-success-lt text-success">Agregado</span>`;
                lista.appendChild(div);
                document.getElementById('nuevoRecepNombre').value = '';
            })
            .catch(() => alert('Error al guardar el recepcionista.'));
        });
    }

    // ── Escaneo de Rol de Turnos ───────────────────────────────
    if (isAdmin) {
        let archivoSeleccionado = null;
        let catalogoRecepcionistas = [];

        const modalEl           = document.getElementById('modalEscanearRol');
        const dropzone          = document.getElementById('ocrDropzone');
        const inputArchivo      = document.getElementById('inputArchivoOcr');
        const inputCamara       = document.getElementById('inputCamaraOcr');
        const promptZone        = document.getElementById('ocrPromptZone');
        const previewZone       = document.getElementById('ocrPreviewZone');
        const previewImg        = document.getElementById('ocrPreviewImg');
        const previewNombre     = document.getElementById('ocrPreviewNombre');
        const btnQuitarImg      = document.getElementById('btnQuitarImagen');
        const btnAnalizar       = document.getElementById('btnComenzarAnalisis');
        const loadingZone       = document.getElementById('ocrLoadingZone');
        const accionesPaso1     = document.getElementById('ocrAccionesPaso1');
        const errorAlert        = document.getElementById('ocrErrorAlert');
        const errorMessage      = document.getElementById('ocrErrorMessage');
        const pasoSubir         = document.getElementById('pasoSubirImagen');
        const pasoRevision      = document.getElementById('pasoRevisionTurnos');
        const tituloDetectado   = document.getElementById('ocrTituloDetectado');
        const totalTurnosBadge  = document.getElementById('ocrTotalTurnosBadge');
        const tbodyTurnos       = document.getElementById('tbodyTurnosOcr');
        const btnVolverPaso1    = document.getElementById('btnVolverPaso1');
        const btnConfirmar      = document.getElementById('btnConfirmarImportar');
        const checkSobrescribir = document.getElementById('checkSobrescribir');
        const savingZone        = document.getElementById('ocrSavingZone');

        function mostrarError(msg) {
            errorMessage.textContent = msg;
            errorAlert.classList.remove('d-none');
            errorAlert.classList.add('d-flex');
        }

        function ocultarError() {
            errorAlert.classList.add('d-none');
            errorAlert.classList.remove('d-flex');
        }

        function setArchivo(file) {
            if (!file || !file.type.startsWith('image/')) {
                mostrarError('Por favor selecciona un archivo de imagen válido (JPG, PNG, WEBP).');
                return;
            }
            ocultarError();
            archivoSeleccionado = file;

            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                previewNombre.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                promptZone.classList.add('d-none');
                previewZone.classList.remove('d-none');
                btnAnalizar.disabled = false;
            };
            reader.readAsDataURL(file);
        }

        function resetSubida() {
            archivoSeleccionado = null;
            inputArchivo.value = '';
            inputCamara.value = '';
            previewImg.src = '';
            previewNombre.textContent = '';
            previewZone.classList.add('d-none');
            promptZone.classList.remove('d-none');
            loadingZone.classList.add('d-none');
            accionesPaso1.classList.remove('d-none');
            btnAnalizar.disabled = true;
            ocultarError();
        }

        // Eventos de selección de archivo
        document.getElementById('btnDispararCamara')?.addEventListener('click', (e) => {
            e.stopPropagation();
            inputCamara.click();
        });

        document.getElementById('btnDispararArchivo')?.addEventListener('click', (e) => {
            e.stopPropagation();
            inputArchivo.click();
        });

        dropzone?.addEventListener('click', (e) => {
            if (e.target.closest('button')) return;
            inputArchivo.click();
        });

        inputArchivo?.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) setArchivo(e.target.files[0]);
        });

        inputCamara?.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) setArchivo(e.target.files[0]);
        });

        btnQuitarImg?.addEventListener('click', (e) => {
            e.stopPropagation();
            resetSubida();
        });

        // Drag & Drop
        ['dragenter', 'dragover'].forEach(name => {
            dropzone?.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-primary', 'bg-blue-lt');
            });
        });

        ['dragleave', 'drop'].forEach(name => {
            dropzone?.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-primary', 'bg-blue-lt');
            });
        });

        dropzone?.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length) {
                setArchivo(dt.files[0]);
            }
        });

        // Al cerrar modal, reiniciar estado si está en paso 1
        modalEl?.addEventListener('hidden.bs.modal', () => {
            resetSubida();
            pasoSubir.classList.remove('d-none');
            pasoRevision.classList.add('d-none');
        });

        // Procesar imagen
        btnAnalizar?.addEventListener('click', () => {
            if (!archivoSeleccionado) return;

            ocultarError();
            dropzone.classList.add('d-none');
            accionesPaso1.classList.add('d-none');
            loadingZone.classList.remove('d-none');

            const formData = new FormData();
            formData.append('imagen', archivoSeleccionado);

            fetch('{{ route("turnos.escanear") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(async (r) => {
                const data = await r.json();
                if (!r.ok || !data.success) {
                    throw new Error(data.error || 'No se pudo procesar la imagen.');
                }
                return data;
            })
            .then((data) => {
                catalogoRecepcionistas = data.recepcionistas || [];
                tituloDetectado.textContent = data.titulo || `Turnos ${data.mes}/${data.anio}`;
                totalTurnosBadge.textContent = `${data.turnos.length} turnos detectados`;

                renderizarFilasRevision(data.turnos);

                pasoSubir.classList.add('d-none');
                pasoRevision.classList.remove('d-none');
            })
            .catch((err) => {
                mostrarError(err.message || 'Error al procesar el documento.');
                dropzone.classList.remove('d-none');
                accionesPaso1.classList.remove('d-none');
                loadingZone.classList.add('d-none');
            });
        });

        // Renderizar tabla de revisión
        function renderizarFilasRevision(turnos) {
            tbodyTurnos.innerHTML = '';

            if (!turnos || turnos.length === 0) {
                tbodyTurnos.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            No se detectaron turnos en la imagen. Intenta con una foto más clara o mejor iluminada.
                        </td>
                    </tr>`;
                btnConfirmar.disabled = true;
                return;
            }

            btnConfirmar.disabled = false;

            turnos.forEach((t, idx) => {
                const tr = document.createElement('tr');
                tr.dataset.index = idx;

                const fechaPartes = t.fecha ? t.fecha.split('-') : [];
                const fechaFormateada = fechaPartes.length === 3 ? `${fechaPartes[2]}/${fechaPartes[1]}/${fechaPartes[0]}` : t.fecha;

                tr.innerHTML = `
                    <td class="text-center fw-bold text-muted">${idx + 1}</td>
                    <td>
                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">${fechaFormateada}</div>
                        <span class="badge bg-blue-lt text-uppercase" style="font-size: 0.72rem;">${t.dia || 'DÍA'}</span>
                        <input type="hidden" class="ocr-val-fecha" value="${t.fecha}">
                    </td>
                    <td>
                        <select class="form-select form-select-sm ocr-val-recep mb-1">
                            ${catalogoRecepcionistas.map(r => `
                                <option value="${r.id_recepcionista}" ${r.id_recepcionista === t.id_recepcionista ? 'selected' : ''}>
                                    ${r.nombre}
                                </option>
                            `).join('')}
                            <option value="__nuevo__" ${t.es_nuevo ? 'selected' : ''}>
                                ➕ Crear nuevo: "${t.nombre_detectado}"
                            </option>
                        </select>
                        <input type="text" class="form-control form-control-sm ocr-val-nuevo-nombre ${t.es_nuevo ? '' : 'd-none'}"
                               value="${t.nombre_detectado}" placeholder="Nombre para registrar">
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <input type="time" class="form-control form-control-sm p-1 ocr-val-inicio" value="${t.hora_inicio || '08:00'}" style="width: 78px;">
                            <span class="text-muted small">-</span>
                            <input type="time" class="form-control form-control-sm p-1 ocr-val-fin" value="${t.hora_fin || '16:00'}" style="width: 78px;">
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-outline-danger btn-quitar-fila-ocr" title="Descartar este turno">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>`;

                const selectRecep = tr.querySelector('.ocr-val-recep');
                const inputNuevo  = tr.querySelector('.ocr-val-nuevo-nombre');
                selectRecep.addEventListener('change', () => {
                    inputNuevo.classList.toggle('d-none', selectRecep.value !== '__nuevo__');
                });

                tr.querySelector('.btn-quitar-fila-ocr').addEventListener('click', () => {
                    tr.remove();
                    actualizarContadorFilas();
                });

                tbodyTurnos.appendChild(tr);
            });
        }

        function actualizarContadorFilas() {
            const total = tbodyTurnos.querySelectorAll('tr').length;
            totalTurnosBadge.textContent = `${total} turnos`;
            if (total === 0) {
                btnConfirmar.disabled = true;
                tbodyTurnos.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            Has descartado todos los turnos.
                        </td>
                    </tr>`;
            }
        }

        // Volver al Paso 1
        btnVolverPaso1?.addEventListener('click', () => {
            pasoRevision.classList.add('d-none');
            pasoSubir.classList.remove('d-none');
            dropzone.classList.remove('d-none');
            accionesPaso1.classList.remove('d-none');
            loadingZone.classList.add('d-none');
        });

        // Confirmar e importar turnos en lote
        btnConfirmar?.addEventListener('click', () => {
            const filas = tbodyTurnos.querySelectorAll('tr');
            if (filas.length === 0) return;

            const turnosPayload = [];
            filas.forEach(tr => {
                const inputFecha = tr.querySelector('.ocr-val-fecha');
                if (!inputFecha) return;

                const selectRecep = tr.querySelector('.ocr-val-recep');
                const inputNuevo  = tr.querySelector('.ocr-val-nuevo-nombre');
                const horaInicio  = tr.querySelector('.ocr-val-inicio').value;
                const horaFin     = tr.querySelector('.ocr-val-fin').value;

                const esNuevo = selectRecep.value === '__nuevo__';
                const nombreNuevo = esNuevo ? (inputNuevo.value.trim() || 'Recepcionista') : null;
                const nombreRecep = esNuevo ? nombreNuevo : selectRecep.selectedOptions[0]?.text?.trim();

                turnosPayload.push({
                    fecha: inputFecha.value,
                    id_recepcionista: esNuevo ? null : selectRecep.value,
                    nombre_nuevo: nombreNuevo,
                    nombre_recep: nombreRecep,
                    hora_inicio: horaInicio || null,
                    hora_fin: horaFin || null,
                    observaciones: 'Rol de Ventanilla importado desde imagen',
                });
            });

            if (turnosPayload.length === 0) {
                alert('No hay turnos para importar.');
                return;
            }

            btnConfirmar.disabled = true;
            btnVolverPaso1.disabled = true;
            savingZone.classList.remove('d-none');

            fetch('{{ route("turnos.importar-lote") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    turnos: turnosPayload,
                    sobrescribir: checkSobrescribir.checked,
                }),
            })
            .then(async (r) => {
                const res = await r.json();
                if (!r.ok || !res.success) {
                    throw new Error(res.error || 'Error al guardar los turnos.');
                }
                return res;
            })
            .then((res) => {
                window.location.href = res.url || `{{ route('turnos.index') }}?mes=${res.mes}&anio=${res.anio}`;
            })
            .catch((err) => {
                alert('Ocurrió un error al guardar los turnos: ' + err.message);
                btnConfirmar.disabled = false;
                btnVolverPaso1.disabled = false;
                savingZone.classList.add('d-none');
            });
        });
    }
})();
</script>
@endsection
