@extends('layouts.app')

@section('title', 'Registro de Pacientes')
@section('page_title', 'Gestión de Pacientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h3 class="mb-0 text-dark fw-bold" style="font-size: 1.25rem;">Pacientes</h3>
        <span class="text-secondary small d-md-none">{{ $pacientes->total() }} registros</span>
        <p class="text-secondary mb-0 small d-none d-md-block">Consulte expedientes y realice búsquedas por DPI, nombre, expediente físico o No. de registro.</p>
    </div>
    <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm px-3 py-1 py-md-2" data-bs-toggle="modal" data-bs-target="#modalCrearPaciente">
        <i class="ti ti-user-plus fs-2"></i>
        <span>Nuevo Paciente</span>
    </button>
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

<style>
.search-unified-bar {
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}
.search-unified-bar:focus-within {
    border-color: #206bc4;
    box-shadow: 0 0 0 3px rgba(32, 107, 196, 0.15);
}
.search-unified-bar .form-control {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
}
.search-unified-bar .form-control:focus {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}
</style>

<!-- Filtros de búsqueda Tabler -->
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body p-2 p-md-3">
        <form method="GET" action="{{ route('pacientes.index') }}" id="search-form">
            <div class="row g-2 align-items-center">
                {{-- Barra de búsqueda principal limpia y continua --}}
                <div class="col-12 col-md-5 position-relative">
                    <div class="search-unified-bar d-flex align-items-center px-3 py-1 bg-white">
                        <i class="ti ti-search text-secondary me-2 fs-2 flex-shrink-0"></i>
                        <input type="text" id="buscar" name="buscar" class="form-control ps-0 py-1 flex-grow-1" 
                               value="{{ request('buscar') }}" 
                               placeholder="Buscar por Nombre, DPI, Exp. o No. Reg..." 
                               autocomplete="off">
                        
                        {{-- Botón para alternar filtros avanzados en móvil --}}
                        <button class="btn btn-sm px-2 py-1 d-md-none flex-shrink-0 border-0 rounded-2 ms-1 {{ (request('criterio') && request('criterio') !== 'todos' || request('sexo') || request('estado')) ? 'bg-primary-lt text-primary fw-bold' : 'btn-light text-secondary' }}" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#filtrosAvanzados" 
                                aria-expanded="{{ (request('criterio') && request('criterio') !== 'todos' || request('sexo') || request('estado')) ? 'true' : 'false' }}"
                                title="Filtros avanzados">
                            <i class="ti ti-adjustments-horizontal me-1"></i>
                            <span class="small">Filtros</span>
                            @if(request('criterio') && request('criterio') !== 'todos' || request('sexo') || request('estado'))
                                <span class="badge bg-primary text-white rounded-pill ms-1">●</span>
                            @endif
                        </button>
                    </div>
                    <div id="search-suggestions" class="dropdown-menu w-100 shadow" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 4px; max-height: 280px; overflow-y: auto; z-index: 1050; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;"></div>
                </div>

                {{-- Filtros complementarios: colapsables en móvil, siempre visibles en escritorio --}}
                <div class="col-12 col-md-7 collapse d-md-block {{ (request('criterio') && request('criterio') !== 'todos' || request('sexo') || request('estado')) ? 'show' : '' }}" id="filtrosAvanzados">
                    <div class="row g-2 align-items-end pt-2 pt-md-0 border-top border-md-0 mt-1 mt-md-0">
                        <div class="col-6 col-md-4">
                            <label class="form-label small text-secondary mb-1" for="criterio">Buscar por</label>
                            <select id="criterio" name="criterio" class="form-select form-select-sm">
                                <option value="todos" {{ request('criterio') == 'todos' || !request('criterio') ? 'selected' : '' }}>Todos los campos</option>
                                <option value="nombre" {{ request('criterio') == 'nombre' ? 'selected' : '' }}>Solo Nombre</option>
                                <option value="numero_registro" {{ request('criterio') == 'numero_registro' ? 'selected' : '' }}>Solo No. Registro</option>
                                <option value="familia" {{ request('criterio') == 'familia' ? 'selected' : '' }}>Solo No. Familia</option>
                                <option value="dpi" {{ request('criterio') == 'dpi' ? 'selected' : '' }}>Solo DPI</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small text-secondary mb-1" for="sexo">Sexo</label>
                            <select id="sexo" name="sexo" class="form-select form-select-sm">
                                <option value="">-- Todos --</option>
                                <option value="M" {{ request('sexo') === 'M' ? 'selected' : '' }}>Masculino (M)</option>
                                <option value="F" {{ request('sexo') === 'F' ? 'selected' : '' }}>Femenino (F)</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small text-secondary mb-1" for="estado">Estado</label>
                            <select id="estado" name="estado" class="form-select form-select-sm">
                                <option value="">-- Todos --</option>
                                <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                                <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary w-100 btn-sm">
                                <i class="ti ti-rotate-clockwise me-1"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="table-container">
<!-- Tabla de Pacientes -->
<!-- Tabla de Pacientes (Escritorio) -->
<div class="card d-none d-md-block">
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
                        <td>
                            <strong class="text-primary">{{ $paciente->numero_expediente_fisico }}</strong>
                            @if($paciente->numero_registro)
                                <div><span class="badge bg-purple-lt text-purple fw-bold mt-1">Reg. #{{ $paciente->numero_registro }}</span></div>
                            @endif
                        </td>
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
                                <button type="button" class="btn btn-sm btn-warning btn-editar-paciente"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarPaciente"
                                        data-id="{{ $paciente->id_paciente }}"
                                        data-family="{{ $paciente->id_family }}"
                                        data-expediente="{{ $paciente->numero_expediente_fisico }}"
                                        data-nombres="{{ $paciente->nombres }}"
                                        data-apellidos="{{ $paciente->apellidos }}"
                                        data-nacimiento="{{ optional($paciente->fecha_nacimiento)->format('Y-m-d') }}"
                                        data-sexo="{{ $paciente->sexo }}"
                                        data-dpi="{{ $paciente->dpi }}"
                                        data-telefono="{{ $paciente->telefono }}"
                                        data-numero-registro="{{ $paciente->numero_registro }}"
                                        data-descripcion-registro="{{ $paciente->descripcion_registro }}"
                                        data-direccion="{{ $paciente->direccion }}">
                                    <i class="ti ti-edit me-1"></i> Editar
                                </button>
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

<!-- Vista de Tarjetas (Móvil) -->
<div class="divide-y d-md-none bg-white border rounded">
    @forelse($pacientes as $paciente)
        <div class="p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-sm bg-{{ $paciente->sexo === 'F' ? 'pink' : 'blue' }}-lt
                          text-{{ $paciente->sexo === 'F' ? 'pink' : 'blue' }} rounded-circle">
                        <i class="ti ti-user{{ $paciente->sexo === 'F' ? '-female' : '' }}"></i>
                    </span>
                    <div>
                        <div class="fw-bold" style="font-size: 0.95rem;">
                            {{ $paciente->nombres }} {{ $paciente->apellidos }}
                        </div>
                        <div class="text-secondary small">
                            Exp: <strong class="text-primary">{{ $paciente->numero_expediente_fisico }}</strong>
                            @if($paciente->numero_registro) · <span class="badge bg-purple-lt text-purple fw-bold">Reg. #{{ $paciente->numero_registro }}</span> @endif
                            @if($paciente->dpi) · DPI: {{ $paciente->dpi }} @endif
                        </div>
                    </div>
                </div>
                <div>
                    @if($paciente->activo)
                        <span class="badge bg-green-lt">Activo</span>
                    @else
                        <span class="badge bg-red-lt">Inactivo</span>
                    @endif
                </div>
            </div>
            
            <div class="small text-secondary mb-3" style="font-size: 0.8rem;">
                <strong>Edad/Sexo:</strong> {{ optional($paciente->fecha_nacimiento)->age ?? '?' }} años ({{ $paciente->sexo }})
                @if($paciente->telefono) · <strong>Tel:</strong> {{ $paciente->telefono }} @endif
                @if($paciente->familia)
                    <br>
                    <strong>Familia:</strong> {{ $paciente->familia->numero_familia }} ({{ $paciente->familia->apellido_cabeza }})
                @endif
            </div>
            
            <div class="d-flex gap-2 justify-content-end pt-2 border-top">
                <a href="{{ route('pacientes.show', $paciente->id_paciente) }}" class="btn btn-sm btn-outline-info py-1 px-2" style="font-size: 0.75rem;">
                    <i class="ti ti-eye me-1"></i> Ficha
                </a>
                <button type="button" class="btn btn-sm btn-outline-warning btn-editar-paciente py-1 px-2" style="font-size: 0.75rem;"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditarPaciente"
                        data-id="{{ $paciente->id_paciente }}"
                        data-family="{{ $paciente->id_family }}"
                        data-expediente="{{ $paciente->numero_expediente_fisico }}"
                        data-nombres="{{ $paciente->nombres }}"
                        data-apellidos="{{ $paciente->apellidos }}"
                        data-nacimiento="{{ optional($paciente->fecha_nacimiento)->format('Y-m-d') }}"
                        data-sexo="{{ $paciente->sexo }}"
                        data-dpi="{{ $paciente->dpi }}"
                        data-telefono="{{ $paciente->telefono }}"
                        data-numero-registro="{{ $paciente->numero_registro }}"
                        data-descripcion-registro="{{ $paciente->descripcion_registro }}"
                        data-direccion="{{ $paciente->direccion }}">
                    <i class="ti ti-edit me-1"></i> Editar
                </button>
                <form action="{{ route('pacientes.toggle-status', $paciente->id_paciente) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ $paciente->activo ? 'btn-outline-secondary' : 'btn-outline-success' }} py-1 px-2" style="font-size: 0.75rem;">
                        {{ $paciente->activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-center text-secondary py-4">
            <i class="ti ti-user-off fs-1 d-block mb-2 opacity-50"></i>
            No se encontraron pacientes registrados con los filtros aplicados.
        </div>
    @endforelse
</div>

<div class="mt-3">
    {{ $pacientes->links() }}
</div>
</div>

{{-- MODAL CREAR PACIENTE --}}
<div class="modal fade" id="modalCrearPaciente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="ti ti-user-plus me-2"></i> Registrar Nuevo Paciente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pacientes.store') }}" method="POST" id="formCrearPaciente">
                @csrf
                <div class="modal-body">
                    <h6 class="text-secondary border-bottom pb-2 mb-3">1. Adscripción al Núcleo Familiar</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-12">
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
                            <input type="text" id="create_dpi" name="dpi" class="form-control" inputmode="numeric" pattern="[0-9]*" maxlength="13" placeholder="Ej: 1987654320101">
                            <span id="msg-dup-dpi-create" class="form-hint fw-bold"></span>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="create_telefono">Teléfono de Contacto (8 dígitos)</label>
                            <input type="text" id="create_telefono" name="telefono" class="form-control" inputmode="numeric" pattern="[0-9]*" maxlength="8" placeholder="Ej: 55551234">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="create_direccion">Dirección</label>
                            <input type="text" id="create_direccion" name="direccion" class="form-control" maxlength="255" placeholder="Ej: Caserío El Centro, Sector 2">
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3">3. Datos del Registro Físico</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="create_numero_registro">No. de Registro</label>
                            <input type="text" id="create_numero_registro" name="numero_registro" class="form-control" inputmode="numeric" pattern="[0-9]*" placeholder="Ej: 1024">
                            <span id="msg-dup-reg-create" class="form-hint fw-bold"></span>
                            <span class="form-hint text-muted">Número entero del registro físico</span>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="create_descripcion_registro">Descripción / Ubicación del Registro</label>
                            <textarea id="create_descripcion_registro" name="descripcion_registro" class="form-control auto-expand-textarea" rows="1" maxlength="150" placeholder="Ej: Archivero 3, Cajón B, Folder amarillo"></textarea>
                            <span class="form-hint">Máx. 150 caracteres — indica dónde está el expediente físico</span>
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

{{-- MODAL EDITAR PACIENTE --}}
<div class="modal fade" id="modalEditarPaciente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="ti ti-edit me-2"></i> Editar Información de Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="formEditarPaciente">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_paciente_id" name="paciente_id">
                <div class="modal-body">
                    <h6 class="text-secondary border-bottom pb-2 mb-3">1. Adscripción al Núcleo Familiar</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label required" for="edit_id_family">Núcleo Familiar</label>
                            <select id="edit_id_family" name="id_family" class="form-select" required>
                                <option value="">-- Seleccione un núcleo familiar --</option>
                                @foreach($familias as $fam)
                                    <option value="{{ $fam->id_family }}" data-numero-familia="{{ $fam->numero_familia }}">
                                        No. Familia: {{ $fam->numero_familia }} — Cabeza: {{ $fam->apellido_cabeza }} ({{ $fam->comunidad->nombre ?? 'Sin comunidad' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="edit_numero_expediente_fisico">No. Expediente Físico</label>
                            <input type="text" id="edit_numero_expediente_fisico" name="numero_expediente_fisico" class="form-control bg-light" placeholder="No. expediente">
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3">2. Información Personal del Paciente</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="edit_nombres">Nombres</label>
                            <input type="text" id="edit_nombres" name="nombres" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="edit_apellidos">Apellidos</label>
                            <input type="text" id="edit_apellidos" name="apellidos" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label required" for="edit_fecha_nacimiento">Fecha Nacimiento</label>
                            <input type="date" id="edit_fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="edit_sexo">Sexo</label>
                            <select id="edit_sexo" name="sexo" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                <option value="M">Masculino (M)</option>
                                <option value="F">Femenino (F)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="edit_dpi">DPI (13 dígitos)</label>
                            <input type="text" id="edit_dpi" name="dpi" class="form-control" inputmode="numeric" pattern="[0-9]*" maxlength="13">
                            <span id="msg-dup-dpi-edit" class="form-hint fw-bold"></span>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="edit_telefono">Teléfono de Contacto (8 dígitos)</label>
                            <input type="text" id="edit_telefono" name="telefono" class="form-control" inputmode="numeric" pattern="[0-9]*" maxlength="8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_direccion">Dirección</label>
                            <input type="text" id="edit_direccion" name="direccion" class="form-control" maxlength="255" placeholder="Ej: Caserío El Centro, Sector 2">
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3">3. Datos del Registro Físico</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="edit_numero_registro">No. de Registro</label>
                            <input type="text" id="edit_numero_registro" name="numero_registro" class="form-control" inputmode="numeric" pattern="[0-9]*" placeholder="Ej: 1024">
                            <span id="msg-dup-reg-edit" class="form-hint fw-bold"></span>
                            <span class="form-hint text-muted">Número entero del registro físico</span>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="edit_descripcion_registro">Descripción / Ubicación del Registro</label>
                            <textarea id="edit_descripcion_registro" name="descripcion_registro" class="form-control auto-expand-textarea" rows="1" maxlength="150" placeholder="Ej: Archivero 3, Cajón B, Folder amarillo"></textarea>
                            <span class="form-hint">Máx. 150 caracteres — indica dónde está el expediente físico</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="ti ti-device-floppy me-1"></i> Actualizar Paciente</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Auto-completar expediente en editar
    const editFamSelect = document.getElementById('edit_id_family');
    const editExpInput = document.getElementById('edit_numero_expediente_fisico');
    if (editFamSelect) {
        editFamSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.numeroFamilia && !editExpInput.value) {
                editExpInput.value = opt.dataset.numeroFamilia;
            }
        });
    }

    // Llenar Modal de Edición al presionar Editar (Función Vinculable)
    function bindEditEvents() {
        document.querySelectorAll('.btn-editar-paciente').forEach(button => {
            // Eliminar listeners previos clonando el nodo si es necesario, o simplemente añadiendo
            button.onclick = function () {
                const id = this.getAttribute('data-id');
                const form = document.getElementById('formEditarPaciente');
                form.action = `/pacientes/${id}`;
                document.getElementById('edit_paciente_id').value = id;
                document.getElementById('edit_id_family').value = this.getAttribute('data-family');
                document.getElementById('edit_numero_expediente_fisico').value = this.getAttribute('data-expediente');
                document.getElementById('edit_nombres').value = this.getAttribute('data-nombres');
                document.getElementById('edit_apellidos').value = this.getAttribute('data-apellidos');
                document.getElementById('edit_fecha_nacimiento').value = this.getAttribute('data-nacimiento');
                document.getElementById('edit_sexo').value = this.getAttribute('data-sexo');
                document.getElementById('edit_dpi').value = this.getAttribute('data-dpi') || '';
                document.getElementById('edit_telefono').value = this.getAttribute('data-telefono') || '';
                document.getElementById('edit_numero_registro').value = this.getAttribute('data-numero-registro') || '';
                document.getElementById('edit_descripcion_registro').value = this.getAttribute('data-descripcion-registro') || '';
                document.getElementById('edit_descripcion_registro').dispatchEvent(new Event('input'));
                document.getElementById('edit_direccion').value = this.getAttribute('data-direccion') || '';
                
                const msgDpiEdit = document.getElementById('msg-dup-dpi-edit');
                const msgRegEdit = document.getElementById('msg-dup-reg-edit');
                if (msgDpiEdit) msgDpiEdit.textContent = '';
                if (msgRegEdit) msgRegEdit.textContent = '';
                document.getElementById('edit_dpi').classList.remove('is-invalid');
                document.getElementById('edit_numero_registro').classList.remove('is-invalid');
                editDpiDuplicado = false;
                editRegDuplicado = false;
            };
        });
    }

    // AJAX para Búsqueda y Filtrado en Tiempo Real
    const searchForm = document.getElementById('search-form');
    const tableContainer = document.getElementById('table-container');

    function performSearch(url = null) {
        if (!url) {
            const formData = new FormData(searchForm);
            const query = new URLSearchParams(formData).toString();
            url = `${searchForm.action}?${query}`;
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('table-container');
                if (newTable && tableContainer) {
                    tableContainer.innerHTML = newTable.innerHTML;
                    bindEditEvents();
                }
            });
    }

    // Búsqueda y Filtrado en Tiempo Real (asíncrono al ir ingresando datos)
    const buscarInput = document.getElementById('buscar');
    const filtroRegInput = document.getElementById('filtro_numero_registro');
    const criterioSelect = document.getElementById('criterio');
    let debounceTimer;

    function updatePlaceholder() {
        if (!criterioSelect || !buscarInput) return;
        const c = criterioSelect.value;
        if (c === 'nombre') {
            buscarInput.placeholder = 'Buscar nombres o apellidos...';
        } else if (c === 'numero_registro') {
            buscarInput.placeholder = 'Buscar por No. de registro (ej: 1024)...';
        } else if (c === 'familia') {
            buscarInput.placeholder = 'Buscar por No. de familia o expediente...';
        } else if (c === 'dpi') {
            buscarInput.placeholder = 'Buscar por DPI (ej: 198765...)...';
        } else {
            buscarInput.placeholder = 'Nombre, DPI, Exp. o No. Reg...';
        }
    }

    if (criterioSelect) {
        criterioSelect.addEventListener('change', function () {
            updatePlaceholder();
            performSearch();
        });
        updatePlaceholder();
    }

    if (buscarInput) {
        buscarInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                performSearch();
            }, 300);
        });
    }

    if (filtroRegInput) {
        filtroRegInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                performSearch();
            }, 300);
        });
    }

    const sexoSelect = document.getElementById('sexo');
    if (sexoSelect) {
        sexoSelect.addEventListener('change', () => performSearch());
    }

    const estadoSelect = document.getElementById('estado');
    if (estadoSelect) {
        estadoSelect.addEventListener('change', () => performSearch());
    }

    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
    }

    // Interceptar clics de paginación para hacerlos asíncronos
    if (tableContainer) {
        tableContainer.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                performSearch(link.href);
            }
        });
    }

    // Estados de duplicidad para Crear y Editar
    let createDpiDuplicado = false;
    let createRegDuplicado = false;
    let editDpiDuplicado = false;
    let editRegDuplicado = false;

    // Verificación duplicados genérica (DPI y No. Registro)
    function setupDuplicateCheck(inputId, msgId, tipo, getIgnoreId = () => null, onStatusChange = () => {}) {
        const input = document.getElementById(inputId);
        const msg = document.getElementById(msgId);
        if (!input || !msg) return;

        input.addEventListener('input', function () {
            input.classList.remove('is-invalid');
            msg.textContent = '';
            onStatusChange(false);
        });

        input.addEventListener('blur', function () {
            const val = this.value.trim();
            if (!val) { 
                msg.textContent = ''; 
                input.classList.remove('is-invalid');
                onStatusChange(false);
                return; 
            }
            let url = `/pacientes/verificar-duplicado?tipo=${tipo}&valor=${encodeURIComponent(val)}`;
            const ignoreId = getIgnoreId();
            if (ignoreId) url += `&ignore_id=${ignoreId}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.duplicate) {
                        input.classList.add('is-invalid');
                        msg.className = 'form-hint text-danger fw-bold';
                        msg.textContent = '⚠ ' + data.message;
                        onStatusChange(true);
                    } else {
                        input.classList.remove('is-invalid');
                        msg.className = 'form-hint text-success fw-bold';
                        msg.textContent = '✓ Disponible';
                        onStatusChange(false);
                    }
                });
        });
    }

    // Configurar verificaciones en modal Crear
    setupDuplicateCheck('create_dpi', 'msg-dup-dpi-create', 'dpi', () => null, (isDup) => {
        createDpiDuplicado = isDup;
    });
    setupDuplicateCheck('create_numero_registro', 'msg-dup-reg-create', 'numero_registro', () => null, (isDup) => {
        createRegDuplicado = isDup;
    });

    // Configurar verificaciones en modal Editar
    setupDuplicateCheck('edit_dpi', 'msg-dup-dpi-edit', 'dpi', () => document.getElementById('edit_paciente_id').value, (isDup) => {
        editDpiDuplicado = isDup;
    });
    setupDuplicateCheck('edit_numero_registro', 'msg-dup-reg-edit', 'numero_registro', () => document.getElementById('edit_paciente_id').value, (isDup) => {
        editRegDuplicado = isDup;
    });

    // Validar antes de enviar formCrearPaciente
    const formCrear = document.getElementById('formCrearPaciente');
    if (formCrear) {
        formCrear.addEventListener('submit', function (e) {
            if (createDpiDuplicado) {
                e.preventDefault();
                alert('No se puede guardar: El DPI ya está registrado en el sistema.');
                document.getElementById('create_dpi').focus();
                return false;
            }
            if (createRegDuplicado) {
                e.preventDefault();
                alert('No se puede guardar: El No. de Registro ya está asignado a otro paciente.');
                document.getElementById('create_numero_registro').focus();
                return false;
            }
        });
    }

    // Validar antes de enviar formEditarPaciente
    const formEditar = document.getElementById('formEditarPaciente');
    if (formEditar) {
        formEditar.addEventListener('submit', function (e) {
            if (editDpiDuplicado) {
                e.preventDefault();
                alert('No se puede actualizar: El DPI ya pertenece a otro paciente o familia.');
                document.getElementById('edit_dpi').focus();
                return false;
            }
            if (editRegDuplicado) {
                e.preventDefault();
                alert('No se puede actualizar: El No. de Registro ya pertenece a otro paciente.');
                document.getElementById('edit_numero_registro').focus();
                return false;
            }
        });
    }

    // Vinculación inicial de eventos
    bindEditEvents();

    @if($errors->any() && !old('paciente_id'))
    const modalCrearEl = document.getElementById('modalCrearPaciente');
    if (modalCrearEl) {
        const modalCrear = new bootstrap.Modal(modalCrearEl);
        modalCrear.show();
    }
    @endif
});
</script>
@endsection
