@extends('layouts.app')

@section('title', 'Núcleos Familiares')
@section('page_title', 'Gestión de Núcleos Familiares')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1 text-dark">Núcleos Familiares Registrados</h3>
        <p class="text-secondary mb-0 small">Consulte y gestione las familias de la comunidad de Chicamán y sus alrededores.</p>
    </div>
    <a href="{{ route('familias.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Registrar Nueva Familia
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
        <form method="GET" action="{{ route('familias.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="buscar">Buscar No. Familia / Cabeza / DPI</label>
                <input type="text" id="buscar" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Ej: FAM-001 o Pérez...">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="id_comunidad">Comunidad</label>
                <select id="id_comunidad" name="id_comunidad" class="form-select">
                    <option value="">-- Todas las comunidades --</option>
                    @foreach($comunidades as $com)
                        <option value="{{ $com->id_comunidad }}" {{ request('id_comunidad') == $com->id_comunidad ? 'selected' : '' }}>
                            {{ $com->nombre }} {{ $com->zona ? '('.$com->zona.')' : '' }}
                        </option>
                    @endforeach
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
                <button type="submit" class="btn btn-primary flex-fill"><i class="ti ti-search me-1"></i> Filtrar</button>
                <a href="{{ route('familias.index') }}" class="btn btn-secondary"><i class="ti ti-rotate-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Familias -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>No. Familia</th>
                    <th>Apellido Cabeza</th>
                    <th>DPI Cabeza</th>
                    <th>Comunidad / Municipio</th>
                    <th>Integrantes</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($familias as $familia)
                    <tr>
                        <td><strong class="text-primary">{{ $familia->numero_familia }}</strong></td>
                        <td>{{ $familia->apellido_cabeza }}</td>
                        <td>{{ $familia->dpi ?? 'No registrado' }}</td>
                        <td>
                            @if($familia->comunidad)
                                {{ $familia->comunidad->nombre }}
                                <div class="text-secondary small">{{ $familia->comunidad->municipio->nombre ?? '' }}</div>
                            @else
                                <span class="text-muted">Sin ubicación</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-blue-lt">
                                <i class="ti ti-users me-1"></i> {{ $familia->pacientes->count() }} miembros
                            </span>
                        </td>
                        <td>
                            @if($familia->activo)
                                <span class="badge bg-green-lt">Activo</span>
                            @else
                                <span class="badge bg-red-lt">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-list justify-content-end">
                                <a href="{{ route('familias.show', $familia->id_family) }}" class="btn btn-sm btn-info">
                                    <i class="ti ti-eye me-1"></i> Ficha
                                </a>
                                <a href="{{ route('familias.edit', $familia->id_family) }}" class="btn btn-sm btn-warning">
                                    <i class="ti ti-edit me-1"></i> Editar
                                </a>
                                <form action="{{ route('familias.toggle-status', $familia->id_family) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $familia->activo ? 'btn-secondary' : 'btn-success' }}">
                                        {{ $familia->activo ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">
                            No se encontraron núcleos familiares con los criterios especificados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $familias->links() }}
</div>
@endsection
