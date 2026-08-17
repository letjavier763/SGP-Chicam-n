@extends('layouts.app')

@section('title', 'Turnos del Personal')
@section('page_title', 'Módulo de Ventanilla — Turnos del Personal')

@section('content')
{{-- Barra de filtros + botón crear --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">
            <i class="ti ti-clock me-2 text-primary"></i> Turnos Registrados
        </h3>
        @if(Auth::user()->esAdministrador())
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearTurno">
            <i class="ti ti-plus me-1"></i> Nuevo Turno
        </button>
        @endif
    </div>
    <div class="card-body border-bottom py-3">
        <form method="GET" action="{{ route('turnos.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-secondary small">Filtrar por Fecha</label>
                <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label text-secondary small">Tipo de Turno</label>
                <select name="tipo_turno" class="form-select">
                    <option value="">Todos</option>
                    <option value="matutino"   {{ request('tipo_turno') === 'matutino'   ? 'selected' : '' }}>Matutino</option>
                    <option value="vespertino" {{ request('tipo_turno') === 'vespertino' ? 'selected' : '' }}>Vespertino</option>
                    <option value="nocturno"   {{ request('tipo_turno') === 'nocturno'   ? 'selected' : '' }}>Nocturno</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="ti ti-filter me-1"></i> Filtrar
                </button>
                <a href="{{ route('turnos.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ti ti-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ti ti-alert-circle me-2"></i> {{ session('error') }}
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

{{-- Tabla de turnos --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Personal</th>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Horario</th>
                    <th>Observaciones</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($turnos as $turno)
                <tr>
                    <td class="text-secondary">{{ $turno->id_turno }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-blue-lt text-blue rounded-circle fw-bold">
                                {{ strtoupper(substr($turno->usuario->nombre_completo, 0, 2)) }}
                            </span>
                            <div>
                                <div class="fw-medium">{{ $turno->usuario->nombre_completo }}</div>
                                <div class="text-secondary small">{{ $turno->usuario->rol->nombre_rol }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="fw-medium">{{ $turno->fecha->format('d/m/Y') }}</span>
                        @if($turno->fecha->isToday())
                            <span class="badge bg-green-lt text-green ms-1">Hoy</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeColor = match($turno->tipo_turno) {
                                'matutino'   => 'warning',
                                'vespertino' => 'primary',
                                'nocturno'   => 'dark',
                                default      => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $badgeColor }}-lt text-{{ $badgeColor }} text-capitalize">
                            {{ $turno->tipo_turno }}
                        </span>
                    </td>
                    <td>
                        <i class="ti ti-clock text-secondary me-1"></i>
                        {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}
                        — {{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}
                    </td>
                    <td class="text-secondary small">
                        {{ Str::limit($turno->observaciones, 50, '…') ?: '—' }}
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1 flex-wrap">
                            <a href="{{ route('ventanilla.index', ['turno_id' => $turno->id_turno]) }}"
                               class="btn btn-sm btn-outline-primary" title="Ir a Ventanilla">
                                <i class="ti ti-door-enter me-1"></i>Ventanilla
                            </a>
                            <a href="{{ route('reportes.diario', $turno->id_turno) }}"
                               class="btn btn-sm btn-outline-success" title="Ver Reporte">
                                <i class="ti ti-chart-bar me-1"></i>Reporte
                            </a>
                            @if(Auth::user()->esAdministrador())
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-editar-turno"
                                    title="Editar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditarTurno"
                                    data-id="{{ $turno->id_turno }}"
                                    data-usuario="{{ $turno->id_usuario }}"
                                    data-fecha="{{ $turno->fecha->format('Y-m-d') }}"
                                    data-tipo="{{ $turno->tipo_turno }}"
                                    data-inicio="{{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}"
                                    data-fin="{{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}"
                                    data-obs="{{ $turno->observaciones }}">
                                <i class="ti ti-edit"></i>
                            </button>
                            <form action="{{ route('turnos.destroy', $turno->id_turno) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este turno?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-secondary py-5">
                        <i class="ti ti-calendar-off fs-2 d-block mb-2"></i>
                        No se encontraron turnos con los filtros aplicados.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($turnos->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Mostrando {{ $turnos->firstItem() }}–{{ $turnos->lastItem() }} de {{ $turnos->total() }} turnos
        </p>
        <ul class="pagination m-0 ms-auto">
            {{ $turnos->links('pagination::bootstrap-5') }}
        </ul>
    </div>
    @endif
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

{{-- MODAL EDITAR TURNO --}}
<div class="modal fade" id="modalEditarTurno" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="ti ti-edit me-2"></i> Editar Turno de Personal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="formEditarTurno">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="edit_turno_usuario" class="form-label required">Personal Asignado</label>
                            <select id="edit_turno_usuario" name="id_usuario" class="form-select" required>
                                <option value="">— Seleccione un usuario —</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id_usuario }}">
                                        {{ $u->nombre_completo }} ({{ $u->rol->nombre_rol }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_turno_fecha" class="form-label required">Fecha del Turno</label>
                            <input type="date" id="edit_turno_fecha" name="fecha" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_tipo_turno" class="form-label required">Tipo de Turno</label>
                            <select id="edit_tipo_turno" name="tipo_turno" class="form-select" required>
                                <option value="">— Seleccione —</option>
                                <option value="matutino">Matutino (6:00 – 14:00)</option>
                                <option value="vespertino">Vespertino (14:00 – 22:00)</option>
                                <option value="nocturno">Nocturno (22:00 – 6:00)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_hora_inicio" class="form-label required">Hora de Inicio</label>
                            <input type="time" id="edit_hora_inicio" name="hora_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_hora_fin" class="form-label required">Hora de Fin</label>
                            <input type="time" id="edit_hora_fin" name="hora_fin" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label for="edit_turno_obs" class="form-label">Observaciones</label>
                            <textarea id="edit_turno_obs" name="observaciones" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="ti ti-device-floppy me-1"></i> Actualizar Turno</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const horarios = {
        matutino:   { inicio: '06:00', fin: '14:00' },
        vespertino: { inicio: '14:00', fin: '22:00' },
        nocturno:   { inicio: '22:00', fin: '06:00' },
    };

    // Auto-rellenar horas en Crear Turno
    const createTipoSelect = document.getElementById('create_tipo_turno');
    if (createTipoSelect) {
        createTipoSelect.addEventListener('change', function () {
            const sel = horarios[this.value];
            if (sel) {
                document.getElementById('create_hora_inicio').value = sel.inicio;
                document.getElementById('create_hora_fin').value    = sel.fin;
            }
        });
    }

    // Auto-rellenar horas en Editar Turno
    const editTipoSelect = document.getElementById('edit_tipo_turno');
    if (editTipoSelect) {
        editTipoSelect.addEventListener('change', function () {
            const sel = horarios[this.value];
            if (sel) {
                document.getElementById('edit_hora_inicio').value = sel.inicio;
                document.getElementById('edit_hora_fin').value    = sel.fin;
            }
        });
    }

    // Llenar Modal de Editar Turno
    document.querySelectorAll('.btn-editar-turno').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const form = document.getElementById('formEditarTurno');
            form.action = `/turnos/${id}`;
            document.getElementById('edit_turno_usuario').value = this.getAttribute('data-usuario');
            document.getElementById('edit_turno_fecha').value = this.getAttribute('data-fecha');
            document.getElementById('edit_tipo_turno').value = this.getAttribute('data-tipo');
            document.getElementById('edit_hora_inicio').value = this.getAttribute('data-inicio');
            document.getElementById('edit_hora_fin').value = this.getAttribute('data-fin');
            document.getElementById('edit_turno_obs').value = this.getAttribute('data-obs') || '';
        });
    });
});
</script>
@endsection
