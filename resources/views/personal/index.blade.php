@extends('layouts.app')

@section('title', 'Gestión de Personal')
@section('page_title', 'Administración de Personal y Accesos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1 text-dark">Personal y Usuarios del Sistema</h3>
        <p class="text-secondary mb-0 small">Consulte, registre y gestione los usuarios que acceden al sistema, sus roles y estados de cuenta.</p>
    </div>
    <a href="{{ route('personal.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Registrar Nuevo Personal
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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div><i class="ti ti-alert-triangle me-2 fs-2"></i></div>
            <div>{{ session('error') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

<!-- Filtros de búsqueda -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('personal.index') }}" class="row g-2 align-items-end" id="filter-form">
            <div class="col-md-4">
                <label class="form-label" for="buscar">Buscar Nombre o Usuario</label>
                <input type="text" id="buscar" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Ej: María Mercedes o admin..." autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="id_rol">Rol</label>
                <select id="id_rol" name="id_rol" class="form-select">
                    <option value="">-- Todos los roles --</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id_rol }}" {{ request('id_rol') == $rol->id_rol ? 'selected' : '' }}>
                            {{ $rol->nombre_rol }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="estado">Estado</label>
                <select id="estado" name="estado" class="form-select">
                    <option value="">-- Todos los estados --</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="ti ti-rotate-clockwise me-1"></i> Limpiar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Listado de Personal -->
<div class="card" id="list-container">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-users me-2 text-primary"></i>
            Personal Registrado
        </h3>
        <div class="card-options text-secondary small">
            {{ $personal->total() }} registros totales
        </div>
    </div>
    <!-- Vista de Tabla para Escritorio -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-vcenter table-hover card-table">
            <thead>
                <tr>
                    <th>Nombre Completo</th>
                    <th>Nombre de Usuario</th>
                    <th>Rol</th>
                    <th>Último Acceso</th>
                    <th>Estado</th>
                    <th class="w-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($personal as $p)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-blue-lt text-blue rounded-circle fw-bold">
                                {{ strtoupper(substr($p->nombre_completo, 0, 2)) }}
                            </span>
                            <div class="font-weight-medium">
                                {{ $p->nombre_completo }}
                                @if(Auth::id() == $p->id_usuario)
                                    <span class="badge bg-purple-lt text-purple ms-1">Tú</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-secondary font-monospace">{{ $p->username }}</td>
                    <td>
                        @php
                            $roleColors = [
                                'Administrador' => 'danger',
                                'Recepcionista' => 'primary',
                                'Director' => 'warning'
                            ];
                            $color = $roleColors[$p->rol->nombre_rol] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}-lt text-{{ $color }}">
                            {{ $p->rol->nombre_rol }}
                        </span>
                    </td>
                    <td>
                        @if($p->ultimo_acceso)
                            <div class="small text-dark">
                                {{ \Carbon\Carbon::parse($p->ultimo_acceso)->format('d/m/Y H:i') }}
                            </div>
                            <div class="text-secondary small">
                                ({{ \Carbon\Carbon::parse($p->ultimo_acceso)->diffForHumans() }})
                            </div>
                        @else
                            <span class="text-muted small">Nunca ha ingresado</span>
                        @endif
                    </td>
                    <td>
                        @if($p->activo)
                            <span class="badge bg-success-lt text-success">Activo</span>
                        @else
                            <span class="badge bg-secondary-lt text-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('personal.edit', $p->id_usuario) }}" class="btn btn-xs btn-outline-primary" title="Editar">
                                <i class="ti ti-edit fs-5"></i> Editar
                            </a>

                            @if(Auth::id() != $p->id_usuario)
                                <form action="{{ route('personal.toggle-status', $p->id_usuario) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($p->activo)
                                        <button type="submit" class="btn btn-xs btn-outline-danger" title="Desactivar" onclick="return confirm('¿Está seguro de que desea desactivar a este usuario? Perderá el acceso inmediatamente.')">
                                            <i class="ti ti-circle-x fs-5"></i> Desactivar
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-xs btn-outline-success" title="Activar">
                                            <i class="ti ti-circle-check fs-5"></i> Activar
                                        </button>
                                    @endif
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-4">
                        <i class="ti ti-user-off fs-1 d-block mb-2 opacity-50"></i>
                        No se encontraron usuarios registrados.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Vista de Tarjetas para Móviles -->
    <div class="divide-y d-md-none">
        @forelse($personal as $p)
            <div class="p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-sm bg-blue-lt text-blue rounded-circle fw-bold">
                            {{ strtoupper(substr($p->nombre_completo, 0, 2)) }}
                        </span>
                        <div>
                            <div class="fw-bold" style="font-size: 0.95rem;">
                                {{ $p->nombre_completo }}
                                @if(Auth::id() == $p->id_usuario)
                                    <span class="badge bg-purple-lt text-purple ms-1">Tú</span>
                                @endif
                            </div>
                            <div class="text-secondary small font-monospace">{{ $p->username }}</div>
                        </div>
                    </div>
                    <div>
                        @php
                            $roleColors = [
                                'Administrador' => 'danger',
                                'Recepcionista' => 'primary',
                                'Director' => 'warning'
                            ];
                            $color = $roleColors[$p->rol->nombre_rol] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}-lt text-{{ $color }}">
                            {{ $p->rol->nombre_rol }}
                        </span>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center small text-secondary mb-2" style="font-size: 0.8rem;">
                    <div>
                        <i class="ti ti-clock me-1"></i>
                        @if($p->ultimo_acceso)
                            {{ \Carbon\Carbon::parse($p->ultimo_acceso)->format('d/m/Y H:i') }}
                        @else
                            <span class="text-muted">Nunca ingresó</span>
                        @endif
                    </div>
                    <div>
                        @if($p->activo)
                            <span class="badge bg-success-lt text-success">Activo</span>
                        @else
                            <span class="badge bg-secondary-lt text-secondary">Inactivo</span>
                        @endif
                    </div>
                </div>
                
                <div class="d-flex gap-2 justify-content-end pt-1">
                    <a href="{{ route('personal.edit', $p->id_usuario) }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.75rem;">
                        <i class="ti ti-edit me-1"></i> Editar
                    </a>

                    @if(Auth::id() != $p->id_usuario)
                        <form action="{{ route('personal.toggle-status', $p->id_usuario) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            @if($p->activo)
                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: 0.75rem;" onclick="return confirm('¿Está seguro de que desea desactivar a este usuario? Perderá el acceso inmediatamente.')">
                                    <i class="ti ti-circle-x me-1"></i> Desactivar
                                </button>
                            @else
                                <button type="submit" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 0.75rem;">
                                    <i class="ti ti-circle-check me-1"></i> Activar
                                </button>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-secondary py-4">
                <i class="ti ti-user-off fs-1 d-block mb-2 opacity-50"></i>
                No se encontraron usuarios registrados.
            </div>
        @endforelse
    </div>
    @if($personal->hasPages())
        <div class="card-footer d-flex align-items-center">
            {{ $personal->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filter-form');
    const listContainer = document.getElementById('list-container');
    const buscarInput = document.getElementById('buscar');
    let debounceTimer;

    function performSearch(url = null) {
        if (!url && filterForm) {
            const formData = new FormData(filterForm);
            const query = new URLSearchParams(formData).toString();
            url = `${filterForm.action}?${query}`;
        }
        if (!url) return;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newList = doc.getElementById('list-container');
                if (newList && listContainer) {
                    listContainer.innerHTML = newList.innerHTML;
                }
            });
    }

    if (buscarInput) {
        buscarInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                performSearch();
            }, 300);
        });
    }

    const rolSelect = document.getElementById('id_rol');
    if (rolSelect) {
        rolSelect.addEventListener('change', () => performSearch());
    }

    const estadoSelect = document.getElementById('estado');
    if (estadoSelect) {
        estadoSelect.addEventListener('change', () => performSearch());
    }

    // Intercept pagination clicks
    if (listContainer) {
        listContainer.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                performSearch(link.href);
            }
        });
    }
});
</script>
@endsection
