@extends('layouts.app')

@section('title', 'Bitácora del Sistema')
@section('page_title', 'Bitácora de Auditoría — Control de Calidad')

@section('content')

{{-- Filtros de búsqueda --}}
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body p-2 p-md-3">
        <form method="GET" action="{{ route('bitacora.index') }}" class="row g-2 align-items-end" id="search-form">
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label text-secondary small mb-1">Usuario</label>
                <select name="usuario_id" class="form-select">
                    <option value="">Usuario: Todos</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id_usuario }}"
                            {{ request('usuario_id') == $u->id_usuario ? 'selected' : '' }}>
                            {{ $u->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label text-secondary small mb-1">Acción</label>
                <select name="accion" class="form-select">
                    <option value="">Acción: Todas</option>
                    @foreach(['crear', 'editar', 'eliminar', 'login', 'logout', 'creacion_usuario', 'edicion_usuario', 'cambio_estado_usuario', 'inicio_sesion', 'cierre_sesion'] as $a)
                        <option value="{{ $a }}" {{ request('accion') === $a ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $a)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label text-secondary small mb-1">Desde</label>
                <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label text-secondary small mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
            </div>
            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="ti ti-search me-1"></i> Filtrar
                </button>
                @if(request('usuario_id') || request('accion') || request('fecha_desde') || request('fecha_hasta'))
                    <a href="{{ route('bitacora.index') }}" class="btn btn-outline-secondary" title="Limpiar Filtros">
                        <i class="ti ti-rotate-clockwise me-1 d-md-none"></i>
                        <span class="d-md-none">Limpiar</span>
                        <i class="ti ti-x d-none d-md-inline"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Contenedor Principal de Lista / Tabla --}}
<div class="card shadow-sm border-0" id="table-container">
    <div class="card-header d-flex justify-content-between align-items-center py-2 px-3 bg-white border-bottom">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title mb-0 fw-bold text-dark">
                <i class="ti ti-shield-check me-2 text-primary"></i>Registro de Eventos
            </h4>
            <span class="badge bg-primary-lt text-primary rounded-pill px-2">
                {{ $eventos->total() }} {{ $eventos->total() == 1 ? 'evento' : 'eventos' }}
            </span>
        </div>
        <div class="card-options text-secondary small d-none d-sm-block">
            Página {{ $eventos->currentPage() }} de {{ $eventos->lastPage() }}
        </div>
    </div>

    <!-- ========================================== -->
    <!-- Vista de Tabla para Pantallas Medianas/Grandes -->
    <!-- ========================================== -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-vcenter table-hover card-table mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="text-secondary fw-semibold" style="font-size:.78rem">Fecha / Hora</th>
                    <th class="text-secondary fw-semibold" style="font-size:.78rem">Usuario</th>
                    <th class="text-secondary fw-semibold" style="font-size:.78rem">Acción</th>
                    <th class="text-secondary fw-semibold" style="font-size:.78rem">Tabla Afectada</th>
                    <th class="text-secondary fw-semibold" style="font-size:.78rem">ID Reg.</th>
                    <th class="text-secondary fw-semibold" style="font-size:.78rem">Detalle</th>
                    <th class="text-secondary fw-semibold" style="font-size:.78rem">IP</th>
                </tr>
            </thead>
            <tbody>
            @forelse($eventos as $evento)
                @php
                    $act = strtolower($evento->accion);
                    $actConf = match(true) {
                        str_contains($act, 'crea')   => ['label' => 'Crear', 'bg' => 'success-lt', 'text' => 'success', 'icon' => 'ti-plus'],
                        str_contains($act, 'edit')   => ['label' => 'Editar', 'bg' => 'warning-lt', 'text' => 'warning', 'icon' => 'ti-edit'],
                        str_contains($act, 'elim')   => ['label' => 'Eliminar', 'bg' => 'danger-lt', 'text' => 'danger', 'icon' => 'ti-trash'],
                        str_contains($act, 'login') || str_contains($act, 'inicio') => ['label' => 'Inicio Sesión', 'bg' => 'info-lt', 'text' => 'info', 'icon' => 'ti-login'],
                        str_contains($act, 'logout') || str_contains($act, 'cierre') => ['label' => 'Cierre Sesión', 'bg' => 'secondary-lt', 'text' => 'secondary', 'icon' => 'ti-logout'],
                        str_contains($act, 'estado') => ['label' => 'Estado', 'bg' => 'purple-lt', 'text' => 'purple', 'icon' => 'ti-toggle-left'],
                        default                      => ['label' => ucwords(str_replace('_', ' ', $evento->accion)), 'bg' => 'primary-lt', 'text' => 'primary', 'icon' => 'ti-activity'],
                    };
                @endphp
                <tr style="font-size:.8rem">
                    <td class="text-nowrap">
                        <div class="fw-medium text-dark" style="font-size:.78rem">
                            {{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y') }}
                        </div>
                        <div class="text-secondary" style="font-size:.72rem">
                            {{ \Carbon\Carbon::parse($evento->fecha)->format('H:i:s') }}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <span class="avatar avatar-xs bg-blue-lt text-blue rounded-circle fw-bold flex-shrink-0">
                                {{ strtoupper(substr($evento->usuario->nombre_completo ?? '?', 0, 2)) }}
                            </span>
                            <div class="fw-medium text-dark" style="font-size:.78rem; max-width:110px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap">
                                {{ $evento->usuario->nombre_completo ?? 'Sistema' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-{{ $actConf['bg'] }} text-{{ $actConf['text'] }} d-inline-flex align-items-center gap-1" style="font-size:.7rem; white-space:normal; line-height:1.2">
                            <i class="ti {{ $actConf['icon'] }}"></i>
                            {{ $actConf['label'] }}
                        </span>
                    </td>
                    <td>
                        <code style="font-size:.72rem">{{ $evento->tabla_afectada }}</code>
                    </td>
                    <td class="text-secondary font-monospace" style="font-size:.72rem">{{ $evento->id_registro_afectado ?? '—' }}</td>
                    <td class="text-secondary" style="font-size:.78rem; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap"
                        title="{{ $evento->detalle }}">
                        {{ $evento->detalle ?: '—' }}
                    </td>
                    <td class="text-secondary font-monospace" style="font-size:.72rem">{{ $evento->ip_equipo ?: '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-secondary py-5">
                        <i class="ti ti-shield-off fs-1 d-block mb-2 text-muted opacity-50"></i>
                        <p class="fw-bold text-dark mb-1">No se encontraron eventos</p>
                        <p class="small text-secondary mb-0">Pruebe ajustando los filtros de fecha o usuario.</p>
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
        @forelse($eventos as $evento)
            @php
                $act = strtolower($evento->accion);
                $actConf = match(true) {
                    str_contains($act, 'crea')   => ['label' => 'Crear', 'bg' => 'success-lt', 'text' => 'success', 'icon' => 'ti-plus'],
                    str_contains($act, 'edit')   => ['label' => 'Editar', 'bg' => 'warning-lt', 'text' => 'warning', 'icon' => 'ti-edit'],
                    str_contains($act, 'elim')   => ['label' => 'Eliminar', 'bg' => 'danger-lt', 'text' => 'danger', 'icon' => 'ti-trash'],
                    str_contains($act, 'login') || str_contains($act, 'inicio') => ['label' => 'Inicio Sesión', 'bg' => 'info-lt', 'text' => 'info', 'icon' => 'ti-login'],
                    str_contains($act, 'logout') || str_contains($act, 'cierre') => ['label' => 'Cierre Sesión', 'bg' => 'secondary-lt', 'text' => 'secondary', 'icon' => 'ti-logout'],
                    str_contains($act, 'estado') => ['label' => 'Estado', 'bg' => 'purple-lt', 'text' => 'purple', 'icon' => 'ti-toggle-left'],
                    default                      => ['label' => ucwords(str_replace('_', ' ', $evento->accion)), 'bg' => 'primary-lt', 'text' => 'primary', 'icon' => 'ti-activity'],
                };
            @endphp
            <div class="p-2 bg-white">
                <!-- Línea 1: Avatar + Usuario + Acción Badge -->
                <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                    <div class="d-flex align-items-center gap-1 min-w-0 flex-grow-1">
                        <span class="avatar avatar-xs bg-blue-lt text-blue rounded-circle fw-bold flex-shrink-0" style="width:28px;height:28px;font-size:.72rem">
                            {{ strtoupper(substr($evento->usuario->nombre_completo ?? '?', 0, 2)) }}
                        </span>
                        <div class="fw-semibold text-dark text-truncate" style="font-size:.82rem">
                            {{ $evento->usuario->nombre_completo ?? 'Sistema' }}
                        </div>
                    </div>
                    <span class="badge bg-{{ $actConf['bg'] }} text-{{ $actConf['text'] }} flex-shrink-0 d-inline-flex align-items-center gap-1" style="font-size:.68rem; white-space:normal; line-height:1.3; text-align:center; max-width:90px">
                        <i class="ti {{ $actConf['icon'] }}"></i>
                        {{ $actConf['label'] }}
                    </span>
                </div>

                <!-- Línea 2: Timestamp, Tabla e ID -->
                <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                    <span class="badge bg-light text-secondary border font-monospace" style="font-size:.68rem">
                        <i class="ti ti-database text-muted"></i> {{ $evento->tabla_afectada }}
                    </span>
                    @if($evento->id_registro_afectado)
                        <span class="badge bg-light text-primary border font-monospace" style="font-size:.68rem">
                            #{{ $evento->id_registro_afectado }}
                        </span>
                    @endif
                    <span class="text-secondary ms-auto text-nowrap" style="font-size:.7rem">
                        <i class="ti ti-clock text-muted"></i> {{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y H:i') }}
                    </span>
                </div>

                <!-- Línea 3: Detalle e IP -->
                <div class="bg-light rounded-2 px-2 py-1 border" style="font-size:.75rem">
                    <div class="text-dark fw-medium" style="line-height:1.4">
                        {{ $evento->detalle ?: 'Sin detalle registrado' }}
                    </div>
                    @if($evento->ip_equipo)
                        <div class="text-muted font-monospace mt-1" style="font-size:.68rem">
                            <i class="ti ti-network"></i> {{ $evento->ip_equipo }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-secondary py-5 px-3">
                <i class="ti ti-shield-off fs-1 d-block mb-2 text-muted opacity-50"></i>
                <div class="fw-bold text-dark mb-1">No se encontraron eventos</div>
                <div class="small text-secondary mb-3">No hay registros que coincidan con los filtros aplicados.</div>
                <a href="{{ route('bitacora.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="ti ti-rotate-clockwise me-1"></i> Restablecer filtros
                </a>
            </div>
        @endforelse
    </div>

    @if($eventos->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-center justify-content-md-between bg-white py-2">
        <div class="text-secondary small d-none d-md-block">
            Mostrando {{ $eventos->firstItem() }} a {{ $eventos->lastItem() }} de {{ $eventos->total() }} eventos
        </div>
        <div>
            {{ $eventos->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.getElementById('search-form');
    const tableContainer = document.getElementById('table-container');

    function performSearch(url = null) {
        if (!url && searchForm) {
            const formData = new FormData(searchForm);
            const query = new URLSearchParams(formData).toString();
            url = `${searchForm.action}?${query}`;
        }
        if (!url) return;

        if (tableContainer) {
            tableContainer.style.opacity = '0.6';
            tableContainer.style.pointerEvents = 'none';
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('table-container');
                if (newTable && tableContainer) {
                    tableContainer.innerHTML = newTable.innerHTML;
                }
            })
            .catch(err => {
                console.error('Error al filtrar bitácora:', err);
            })
            .finally(() => {
                if (tableContainer) {
                    tableContainer.style.opacity = '1';
                    tableContainer.style.pointerEvents = 'auto';
                }
            });
    }

    const selects = document.querySelectorAll('#search-form select');
    selects.forEach(select => {
        select.addEventListener('change', () => performSearch());
    });

    const dates = document.querySelectorAll('#search-form input[type="date"]');
    dates.forEach(date => {
        date.addEventListener('change', () => performSearch());
    });

    // Intercept pagination clicks
    if (tableContainer) {
        tableContainer.addEventListener('click', function(e) {
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
