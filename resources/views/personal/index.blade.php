@extends('layouts.app')

@section('title', 'Gestión de Personal')
@section('page_title', 'Personal y Usuarios')
@section('page_subtitle', 'Gestión de cuentas, roles y accesos al sistema SGP')

@section('page_actions')
<a href="{{ route('personal.create') }}" class="btn btn-primary shadow-sm">
    <i class="ti ti-user-plus me-1"></i> Registrar Personal
</a>
@endsection

@section('content')
<!-- Tarjetas Resumen de Métricas (KPIs Horizontal Compacto) -->
<div class="card card-sm shadow-sm border-0 mb-3 bg-white">
    <div class="card-body p-2 p-md-3">
        <div class="row g-0 text-center align-items-center">
            <div class="col-4 border-end">
                <div class="text-secondary small fw-medium" style="font-size: 0.725rem; letter-spacing: 0.04em;">TOTAL</div>
                <div class="h3 mb-0 fw-bold text-dark">{{ $stats['total'] ?? $personal->total() }}</div>
            </div>
            <div class="col-4 border-end">
                <div class="text-secondary small fw-medium" style="font-size: 0.725rem; letter-spacing: 0.04em;">ACTIVOS</div>
                <div class="h3 mb-0 fw-bold text-success d-inline-flex align-items-center justify-content-center gap-1">
                    <span class="badge-dot bg-success"></span>
                    {{ $stats['activos'] ?? '-' }}
                </div>
            </div>
            <div class="col-4">
                <div class="text-secondary small fw-medium" style="font-size: 0.725rem; letter-spacing: 0.04em;">INACTIVOS</div>
                <div class="h3 mb-0 fw-bold text-secondary d-inline-flex align-items-center justify-content-center gap-1">
                    <span class="badge-dot bg-secondary"></span>
                    {{ $stats['inactivos'] ?? '-' }}
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible shadow-sm fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-2 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible shadow-sm fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle fs-2 me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

<!-- Barra de Búsqueda y Filtros Optimizada -->
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body p-2 p-md-3">
        <form method="GET" action="{{ route('personal.index') }}" id="filter-form" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text" id="buscar" name="buscar" class="form-control border-start-0 ps-0" 
                           value="{{ request('buscar') }}" 
                           placeholder="Buscar por nombre o usuario..." 
                           autocomplete="off">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select id="id_rol" name="id_rol" class="form-select">
                    <option value="">Rol: Todos</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id_rol }}" {{ request('id_rol') == $rol->id_rol ? 'selected' : '' }}>
                            {{ $rol->nombre_rol }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select id="estado" name="estado" class="form-select">
                    <option value="">Estado: Todos</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="ti ti-filter me-1"></i> Filtrar
                </button>
                @if(request('buscar') || request('id_rol') || request('estado'))
                    <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary" title="Limpiar Filtros">
                        <i class="ti ti-rotate-clockwise me-1 d-md-none"></i>
                        <span class="d-md-none">Limpiar</span>
                        <i class="ti ti-x d-none d-md-inline"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Contenedor Principal de Lista / Tabla -->
<div class="card shadow-sm border-0" id="list-container">
    <div class="card-header d-flex justify-content-between align-items-center py-2 px-3 bg-white">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title mb-0 fw-bold text-dark">
                Personal Registrado
            </h4>
            <span class="badge bg-primary-lt text-primary rounded-pill px-2">
                {{ $personal->total() }} {{ $personal->total() == 1 ? 'usuario' : 'usuarios' }}
            </span>
        </div>
        @if(request('buscar') || request('id_rol') || request('estado'))
            <span class="text-secondary small d-none d-sm-inline">
                <i class="ti ti-filter me-1"></i> Filtros activos
            </span>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- Vista de Tabla para Pantallas Medianas/Grandes -->
    <!-- ========================================== -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-vcenter table-hover card-table mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="text-secondary fw-semibold">Usuario / Nombre</th>
                    <th class="text-secondary fw-semibold">Nombre de Usuario</th>
                    <th class="text-secondary fw-semibold">Rol Asignado</th>
                    <th class="text-secondary fw-semibold">Último Acceso</th>
                    <th class="text-secondary fw-semibold text-center">Estado</th>
                    <th class="text-secondary fw-semibold text-end pe-3 w-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($personal as $p)
                @php
                    $roleColors = [
                        'Administrador' => ['bg' => 'danger-lt', 'text' => 'danger', 'icon' => 'ti-shield-lock'],
                        'Recepcionista' => ['bg' => 'primary-lt', 'text' => 'primary', 'icon' => 'ti-device-desktop'],
                        'Director'      => ['bg' => 'warning-lt', 'text' => 'warning', 'icon' => 'ti-crown'],
                    ];
                    $roleConf = $roleColors[$p->rol->nombre_rol] ?? ['bg' => 'secondary-lt', 'text' => 'secondary', 'icon' => 'ti-user'];
                    $isSelf = Auth::id() == $p->id_usuario;
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="position-relative">
                                <span class="avatar bg-blue-lt text-blue rounded-circle fw-bold">
                                    {{ strtoupper(substr($p->nombre_completo, 0, 2)) }}
                                </span>
                                <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $p->activo ? 'success' : 'secondary' }} border border-white rounded-circle"></span>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">
                                    {{ $p->nombre_completo }}
                                    @if($isSelf)
                                        <span class="badge bg-purple-lt text-purple ms-1">Tú</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-secondary font-monospace border">
                            {{ $p->username }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $roleConf['bg'] }} text-{{ $roleConf['text'] }} d-inline-flex align-items-center gap-1">
                            <i class="ti {{ $roleConf['icon'] }} fs-5"></i>
                            {{ $p->rol->nombre_rol }}
                        </span>
                    </td>
                    <td>
                        @if($p->ultimo_acceso)
                            <div class="small text-dark fw-medium">
                                {{ \Carbon\Carbon::parse($p->ultimo_acceso)->format('d/m/Y H:i') }}
                            </div>
                            <div class="text-secondary" style="font-size: 0.775rem;">
                                <i class="ti ti-clock me-1"></i>{{ \Carbon\Carbon::parse($p->ultimo_acceso)->diffForHumans() }}
                            </div>
                        @else
                            <span class="badge bg-light text-muted fw-normal">Sin ingresos</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($p->activo)
                            <span class="badge bg-success-lt text-success d-inline-flex align-items-center gap-1">
                                <span class="badge-dot bg-success"></span> Activo
                            </span>
                        @else
                            <span class="badge bg-secondary-lt text-secondary d-inline-flex align-items-center gap-1">
                                <span class="badge-dot bg-secondary"></span> Inactivo
                            </span>
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('personal.edit', $p->id_usuario) }}" class="btn btn-sm btn-outline-primary" title="Editar Información">
                                <i class="ti ti-edit me-1"></i> Editar
                            </a>

                            @if(!$isSelf)
                                <form action="{{ route('personal.toggle-status', $p->id_usuario) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($p->activo)
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Desactivar usuario" onclick="return confirm('¿Está seguro de desactivar a {{ $p->nombre_completo }}? Perderá el acceso al sistema inmediatamente.')">
                                            <i class="ti ti-ban me-1"></i> Desactivar
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Activar usuario">
                                            <i class="ti ti-check me-1"></i> Activar
                                        </button>
                                    @endif
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-5">
                        <div class="empty">
                            <div class="empty-icon text-muted">
                                <i class="ti ti-user-search fs-1"></i>
                            </div>
                            <p class="empty-title fw-bold text-dark">No se encontraron usuarios</p>
                            <p class="empty-subtitle text-secondary">Intente cambiar los criterios de búsqueda o limpie los filtros.</p>
                            <div class="empty-action">
                                <a href="{{ route('personal.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="ti ti-rotate-clockwise me-1"></i> Restablecer filtros
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- ========================================== -->
    <!-- Vista de Tarjetas Adaptada para Móviles     -->
    <!-- ========================================== -->
    <div class="divide-y d-md-none">
        @forelse($personal as $p)
            @php
                $roleColors = [
                    'Administrador' => ['bg' => 'danger-lt', 'text' => 'danger', 'icon' => 'ti-shield-lock'],
                    'Recepcionista' => ['bg' => 'primary-lt', 'text' => 'primary', 'icon' => 'ti-device-desktop'],
                    'Director'      => ['bg' => 'warning-lt', 'text' => 'warning', 'icon' => 'ti-crown'],
                ];
                $roleConf = $roleColors[$p->rol->nombre_rol] ?? ['bg' => 'secondary-lt', 'text' => 'secondary', 'icon' => 'ti-user'];
                $isSelf = Auth::id() == $p->id_usuario;
            @endphp
            <div class="p-3 bg-white">
                <!-- Cabecera de la tarjeta: Avatar + Nombre + Estado -->
                <div class="d-flex align-items-start gap-2 mb-2">
                    <div class="position-relative flex-shrink-0">
                        <span class="avatar avatar-md bg-blue-lt text-blue rounded-circle fw-bold">
                            {{ strtoupper(substr($p->nombre_completo, 0, 2)) }}
                        </span>
                        <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $p->activo ? 'success' : 'secondary' }} border border-white rounded-circle"></span>
                    </div>

                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center justify-content-between gap-1">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">
                                {{ $p->nombre_completo }}
                            </div>
                            @if($isSelf)
                                <span class="badge bg-purple-lt text-purple flex-shrink-0">Tú</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                            <span class="badge bg-light text-secondary font-monospace border px-1.5 py-0.5" style="font-size: 0.775rem;">
                                {{ '@' . $p->username }}
                            </span>
                            <span class="badge bg-{{ $roleConf['bg'] }} text-{{ $roleConf['text'] }} d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                <i class="ti {{ $roleConf['icon'] }}"></i>
                                {{ $p->rol->nombre_rol }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Detalle de estado y último acceso -->
                <div class="d-flex justify-content-between align-items-center py-2 px-2.5 bg-light rounded-2 my-2 text-secondary" style="font-size: 0.775rem;">
                    <div class="d-flex align-items-center gap-1">
                        <i class="ti ti-clock text-muted"></i>
                        @if($p->ultimo_acceso)
                            <span>Acceso: {{ \Carbon\Carbon::parse($p->ultimo_acceso)->diffForHumans() }}</span>
                        @else
                            <span class="text-muted">Sin ingresos registrados</span>
                        @endif
                    </div>
                    <div>
                        @if($p->activo)
                            <span class="badge bg-success-lt text-success px-2 py-0.5">
                                <span class="badge-dot bg-success me-1"></span> Activo
                            </span>
                        @else
                            <span class="badge bg-secondary-lt text-secondary px-2 py-0.5">
                                <span class="badge-dot bg-secondary me-1"></span> Inactivo
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Botones de Acción Móvil -->
                <div class="d-flex gap-2 pt-1">
                    <a href="{{ route('personal.edit', $p->id_usuario) }}" class="btn btn-outline-primary btn-sm flex-fill py-1.5">
                        <i class="ti ti-edit me-1"></i> Editar
                    </a>

                    @if(!$isSelf)
                        <form action="{{ route('personal.toggle-status', $p->id_usuario) }}" method="POST" class="flex-fill d-inline-block">
                            @csrf
                            @method('PATCH')
                            @if($p->activo)
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-1.5" onclick="return confirm('¿Está seguro de desactivar a {{ $p->nombre_completo }}?')">
                                    <i class="ti ti-ban me-1"></i> Desactivar
                                </button>
                            @else
                                <button type="submit" class="btn btn-outline-success btn-sm w-100 py-1.5">
                                    <i class="ti ti-check me-1"></i> Activar
                                </button>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-secondary py-5 px-3">
                <i class="ti ti-user-search fs-1 d-block mb-2 text-muted opacity-50"></i>
                <div class="fw-bold text-dark mb-1">No se encontraron usuarios</div>
                <div class="small text-secondary mb-3">No hay registros que coincidan con la búsqueda.</div>
                <a href="{{ route('personal.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="ti ti-rotate-clockwise me-1"></i> Restablecer filtros
                </a>
            </div>
        @endforelse
    </div>

    @if($personal->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-center justify-content-md-between bg-white py-2">
            <div class="text-secondary small d-none d-md-block">
                Mostrando {{ $personal->firstItem() }} a {{ $personal->lastItem() }} de {{ $personal->total() }} registros
            </div>
            <div>
                {{ $personal->links() }}
            </div>
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

        if (listContainer) {
            listContainer.style.opacity = '0.6';
            listContainer.style.pointerEvents = 'none';
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newList = doc.getElementById('list-container');
                if (newList && listContainer) {
                    listContainer.innerHTML = newList.innerHTML;
                }
            })
            .catch(err => {
                console.error('Error al actualizar listado:', err);
            })
            .finally(() => {
                if (listContainer) {
                    listContainer.style.opacity = '1';
                    listContainer.style.pointerEvents = 'auto';
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

    // Interceptar paginación AJAX
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
