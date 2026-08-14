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
                {{-- Buscar paciente --}}
                <form method="GET" action="{{ route('ventanilla.index') }}" class="mb-3">
                    <input type="hidden" name="turno_id" value="{{ $turnoActivo->id_turno }}">
                    <div class="input-group">
                        <input type="text" name="buscar_paciente" class="form-control"
                               placeholder="Buscar por nombre, DPI o expediente…"
                               value="{{ $buscarPaciente }}">
                        <button type="submit" class="btn btn-secondary">
                            <i class="ti ti-search"></i>
                        </button>
                        @if($buscarPaciente)
                        <a href="{{ route('ventanilla.index', ['turno_id' => $turnoActivo->id_turno]) }}"
                           class="btn btn-outline-secondary">
                            <i class="ti ti-x"></i>
                        </a>
                        @endif
                    </div>
                </form>

                {{-- Resultados de búsqueda --}}
                @if($buscarPaciente && $pacientes->isEmpty())
                    <div class="text-secondary small text-center py-3">
                        <i class="ti ti-user-off d-block fs-2 mb-1"></i>
                        No se encontraron pacientes activos con ese criterio.
                    </div>
                @elseif($pacientes->isNotEmpty())
                    <div class="list-group list-group-flush mb-3" id="lista-pacientes-busqueda">
                        @foreach($pacientes as $paciente)
                        <div class="list-group-item list-group-item-action px-2 py-2" id="pac-{{ $paciente->id_paciente }}">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="avatar avatar-sm bg-{{ $paciente->sexo === 'F' ? 'pink' : 'blue' }}-lt
                                      text-{{ $paciente->sexo === 'F' ? 'pink' : 'blue' }} rounded-circle">
                                    <i class="ti ti-user{{ $paciente->sexo === 'F' ? '-female' : '' }}"></i>
                                </span>
                                <div>
                                    <div class="fw-medium small">{{ $paciente->nombres }} {{ $paciente->apellidos }}</div>
                                    <div class="text-secondary" style="font-size:0.78rem">
                                        Exp: {{ $paciente->numero_expediente_fisico }}
                                        @if($paciente->dpi) · DPI: {{ $paciente->dpi }} @endif
                                    </div>
                                </div>
                            </div>
                            {{-- Mini formulario de registro --}}
                            <form action="{{ route('ventanilla.store') }}" method="POST" class="d-flex gap-2 align-items-end flex-wrap">
                                @csrf
                                <input type="hidden" name="id_turno"    value="{{ $turnoActivo->id_turno }}">
                                <input type="hidden" name="id_paciente" value="{{ $paciente->id_paciente }}">
                                <div class="flex-grow-1">
                                    <label class="form-label mb-0 small text-secondary">Hora llegada</label>
                                    <input type="time" name="hora_llegada" class="form-control form-control-sm"
                                           value="{{ now()->format('H:i') }}" required>
                                </div>
                                <div class="form-check form-switch mt-3 me-1">
                                    <input class="form-check-input" type="checkbox"
                                           name="es_nuevo" id="nuevo_{{ $paciente->id_paciente }}" value="1">
                                    <label class="form-check-label small" for="nuevo_{{ $paciente->id_paciente }}">
                                        1ª visita
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ti ti-check me-1"></i>Registrar
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                @endif

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
                                <form action="{{ route('ventanilla.destroy', $reg->id_registro) }}" method="POST"
                                      onsubmit="return confirm('¿Anular este registro de llegada?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="turno_id" value="{{ $turnoActivo->id_turno }}">
                                    <button type="submit" class="btn btn-xs btn-outline-danger" title="Anular">
                                        <i class="ti ti-trash" style="font-size:.85rem"></i>
                                    </button>
                                </form>
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
@endsection
