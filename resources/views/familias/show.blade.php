@extends('layouts.app')

@section('title', 'Ficha Familia #' . $familia->numero_familia)
@section('page_title', 'Ficha del Núcleo Familiar #' . $familia->numero_familia)

@section('content')
<div class="mb-3">
    <a href="{{ route('familias.index') }}" class="btn btn-link text-decoration-none p-0">
        <i class="ti ti-arrow-left me-1"></i> Volver a la lista de familias
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

<div class="row g-3">
    <!-- Detalles de la Familia -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase">No. Familia</span>
                    <h3 class="card-title text-primary fs-2 mb-0">{{ $familia->numero_familia }}</h3>
                </div>
                @if($familia->activo)
                    <span class="badge bg-green-lt">Activa</span>
                @else
                    <span class="badge bg-red-lt">Inactiva</span>
                @endif
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-bold mb-1">Apellido Cabeza de Familia:</label>
                    <div class="fs-3 fw-bold text-dark">{{ $familia->apellido_cabeza }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-bold mb-1">DPI Cabeza de Familia:</label>
                    <div class="fs-4 text-dark">{{ $familia->dpi ?? 'No registrado' }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-bold mb-1">Ubicación Geográfica:</label>
                    <div class="text-dark small">
                        @if($familia->comunidad)
                            <div><strong>Comunidad:</strong> {{ $familia->comunidad->nombre }} {{ $familia->comunidad->zona ? '('.$familia->comunidad->zona.')' : '' }}</div>
                            <div><strong>Municipio:</strong> {{ $familia->comunidad->municipio->nombre ?? 'N/A' }}</div>
                            <div><strong>Departamento:</strong> {{ $familia->comunidad->municipio->departamento->nombre ?? 'N/A' }}</div>
                        @else
                            <em class="text-secondary">Sin asignación territorial</em>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-bold mb-1">Fecha de Registro:</label>
                    <div class="text-dark small">{{ optional($familia->fecha_registro)->format('d/m/Y H:i A') ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('familias.edit', $familia->id_family) }}" class="btn btn-primary flex-fill">
                    <i class="ti ti-edit me-1"></i> Editar
                </a>
                <form action="{{ route('familias.toggle-status', $familia->id_family) }}" method="POST" class="flex-fill">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn {{ $familia->activo ? 'btn-secondary' : 'btn-success' }} w-100">
                        {{ $familia->activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Integrantes de la Familia -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title"><i class="ti ti-users me-2"></i> Integrantes Registrados</h3>
                    <p class="card-subtitle text-secondary mb-0">Pacientes adscritos a este núcleo familiar.</p>
                </div>
                <a href="{{ route('pacientes.create', ['id_family' => $familia->id_family]) }}" class="btn btn-sm btn-primary">
                    <i class="ti ti-plus me-1"></i> Agregar Paciente
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Expediente Físico</th>
                            <th>Edad / Sexo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($familia->pacientes as $paciente)
                            <tr>
                                <td>
                                    <strong class="text-dark">{{ $paciente->nombres }} {{ $paciente->apellidos }}</strong>
                                    @if($paciente->dpi)
                                        <div class="text-secondary small">DPI: {{ $paciente->dpi }}</div>
                                    @endif
                                </td>
                                <td><span class="badge bg-blue-lt">{{ $paciente->numero_expediente_fisico }}</span></td>
                                <td>
                                    {{ optional($paciente->fecha_nacimiento)->age }} años
                                    <span class="text-secondary">({{ $paciente->sexo === 'M' ? 'M' : 'F' }})</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('pacientes.show', $paciente->id_paciente) }}" class="btn btn-sm btn-info">
                                        <i class="ti ti-eye me-1"></i> Ficha
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">
                                    No hay pacientes registrados en este núcleo familiar aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
