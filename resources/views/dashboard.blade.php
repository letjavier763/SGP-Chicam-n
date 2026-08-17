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
            Ventanilla
        </h4>
        <div class="d-flex flex-wrap gap-2 mb-3">
            @if($turnoHoy)
            <a href="{{ route('ventanilla.index', ['turno_id' => $turnoHoy->id_turno]) }}"
               class="btn btn-primary">
                Ir a mi Ventanilla (Turno {{ ucfirst($turnoHoy->tipo_turno) }})
            </a>
            @else
            <a href="{{ route('ventanilla.index') }}" class="btn btn-outline-primary">
                Ir a Ventanilla
            </a>
            @endif
            <a href="{{ route('turnos.index') }}" class="btn btn-outline-secondary">
                Ver Turnos
            </a>
            @if(Auth::user()->esAdministrador())
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalCrearTurno">
                Nuevo Turno
            </button>
            @endif
        </div>
        @endif

        {{-- Registros --}}
        @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
        <h4 class="text-uppercase text-secondary small fw-bold mt-3 mb-2">
            Registros
        </h4>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('pacientes.index') }}" class="btn btn-outline-primary">
                Pacientes
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCrearPaciente">
                Nuevo Paciente
            </button>
            <a href="{{ route('familias.index') }}" class="btn btn-outline-secondary">
                Núcleos Familiares
            </a>
        </div>
        @endif

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
                        <div class="col-md-12">
                            <label for="create_turno_usuario" class="form-label required">Personal Asignado</label>
                            <select id="create_turno_usuario" name="id_usuario" class="form-select" required>
                                <option value="">— Seleccione un usuario —</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id_usuario }}">
                                        {{ $u->nombre_completo }} ({{ $u->rol->nombre_rol }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="create_turno_fecha" class="form-label required">Fecha del Turno</label>
                            <input type="date" id="create_turno_fecha" name="fecha" class="form-control" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_tipo_turno" class="form-label required">Tipo de Turno</label>
                            <select id="create_tipo_turno" name="tipo_turno" class="form-select" required>
                                <option value="">— Seleccione —</option>
                                <option value="matutino">Matutino (6:00 – 14:00)</option>
                                <option value="vespertino">Vespertino (14:00 – 22:00)</option>
                                <option value="nocturno">Nocturno (22:00 – 6:00)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="create_hora_inicio" class="form-label required">Hora de Inicio</label>
                            <input type="time" id="create_hora_inicio" name="hora_inicio" class="form-control" value="06:00" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_hora_fin" class="form-label required">Hora de Fin</label>
                            <input type="time" id="create_hora_fin" name="hora_fin" class="form-control" value="14:00" required>
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
                        <div class="col-md-8">
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
                        <div class="col-md-4">
                            <label class="form-label" for="create_numero_expediente_fisico">No. Expediente Físico</label>
                            <input type="text" id="create_numero_expediente_fisico" name="numero_expediente_fisico" class="form-control bg-light" readonly placeholder="Se asigna según la familia">
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

    // Auto-completar número de expediente del paciente
    const familySelect = document.getElementById('create_id_family');
    const expInput = document.getElementById('create_numero_expediente_fisico');
    if (familySelect) {
        familySelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            expInput.value = opt ? (opt.dataset.numeroFamilia || '') : '';
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
