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
        <a href="{{ route('turnos.create') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-plus me-1"></i> Nuevo Turno
        </a>
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
                            <a href="{{ route('turnos.edit', $turno->id_turno) }}"
                               class="btn btn-sm btn-outline-secondary" title="Editar">
                                <i class="ti ti-edit"></i>
                            </a>
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
@endsection
