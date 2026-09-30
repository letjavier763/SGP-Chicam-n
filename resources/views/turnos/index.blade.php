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
        <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
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
})();
</script>
@endsection
