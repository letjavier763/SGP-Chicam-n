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

@if($errors->any())
    <div class="alert alert-danger alert-dismissible" role="alert">
        <strong>Atención: Revise los siguientes errores:</strong>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-3">
    <!-- Detalles de la Familia -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
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
            <div class="card-footer d-flex flex-column flex-sm-row gap-2">
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
            <div class="card-header d-flex justify-content-between align-items-start align-items-sm-center flex-wrap gap-2">
                <div>
                    <h3 class="card-title mb-0"><i class="ti ti-users me-2"></i> Integrantes Registrados</h3>
                    <p class="card-subtitle text-secondary mb-0 d-none d-md-block">Pacientes adscritos a este núcleo familiar.</p>
                </div>
                <button type="button" class="btn btn-sm btn-primary w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#modalCrearPacienteFamilia">
                    <i class="ti ti-plus me-1"></i> Agregar Paciente
                </button>
            </div>
            <!-- Vista de Tabla para Escritorio -->
            <div class="table-responsive d-none d-md-block">
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
                                <td colspan="4" class="text-center text-secondary py-4">
                                    No hay pacientes registrados en este núcleo familiar aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Vista de Tarjetas para Móviles -->
            <div class="divide-y d-md-none">
                @forelse($familia->pacientes as $paciente)
                    <div class="p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <strong class="text-dark" style="font-size: 0.95rem;">{{ $paciente->nombres }} {{ $paciente->apellidos }}</strong>
                                <div class="text-secondary small mt-1">
                                    Exp: <span class="badge bg-blue-lt">{{ $paciente->numero_expediente_fisico }}</span>
                                    @if($paciente->dpi) · DPI: {{ $paciente->dpi }} @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('pacientes.show', $paciente->id_paciente) }}" class="btn btn-sm btn-outline-info py-1">
                                    <i class="ti ti-eye me-1"></i> Ficha
                                </a>
                            </div>
                        </div>
                        <div class="small text-secondary" style="font-size: 0.8rem;">
                            <strong>Edad / Sexo:</strong> {{ optional($paciente->fecha_nacimiento)->age ?? '?' }} años ({{ $paciente->sexo === 'M' ? 'M' : 'F' }})
                        </div>
                    </div>
                @empty
                    <div class="text-center text-secondary py-4">
                        No hay pacientes registrados en este núcleo familiar aún.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREAR PACIENTE PARA ESTA FAMILIA --}}
<div class="modal fade" id="modalCrearPacienteFamilia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="ti ti-user-plus me-2"></i> Agregar Paciente a la Familia #{{ $familia->numero_familia }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pacientes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_family" value="{{ $familia->id_family }}">
                <input type="hidden" name="numero_expediente_fisico" value="{{ $familia->numero_familia }}">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Núcleo Familiar Asignado:</label>
                        <div class="form-control bg-light fw-bold text-dark">
                            Familia: {{ $familia->numero_familia }} — Cabeza: {{ $familia->apellido_cabeza }} ({{ $familia->comunidad->nombre ?? 'Sin comunidad' }})
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3">Información Personal del Paciente</h6>
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
