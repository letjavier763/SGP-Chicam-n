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

<div class="row g-3">

    {{-- ============================================================
         COLUMNA IZQUIERDA: Selección de turno + Registro
    ============================================================ --}}
    <div class="col-lg-5">

        {{-- Selector de turno del día --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="ti ti-calendar-event me-2 text-primary"></i>
                    Turno Activo — {{ now()->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                </h3>
            </div>
            <div class="card-body">
                @if($turnosHoy->isEmpty())
                    <div class="text-center text-secondary py-3">
                        <i class="ti ti-calendar-off fs-2 d-block mb-2"></i>
                        No hay turnos registrados para hoy.
                        @if(Auth::user()->esAdministrador())
                        <a href="{{ route('turnos.create') }}" class="btn btn-sm btn-primary mt-2">
                            <i class="ti ti-plus me-1"></i> Crear Turno
                        </a>
                        @endif
                    </div>
                @else
                    <form method="GET" action="{{ route('ventanilla.index') }}" class="d-flex gap-2">
                        <select name="turno_id" class="form-select" onchange="this.form.submit()">
                            @foreach($turnosHoy as $t)
                                <option value="{{ $t->id_turno }}"
                                    {{ $turnoActivo && $turnoActivo->id_turno == $t->id_turno ? 'selected' : '' }}>
                                    {{ ucfirst($t->tipo_turno) }} — {{ $t->usuario->nombre_completo }}
                                    ({{ \Carbon\Carbon::parse($t->hora_inicio)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                @if($turnoActivo)
                <div class="mt-3 d-flex gap-2 flex-wrap">
                    @php
                        $colorTurno = match($turnoActivo->tipo_turno) {
                            'matutino'   => 'warning',
                            'vespertino' => 'primary',
                            'nocturno'   => 'dark',
                            default      => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $colorTurno }}-lt text-{{ $colorTurno }} fs-6 px-3 py-2">
                        <i class="ti ti-clock me-1"></i>
                        {{ ucfirst($turnoActivo->tipo_turno) }}:
                        {{ \Carbon\Carbon::parse($turnoActivo->hora_inicio)->format('H:i') }}
                        – {{ \Carbon\Carbon::parse($turnoActivo->hora_fin)->format('H:i') }}
                    </span>
                    <span class="badge bg-blue-lt text-blue fs-6 px-3 py-2">
                        <i class="ti ti-users me-1"></i>
                        {{ $llegadas->count() }} llegadas
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Formulario de búsqueda y registro de llegada --}}
        @if($turnoActivo)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="ti ti-user-search me-2 text-success"></i> Registrar Llegada de Paciente
                </h3>
            </div>
            <div class="card-body">
                {{-- Barra de búsqueda rápida inteligente --}}
                <div class="mb-3 position-relative">
                    <input type="text" id="buscar_paciente" class="form-control form-control-lg"
                           placeholder="Nombre, DPI, expediente o familia…" autocomplete="off">
                    <div id="search-suggestions"
                         style="display:none; position:absolute; top:100%; left:0; right:0; max-height:420px;
                                overflow-y:auto; z-index:1060; background:#fff;
                                border:1px solid #cbd5e1; border-radius:0 0 8px 8px; box-shadow:0 8px 24px rgba(0,0,0,.12);">
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="ti ti-list-check me-2 text-primary"></i>
                    Llegadas de Este Turno
                </h3>
                @if($turnoActivo && $llegadas->isNotEmpty())
                <a href="{{ route('reportes.diario', $turnoActivo->id_turno) }}"
                   class="btn btn-sm btn-outline-success">
                    <i class="ti ti-chart-bar me-1"></i> Ver Reporte
                </a>
                @endif
            </div>

            @if(!$turnoActivo)
            <div class="card-body text-center text-secondary py-5">
                <i class="ti ti-door-off fs-1 d-block mb-3 opacity-50"></i>
                <p>Seleccione un turno para ver los registros de llegada.</p>
            </div>
            @elseif($llegadas->isEmpty())
            <div class="card-body text-center text-secondary py-5">
                <i class="ti ti-users fs-1 d-block mb-3 opacity-50"></i>
                <p>Aún no hay llegadas registradas en este turno.</p>
                <p class="small">Use el panel izquierdo para buscar y registrar pacientes.</p>
            </div>
            @else
            <div class="table-responsive">
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
            <form action="{{ route('pacientes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="desde_ventanilla" value="1">
                <input type="hidden" name="turno_id" value="{{ $turnoActivo->id_turno }}">
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
                            <label class="form-label required" for="nv_numero_familia">Número de Familia</label>
                            {{-- Campo oculto para id si la familia existe --}}
                            <input type="hidden" id="nv_id_family" name="id_family">
                            <div class="position-relative">
                                <input type="text" id="nv_numero_familia" name="numero_familia"
                                       class="form-control" required autocomplete="off"
                                       placeholder="Ej: F-001 (existente o nuevo)">
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
                                <select id="nv_id_muni" class="form-select form-select-sm" disabled>
                                    <option value="">-- Depto Primero --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required small mb-1" for="nv_id_comunidad">Comunidad</label>
                                <select id="nv_id_comunidad" name="id_comunidad" class="form-select form-select-sm" disabled>
                                    <option value="">-- Muni Primero --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nv_expediente">No. Expediente Físico</label>
                            <input type="text" id="nv_expediente" name="numero_expediente_fisico" class="form-control" placeholder="Se auto-rellena con el número de familia">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="ti ti-login me-1"></i> Guardar y Registrar Primera Llegada</button>
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
                <input type="hidden" name="turno_id" value="{{ $turnoActivo->id_turno }}">
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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buscarInput   = document.getElementById('buscar_paciente');
    const suggestions   = document.getElementById('search-suggestions');
    const turnoId       = {{ $turnoActivo->id_turno }};
    const storePath     = '{{ route('ventanilla.store') }}';
    const buscarPath    = '{{ route('ventanilla.buscar') }}';
    const csrfToken     = '{{ csrf_token() }}';
    let timer;

    // ── Ocultar al clic fuera ──────────────────────────────────────
    document.addEventListener('click', e => {
        if (!buscarInput.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.style.display = 'none';
        }
    });
    suggestions.addEventListener('click', e => e.stopPropagation());

    // ── Buscador inteligente ───────────────────────────────────────
    buscarInput.addEventListener('input', function () {
        const q = this.value.trim();
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

    function renderSuggestions(data, q) {
        suggestions.innerHTML = '';

        if (!data.length) {
            // ─ Sin resultados: ofrecer registro de nuevo paciente ─
            suggestions.innerHTML = `
                <div class="p-3 text-center border-bottom">
                    <i class="ti ti-user-off d-block fs-3 text-muted mb-1"></i>
                    <p class="text-muted small mb-2">No se encontró ningún paciente con <strong>"${q}"</strong></p>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoPaciente">
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
                    <span class="badge bg-success-lt text-success">
                        <i class="ti ti-check me-1"></i>Ya registrado en este turno
                    </span>`;
            } else {
                actionBlock = `
                    <form action="${storePath}" method="POST" class="d-flex align-items-center gap-2 flex-wrap mt-1">
                        <input type="hidden" name="_token"      value="${csrfToken}">
                        <input type="hidden" name="id_turno"    value="${turnoId}">
                        <input type="hidden" name="id_paciente" value="${p.id_paciente}">
                        <input type="time" name="hora_llegada" value="${nowHHMM}" class="form-control form-control-sm" style="width:120px" required>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="es_nuevo" value="1" id="nuevo_${p.id_paciente}">
                            <label class="form-check-label small" for="nuevo_${p.id_paciente}">1ª visita</label>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="ti ti-login me-1"></i>Registrar
                        </button>
                    </form>`;
            }

            const famInfo = p.familia_numero
                ? `<span class="badge bg-blue-lt text-blue me-1">Fam. ${p.familia_numero}</span>${p.comunidad ?? ''}`
                : '<span class="text-muted">Sin familia</span>';

            const item = document.createElement('div');
            item.className = 'p-3 border-bottom' + (p.ya_registrado ? ' bg-success-lt' : '');
            item.style.transition = 'background .15s';
            item.innerHTML = `
                <div class="d-flex align-items-start gap-3">
                    <span class="avatar avatar-sm bg-${sexColor}-lt text-${sexColor} rounded-circle flex-shrink-0">
                        <i class="ti ti-${sexIcon}"></i>
                    </span>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${p.nombres} ${p.apellidos}
                            <small class="text-muted fw-normal ms-1">${p.edad ?? '?'} años · ${p.sexo}</small>
                        </div>
                        <div class="small text-secondary mb-1">
                            Exp: <strong>${p.numero_expediente_fisico}</strong>
                            ${p.dpi ? ' · DPI: ' + p.dpi : ''}
                        </div>
                        <div class="small">${famInfo}</div>
                        ${actionBlock}
                    </div>
                </div>`;
            suggestions.appendChild(item);
        });

        // Botón al final para nuevo paciente si no se encontró lo que se busca
        const footer = document.createElement('div');
        footer.className = 'p-2 text-center bg-light';
        footer.innerHTML = `
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoPaciente">
                <i class="ti ti-user-plus me-1"></i>¿No está en la lista? Registrar nuevo
            </button>`;
        suggestions.appendChild(footer);

        suggestions.style.display = 'block';
    }

    // ── Modal de Confirmación de Anulación ─────────────────────────
    document.querySelectorAll('.btn-anular').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('formAnularLlegada').action = this.getAttribute('data-action');
        });
    });

    // ── Modal Nuevo Paciente: ubicación cascada ─────────────────────────────
    function setupCascadingUbicaciones(deptoId, muniId, comId) {
        const deptoSelect = document.getElementById(deptoId);
        const muniSelect = document.getElementById(muniId);
        const comSelect = document.getElementById(comId);

        if (deptoSelect) {
            deptoSelect.addEventListener('change', function () {
                const val = this.value;
                muniSelect.innerHTML = '<option value="">-- Cargando... --</option>';
                muniSelect.disabled = true;
                comSelect.innerHTML = '<option value="">-- Depto Primero --</option>';
                comSelect.disabled = true;
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
                if (!val) return;

                fetch(`/api/ubicaciones/comunidades/${val}`)
                    .then(res => res.json())
                    .then(data => {
                        comSelect.innerHTML = '<option value="">-- Seleccionar Comunidad --</option>';
                        data.forEach(c => {
                            const zonaText = c.zona ? ` (${c.zona})` : '';
                            comSelect.innerHTML += `<option value="${c.id_comunidad}">${c.nombre}${zonaText}</option>`;
                        });
                        comSelect.disabled = false;
                    });
            });
        }
    }

    setupCascadingUbicaciones('nv_id_depto', 'nv_id_muni', 'nv_id_comunidad');

    // ── Modal Nuevo Paciente: autocompletado de familia ──────────────────────
    const nvNumFamInput  = document.getElementById('nv_numero_familia');
    const nvIdFamInput   = document.getElementById('nv_id_family');
    const nvFamSug       = document.getElementById('nv_familia_suggestions');
    const nvFamStatus    = document.getElementById('nv_familia_status');
    const nvExpediente   = document.getElementById('nv_expediente');
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
            // Auto-rellenar expediente con el número
            if (nvExpediente && !nvExpediente.value) nvExpediente.value = q;
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
                if (nvExpediente && !nvExpediente.value) nvExpediente.value = f.numero_familia;
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

    // ── Modal Nuevo Paciente: verificar DPI duplicado ───────────────
    const nvDpi = document.getElementById('nv_dpi');
    const nvDpiMsg = document.getElementById('msg-nv-dpi');
    if (nvDpi && nvDpiMsg) {
        nvDpi.addEventListener('blur', function () {
            const val = this.value.trim();
            if (!val) { nvDpiMsg.textContent = ''; return; }
            fetch(`/pacientes/verificar-duplicado?tipo=dpi&valor=${encodeURIComponent(val)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.duplicate) {
                        nvDpiMsg.className = 'form-hint text-danger fw-bold';
                        nvDpiMsg.textContent = '⚠ ' + data.message;
                    } else {
                        nvDpiMsg.className = 'form-hint text-success fw-bold';
                        nvDpiMsg.textContent = '✓ Disponible';
                    }
                });
        });
    }
});
</script>
@endsection
