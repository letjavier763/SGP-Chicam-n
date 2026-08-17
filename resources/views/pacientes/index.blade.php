@extends('layouts.app')

@section('title', 'Registro de Pacientes')
@section('page_title', 'Gestión de Pacientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1 text-dark">Pacientes Registrados</h3>
        <p class="text-secondary mb-0 small">Consulte expedientes y realice búsquedas por DPI, nombre o expediente físico.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearPaciente">
        <i class="ti ti-user-plus me-1"></i> Registrar Nuevo Paciente
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
        <form method="GET" action="{{ route('pacientes.index') }}" class="row g-2 align-items-end" id="search-form">
            <div class="col-md-5 position-relative">
                <label class="form-label" for="buscar">Buscar Paciente / DPI / Expediente</label>
                <input type="text" id="buscar" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Nombre, DPI o Expediente..." autocomplete="off">
                <div id="search-suggestions" class="dropdown-menu w-100 shadow" style="display: none; position: absolute; max-height: 280px; overflow-y: auto; z-index: 1050; background: #ffffff; border: 1px solid #cbd5e1;"></div>
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
            <div class="col-md-3">
                <a href="{{ route('pacientes.index') }}" class="btn btn-secondary w-100"><i class="ti ti-rotate-clockwise me-1"></i> Limpiar Filtros</a>
            </div>
        </form>
    </div>
</div>

<div id="table-container">
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
                                        data-telefono="{{ $paciente->telefono }}">
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
                        <div class="col-md-8">
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
                        <div class="col-md-4">
                            <label class="form-label" for="create_numero_expediente_fisico">No. Expediente Físico</label>
                            <input type="text" id="create_numero_expediente_fisico" name="numero_expediente_fisico" class="form-control bg-light" readonly placeholder="Se asigna según la familia">
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
                            <input type="text" id="create_dpi" name="dpi" class="form-control" maxlength="13" placeholder="Ej: 1987654320101">
                            <span id="msg-dup-dpi-create" class="form-hint fw-bold"></span>
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
                            <input type="text" id="edit_dpi" name="dpi" class="form-control" maxlength="13">
                            <span id="msg-dup-dpi-edit" class="form-hint fw-bold"></span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="edit_telefono">Teléfono de Contacto (8 dígitos)</label>
                            <input type="text" id="edit_telefono" name="telefono" class="form-control" maxlength="8">
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
    // Auto-completar expediente en crear
    const createFamSelect = document.getElementById('create_id_family');
    const createExpInput = document.getElementById('create_numero_expediente_fisico');
    if (createFamSelect) {
        createFamSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            createExpInput.value = opt ? (opt.dataset.numeroFamilia || '') : '';
        });
    }

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
                document.getElementById('msg-dup-dpi-edit').textContent = '';
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
                                const exp = row.cells[0]?.textContent.trim();
                                const name = row.cells[1]?.querySelector('.fw-bold')?.textContent.trim() || row.cells[1]?.textContent.trim();
                                const dpi = row.cells[2]?.textContent.trim();
                                const viewLink = row.cells[7]?.querySelector('a.btn-info')?.getAttribute('href');

                                if (name && viewLink) {
                                    const item = document.createElement('a');
                                    item.href = viewLink;
                                    item.className = 'dropdown-item d-flex justify-content-between align-items-center py-2 border-bottom';
                                    item.style.borderBottomColor = '#f1f5f9';
                                    item.innerHTML = `
                                        <div>
                                            <div class="fw-bold text-dark">${name}</div>
                                            <small class="text-secondary">DPI: ${dpi}</small>
                                        </div>
                                        <span class="badge bg-blue-lt">Exp: ${exp}</span>
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

    // Verificación duplicados DPI (Crear y Editar)
    function setupDpiCheck(inputId, msgId, getIgnoreId = () => null) {
        const input = document.getElementById(inputId);
        const msg = document.getElementById(msgId);
        if (!input) return;

        input.addEventListener('blur', function () {
            const val = this.value.trim();
            if (!val) { msg.textContent = ''; return; }
            let url = `/pacientes/verificar-duplicado?tipo=dpi&valor=${encodeURIComponent(val)}`;
            const ignoreId = getIgnoreId();
            if (ignoreId) url += `&ignore_id=${ignoreId}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.duplicate) {
                        msg.className = 'form-hint text-danger fw-bold';
                        msg.textContent = '⚠ ' + data.message;
                    } else {
                        msg.className = 'form-hint text-success fw-bold';
                        msg.textContent = '✓ Disponible';
                    }
                });
        });
    }

    setupDpiCheck('create_dpi', 'msg-dup-dpi-create');
    setupDpiCheck('edit_dpi', 'msg-dup-dpi-edit', () => document.getElementById('edit_paciente_id').value);

    // Vinculación inicial de eventos
    bindEditEvents();
});
</script>
@endsection
