@extends('layouts.app')

@section('title', 'Núcleos Familiares')
@section('page_title', 'Gestión de Núcleos Familiares')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1 text-dark">Núcleos Familiares Registrados</h3>
        <p class="text-secondary mb-0 small">Consulte y gestione las familias de la comunidad de Chicamán y sus alrededores.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearFamilia">
        <i class="ti ti-plus me-1"></i> Registrar Nueva Familia
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

<!-- Filtros de búsqueda Tabler -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('familias.index') }}" class="row g-2 align-items-end" id="search-form">
            <div class="col-md-4 position-relative">
                <label class="form-label" for="buscar">Buscar No. Familia / Cabeza / DPI</label>
                <input type="text" id="buscar" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Ej: FAM-001 o Pérez..." autocomplete="off">
                <div id="search-suggestions" class="dropdown-menu w-100 shadow" style="display: none; position: absolute; max-height: 280px; overflow-y: auto; z-index: 1050; background: #ffffff; border: 1px solid #cbd5e1;"></div>
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
            <div class="col-md-3">
                <a href="{{ route('familias.index') }}" class="btn btn-secondary w-100"><i class="ti ti-rotate-clockwise me-1"></i> Limpiar Filtros</a>
            </div>
        </form>
    </div>
</div>

<div id="table-container">
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
                                <button type="button" class="btn btn-sm btn-warning btn-editar-familia"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarFamilia"
                                        data-id="{{ $familia->id_family }}"
                                        data-numero="{{ $familia->numero_familia }}"
                                        data-cabeza="{{ $familia->apellido_cabeza }}"
                                        data-dpi="{{ $familia->dpi }}"
                                        data-nacimiento="{{ optional($familia->fecha_nacimiento)->format('Y-m-d') }}"
                                        data-comunidad="{{ $familia->id_comunidad }}"
                                        data-municipio="{{ optional(optional($familia->comunidad)->municipio)->id_municipio }}"
                                        data-depto="{{ optional(optional(optional($familia->comunidad)->municipio)->departamento)->id_departamento }}">
                                    <i class="ti ti-edit me-1"></i> Editar
                                </button>
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
</div>

{{-- MODAL CREAR FAMILIA --}}
<div class="modal fade" id="modalCrearFamilia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="ti ti-home-plus me-2"></i> Registrar Nuevo Núcleo Familiar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('familias.store') }}" method="POST" id="formCrearFamilia">
                @csrf
                <div class="modal-body">
                    <h6 class="text-secondary border-bottom pb-2 mb-3">1. Datos del Núcleo Familiar</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="create_fam_numero">Número de Familia</label>
                            <input type="text" id="create_fam_numero" name="numero_familia" class="form-control" required placeholder="Ej: FAM-2026-001">
                            <span id="msg-dup-numfam-create" class="form-hint fw-bold"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="create_fam_cabeza">Apellido Cabeza de Familia</label>
                            <input type="text" id="create_fam_cabeza" name="apellido_cabeza" class="form-control" required placeholder="Ej: Pérez Ramos">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="create_fam_dpi">DPI Cabeza de Familia (13 dígitos)</label>
                            <input type="text" id="create_fam_dpi" name="dpi" class="form-control" maxlength="13" placeholder="Ej: 1234567890101">
                            <span id="msg-dup-dpi-fam-create" class="form-hint fw-bold"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="create_fam_nacimiento">Fecha Nacimiento Cabeza</label>
                            <input type="date" id="create_fam_nacimiento" name="fecha_nacimiento" class="form-control">
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3">2. Ubicación Territorial</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label required" for="create_id_depto">Departamento</label>
                            <select id="create_id_depto" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach($departamentos as $dep)
                                    <option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="create_id_muni">Municipio</label>
                            <select id="create_id_muni" class="form-select" required disabled>
                                <option value="">-- Seleccionar Depto Primero --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="create_id_comunidad">Comunidad</label>
                            <select id="create_id_comunidad" name="id_comunidad" class="form-select" required disabled>
                                <option value="">-- Seleccionar Muni Primero --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Guardar Familia</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDITAR FAMILIA --}}
<div class="modal fade" id="modalEditarFamilia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="ti ti-edit me-2"></i> Editar Núcleo Familiar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="formEditarFamilia">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_familia_id" name="familia_id">
                <div class="modal-body">
                    <h6 class="text-secondary border-bottom pb-2 mb-3">1. Datos del Núcleo Familiar</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="edit_fam_numero">Número de Familia</label>
                            <input type="text" id="edit_fam_numero" name="numero_familia" class="form-control" required>
                            <span id="msg-dup-numfam-edit" class="form-hint fw-bold"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="edit_fam_cabeza">Apellido Cabeza de Familia</label>
                            <input type="text" id="edit_fam_cabeza" name="apellido_cabeza" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="edit_fam_dpi">DPI Cabeza de Familia (13 dígitos)</label>
                            <input type="text" id="edit_fam_dpi" name="dpi" class="form-control" maxlength="13">
                            <span id="msg-dup-dpi-fam-edit" class="form-hint fw-bold"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_fam_nacimiento">Fecha Nacimiento Cabeza</label>
                            <input type="date" id="edit_fam_nacimiento" name="fecha_nacimiento" class="form-control">
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3">2. Ubicación Territorial</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label required" for="edit_id_depto">Departamento</label>
                            <select id="edit_id_depto" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach($departamentos as $dep)
                                    <option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="edit_id_muni">Municipio</label>
                            <select id="edit_id_muni" class="form-select" required disabled>
                                <option value="">-- Seleccionar Depto Primero --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="edit_id_comunidad">Comunidad</label>
                            <select id="edit_id_comunidad" name="id_comunidad" class="form-select" required disabled>
                                <option value="">-- Seleccionar Muni Primero --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="ti ti-device-floppy me-1"></i> Actualizar Familia</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cascada de Ubicaciones en un helper reutilizable
    function setupCascadingUbicaciones(deptoId, muniId, comId, defaultComunidadId = null) {
        const deptoSelect = document.getElementById(deptoId);
        const muniSelect = document.getElementById(muniId);
        const comSelect = document.getElementById(comId);

        if (deptoSelect) {
            deptoSelect.addEventListener('change', function () {
                const val = this.value;
                muniSelect.innerHTML = '<option value="">-- Cargando... --</option>';
                muniSelect.disabled = true;
                comSelect.innerHTML = '<option value="">-- Seleccionar Muni Primero --</option>';
                comSelect.disabled = true;
                if (!val) return;

                fetch(`/api/ubicaciones/municipios/${val}`)
                    .then(res => res.json())
                    .then(data => {
                        muniSelect.innerHTML = '<option value="">-- Seleccionar Municipio --</option>';
                        data.forEach(m => {
                            muniSelect.innerHTML += `<option value="${m.id_municipio}">${m.nombre}</option>`;
                        });
                        muniSelect.disabled = false;
                    });
            });
        }

        if (muniSelect) {
            muniSelect.addEventListener('change', function () {
                const val = this.value;
                comSelect.innerHTML = '<option value="">-- Cargando... --</option>';
                comSelect.disabled = true;
                if (!val) return;

                fetch(`/api/ubicaciones/comunidades/${val}`)
                    .then(res => res.json())
                    .then(data => {
                        comSelect.innerHTML = '<option value="">-- Seleccionar Comunidad --</option>';
                        data.forEach(c => {
                            const zonaText = c.zona ? ` (${c.zona})` : '';
                            const sel = defaultComunidadId == c.id_comunidad ? 'selected' : '';
                            comSelect.innerHTML += `<option value="${c.id_comunidad}" ${sel}>${c.nombre}${zonaText}</option>`;
                        });
                        comSelect.disabled = false;
                    });
            });
        }
    }

    setupCascadingUbicaciones('create_id_depto', 'create_id_muni', 'create_id_comunidad');
    setupCascadingUbicaciones('edit_id_depto', 'edit_id_muni', 'edit_id_comunidad');

    // Llenar Modal de Editar Familia (Función Re-vinculable)
    function bindEditEvents() {
        document.querySelectorAll('.btn-editar-familia').forEach(btn => {
            btn.onclick = function () {
                const id = this.getAttribute('data-id');
                const form = document.getElementById('formEditarFamilia');
                form.action = `/familias/${id}`;
                document.getElementById('edit_familia_id').value = id;
                document.getElementById('edit_fam_numero').value = this.getAttribute('data-numero');
                document.getElementById('edit_fam_cabeza').value = this.getAttribute('data-cabeza');
                document.getElementById('edit_fam_dpi').value = this.getAttribute('data-dpi') || '';
                document.getElementById('edit_fam_nacimiento').value = this.getAttribute('data-nacimiento') || '';

                const deptoId = this.getAttribute('data-depto');
                const muniId = this.getAttribute('data-municipio');
                const comId = this.getAttribute('data-comunidad');

                if (deptoId) {
                    const deptoSelect = document.getElementById('edit_id_depto');
                    deptoSelect.value = deptoId;
                    
                    fetch(`/api/ubicaciones/municipios/${deptoId}`)
                        .then(res => res.json())
                        .then(munis => {
                            const muniSelect = document.getElementById('edit_id_muni');
                            muniSelect.innerHTML = '<option value="">-- Seleccionar Municipio --</option>';
                            munis.forEach(m => {
                                muniSelect.innerHTML += `<option value="${m.id_municipio}" ${m.id_municipio == muniId ? 'selected' : ''}>${m.nombre}</option>`;
                            });
                            muniSelect.disabled = false;

                            if (muniId) {
                                fetch(`/api/ubicaciones/comunidades/${muniId}`)
                                    .then(res => res.json())
                                    .then(coms => {
                                        const comSelect = document.getElementById('edit_id_comunidad');
                                        comSelect.innerHTML = '<option value="">-- Seleccionar Comunidad --</option>';
                                        coms.forEach(c => {
                                            const zonaText = c.zona ? ` (${c.zona})` : '';
                                            comSelect.innerHTML += `<option value="${c.id_comunidad}" ${c.id_comunidad == comId ? 'selected' : ''}>${c.nombre}${zonaText}</option>`;
                                        });
                                        comSelect.disabled = false;
                                    });
                            }
                        });
                }
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

    // Búsqueda y Sugerencias en Tiempo Real (Autocompletado debajo de la barra)
    const buscarInput = document.getElementById('buscar');
    const suggestionsBox = document.getElementById('search-suggestions');
    let debounceTimer;

    if (buscarInput && suggestionsBox) {
        buscarInput.addEventListener('input', function() {
            const val = this.value.trim();
            if (val.length < 2) {
                suggestionsBox.style.display = 'none';
                suggestionsBox.innerHTML = '';
                return;
            }

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const formData = new FormData(searchForm);
                const query = new URLSearchParams(formData).toString();
                const url = `${searchForm.action}?${query}`;

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const rows = doc.querySelectorAll('#table-container tbody tr');
                        suggestionsBox.innerHTML = '';

                        if (rows.length === 0 || (rows.length === 1 && rows[0].querySelector('td[colspan]'))) {
                            suggestionsBox.innerHTML = '<div class="dropdown-item text-muted">No se encontraron coincidencias</div>';
                        } else {
                            rows.forEach(row => {
                                const numFam = row.cells[0]?.textContent.trim();
                                const cabeza = row.cells[1]?.textContent.trim();
                                const comunidad = row.cells[3]?.textContent.trim();
                                const viewLink = row.cells[6]?.querySelector('a.btn-info')?.getAttribute('href');

                                if (numFam && viewLink) {
                                    const item = document.createElement('a');
                                    item.href = viewLink;
                                    item.className = 'dropdown-item d-flex justify-content-between align-items-center py-2 border-bottom';
                                    item.style.borderBottomColor = '#f1f5f9';
                                    item.innerHTML = `
                                        <div>
                                            <div class="fw-bold text-dark">${numFam} — ${cabeza}</div>
                                            <small class="text-secondary">${comunidad}</small>
                                        </div>
                                        <i class="ti ti-chevron-right text-muted"></i>
                                    `;
                                    suggestionsBox.appendChild(item);
                                }
                            });
                        }
                        suggestionsBox.style.display = 'block';
                    });
            }, 200);
        });

        // Cerrar sugerencias al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!buscarInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });
    }

    const comunidadSelect = document.getElementById('id_comunidad');
    if (comunidadSelect) {
        comunidadSelect.addEventListener('change', () => performSearch());
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

    // Paginación asíncrona
    if (tableContainer) {
        tableContainer.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                performSearch(link.href);
            }
        });
    }

    // Vinculación inicial de eventos
    bindEditEvents();
});
</script>
@endsection
