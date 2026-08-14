@extends('layouts.app')

@section('title', 'Registro de Pacientes')
@section('page_title', 'Gestión de Pacientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1 text-dark">Pacientes Registrados</h3>
        <p class="text-secondary mb-0 small">Consulte expedientes y realice búsquedas por DPI, nombre o expediente físico.</p>
    </div>
    <a href="{{ route('pacientes.create') }}" class="btn btn-primary">
        <i class="ti ti-user-plus me-1"></i> Registrar Nuevo Paciente
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div><i class="ti ti-check me-2 fs-2"></i></div>
            <div>{{ session('success') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

<!-- Filtros de búsqueda Tabler -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('pacientes.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label" for="buscar">Buscar Paciente / DPI / Expediente</label>
                <input type="text" id="buscar" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Nombre, DPI o Expediente...">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="sexo">Sexo</label>
                <select id="sexo" name="sexo" class="form-select">
                    <option value="">-- Todos --</option>
                    <option value="M" {{ request('sexo') === 'M' ? 'selected' : '' }}>Masculino (M)</option>
                    <option value="F" {{ request('sexo') === 'F' ? 'selected' : '' }}>Femenino (F)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="estado">Estado</label>
                <select id="estado" name="estado" class="form-select">
                    <option value="">-- Todos --</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="ti ti-search me-1"></i> Buscar</button>
                <a href="{{ route('pacientes.index') }}" class="btn btn-secondary"><i class="ti ti-rotate-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Pacientes -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>No. Expediente</th>
                    <th>Nombre Completo</th>
                    <th>DPI</th>
                    <th>Familia / Comunidad</th>
                    <th>Edad / Sexo</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pacientes as $paciente)
                    <tr>
                        <td><strong class="text-primary">{{ $paciente->numero_expediente_fisico }}</strong></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $paciente->nombres }} {{ $paciente->apellidos }}</div>
                        </td>
                        <td>{{ $paciente->dpi ?? 'Sin DPI' }}</td>
                        <td>
                            @if($paciente->familia)
                                <a href="{{ route('familias.show', $paciente->familia->id_family) }}" class="fw-bold text-decoration-none">
                                    {{ $paciente->familia->numero_familia }} ({{ $paciente->familia->apellido_cabeza }})
                                </a>
                                @if($paciente->familia->comunidad)
                                    <div class="text-secondary small">{{ $paciente->familia->comunidad->nombre }}</div>
                                @endif
                            @else
                                <span class="text-muted">Sin familia</span>
                            @endif
                        </td>
                        <td>
                            {{ optional($paciente->fecha_nacimiento)->age }} años
                            <span class="text-secondary">({{ $paciente->sexo }})</span>
                        </td>
                        <td>{{ $paciente->telefono ?? 'N/A' }}</td>
                        <td>
                            @if($paciente->activo)
                                <span class="badge bg-green-lt">Activo</span>
                            @else
                                <span class="badge bg-red-lt">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-list justify-content-end">
                                <a href="{{ route('pacientes.show', $paciente->id_paciente) }}" class="btn btn-sm btn-info">
                                    <i class="ti ti-eye me-1"></i> Ficha
                                </a>
                                <a href="{{ route('pacientes.edit', $paciente->id_paciente) }}" class="btn btn-sm btn-warning">
                                    <i class="ti ti-edit me-1"></i> Editar
                                </a>
                                <form action="{{ route('pacientes.toggle-status', $paciente->id_paciente) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $paciente->activo ? 'btn-secondary' : 'btn-success' }}">
                                        {{ $paciente->activo ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">
                            No se encontraron pacientes registrados con los filtros aplicados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $pacientes->links() }}
</div>
@endsection
