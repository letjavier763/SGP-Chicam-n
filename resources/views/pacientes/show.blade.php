@extends('layouts.app')

@section('title', 'Ficha Paciente: ' . $paciente->nombres . ' ' . $paciente->apellidos)
@section('page_title', 'Ficha del Paciente')

@section('content')
<div class="mb-3">
    <a href="{{ route('pacientes.index') }}" class="btn btn-link text-decoration-none p-0">
        <i class="ti ti-arrow-left me-1"></i> Volver a la lista de pacientes
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
    <!-- Ficha Personal del Paciente -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase">No. Expediente / Familia</span>
                    <h3 class="card-title text-primary fs-2 mb-0">{{ $paciente->numero_expediente_fisico }}</h3>
                </div>
                @if($paciente->activo)
                    <span class="badge bg-green-lt">Activo</span>
                @else
                    <span class="badge bg-red-lt">Inactivo</span>
                @endif
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-bold mb-1">Nombre Completo:</label>
                    <div class="fs-3 fw-bold text-dark">{{ $paciente->nombres }} {{ $paciente->apellidos }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-bold mb-1">DPI:</label>
                    <div class="fs-4 text-dark">{{ $paciente->dpi ?? 'No posee / No registrado' }}</div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label text-secondary small fw-bold mb-1">Edad / Sexo:</label>
                        <div class="text-dark small">
                            {{ optional($paciente->fecha_nacimiento)->age }} años ({{ $paciente->sexo === 'M' ? 'M' : 'F' }})
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-secondary small fw-bold mb-1">Teléfono:</label>
                        <div class="text-dark small">{{ $paciente->telefono ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-bold mb-1">Fecha Nacimiento:</label>
                    <div class="text-dark small">{{ optional($paciente->fecha_nacimiento)->format('d/m/Y') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-bold mb-1">Fecha Registro en SGP:</label>
                    <div class="text-dark small">{{ optional($paciente->fecha_registro)->format('d/m/Y H:i A') ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('pacientes.edit', $paciente->id_paciente) }}" class="btn btn-primary flex-fill">
                    <i class="ti ti-edit me-1"></i> Editar Ficha
                </a>
                <form action="{{ route('pacientes.toggle-status', $paciente->id_paciente) }}" method="POST" class="flex-fill">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn {{ $paciente->activo ? 'btn-secondary' : 'btn-success' }} w-100">
                        {{ $paciente->activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Contenido Adicional: Núcleo Familiar e Historial de Visitas -->
    <div class="col-lg-8">
        <div class="d-flex flex-column gap-3">
            
            <!-- Ficha de la Familia -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-primary"><i class="ti ti-home-heart me-2"></i> Núcleo Familiar Adscrito</h3>
                </div>
                <div class="card-body">
                    @if($paciente->familia)
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-primary mb-1">Familia #{{ $paciente->familia->numero_familia }}</h4>
                                <div class="text-secondary small">
                                    <strong>Cabeza de Familia:</strong> {{ $paciente->familia->apellido_cabeza }}<br>
                                    <strong>Comunidad:</strong> {{ $paciente->familia->comunidad->nombre ?? 'N/A' }}
                                </div>
                            </div>
                            <a href="{{ route('familias.show', $paciente->familia->id_family) }}" class="btn btn-outline-primary">
                                <i class="ti ti-eye me-1"></i> Ver Ficha Familiar
                            </a>
                        </div>
                    @else
                        <p class="text-secondary mb-0">El paciente no posee un núcleo familiar asignado.</p>
                    @endif
                </div>
            </div>

            <!-- Historial de Visitas / Ventanilla -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-history me-2"></i> Historial de Visitas y Atenciones en CAP</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Fecha / Hora</th>
                                <th>Tipo de Servicio</th>
                                <th>Turno</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paciente->registrosLlegada as $llegada)
                                <tr>
                                    <td>{{ optional($llegada->fecha)->format('d/m/Y H:i A') }}</td>
                                    <td>{{ $llegada->tipo_servicio }}</td>
                                    <td>Turno #{{ $llegada->numero_turno }}</td>
                                    <td>
                                        <span class="badge bg-secondary-lt">
                                            {{ $llegada->estado }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">
                                        No hay registros de visitas de ventanilla para este paciente aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
