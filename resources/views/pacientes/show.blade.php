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

@if($errors->any())
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div><i class="ti ti-alert-triangle me-2 fs-2"></i></div>
            <div>
                <strong>Atención: Revise los errores en el formulario:</strong>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

<div class="row g-3">
    <!-- Ficha Personal del Paciente -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
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
            <div class="card-footer d-flex flex-column flex-sm-row gap-2">
                <button type="button" class="btn btn-primary flex-fill" data-bs-toggle="modal" data-bs-target="#modalEditarPacienteShow">
                    <i class="ti ti-edit me-1"></i> Editar Ficha
                </button>
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
                        <div class="d-flex justify-content-between align-items-start align-items-sm-center flex-column flex-sm-row gap-3">
                            <div>
                                <h4 class="text-primary mb-1">Familia #{{ $paciente->familia->numero_familia }}</h4>
                                <div class="text-secondary small">
                                    <strong>Cabeza de Familia:</strong> {{ $paciente->familia->apellido_cabeza }}<br>
                                    <strong>Comunidad:</strong> {{ $paciente->familia->comunidad->nombre ?? 'N/A' }}
                                </div>
                            </div>
                            <a href="{{ route('familias.show', $paciente->familia->id_family) }}" class="btn btn-outline-primary w-100 w-sm-auto">
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
                <!-- Vista de Tabla para Escritorio -->
                <div class="table-responsive d-none d-md-block">
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

                <!-- Vista de Tarjetas para Móviles -->
                <div class="divide-y d-md-none">
                    @forelse($paciente->registrosLlegada as $llegada)
                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-bold text-dark">
                                    {{ optional($llegada->fecha)->format('d/m/Y') }}
                                </div>
                                <span class="badge bg-secondary-lt">
                                    {{ $llegada->estado }}
                                </span>
                            </div>
                            <div class="text-secondary small">
                                <strong>Hora:</strong> {{ optional($llegada->fecha)->format('H:i A') }}<br>
                                <strong>Servicio:</strong> {{ $llegada->tipo_servicio }}<br>
                                <strong>Turno:</strong> Turno #{{ $llegada->numero_turno }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-secondary py-4">
                            No hay registros de visitas de ventanilla para este paciente aún.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL EDITAR PACIENTE DESDE SHOW --}}
<div class="modal fade" id="modalEditarPacienteShow" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="ti ti-edit me-2"></i> Editar Ficha de Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pacientes.update', $paciente->id_paciente) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <h6 class="text-secondary border-bottom pb-2 mb-3">1. Adscripción al Núcleo Familiar</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label required" for="show_id_family">Núcleo Familiar</label>
                            <select id="show_id_family" name="id_family" class="form-select" required>
                                @foreach($familias as $fam)
                                    <option value="{{ $fam->id_family }}" {{ $paciente->id_family == $fam->id_family ? 'selected' : '' }}>
                                        No. Familia: {{ $fam->numero_familia }} — Cabeza: {{ $fam->apellido_cabeza }} ({{ $fam->comunidad->nombre ?? 'Sin comunidad' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="show_numero_expediente_fisico">No. Expediente Físico</label>
                            <input type="text" id="show_numero_expediente_fisico" name="numero_expediente_fisico" class="form-control bg-light" value="{{ $paciente->numero_expediente_fisico }}">
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3">2. Información Personal del Paciente</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="show_nombres">Nombres</label>
                            <input type="text" id="show_nombres" name="nombres" class="form-control" value="{{ $paciente->nombres }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="show_apellidos">Apellidos</label>
                            <input type="text" id="show_apellidos" name="apellidos" class="form-control" value="{{ $paciente->apellidos }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label required" for="show_fecha_nacimiento">Fecha Nacimiento</label>
                            <input type="date" id="show_fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="{{ optional($paciente->fecha_nacimiento)->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="show_sexo">Sexo</label>
                            <select id="show_sexo" name="sexo" class="form-select" required>
                                <option value="M" {{ $paciente->sexo === 'M' ? 'selected' : '' }}>Masculino (M)</option>
                                <option value="F" {{ $paciente->sexo === 'F' ? 'selected' : '' }}>Femenino (F)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="show_dpi">DPI (13 dígitos)</label>
                            <input type="text" id="show_dpi" name="dpi" class="form-control" value="{{ $paciente->dpi }}" maxlength="13">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="show_telefono">Teléfono de Contacto (8 dígitos)</label>
                            <input type="text" id="show_telefono" name="telefono" class="form-control" value="{{ $paciente->telefono }}" maxlength="8">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="ti ti-device-floppy me-1"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
