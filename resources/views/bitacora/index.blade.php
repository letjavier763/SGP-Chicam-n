@extends('layouts.app')

@section('title', 'Bitácora del Sistema')
@section('page_title', 'Bitácora de Auditoría — Control de Calidad')

@section('content')

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('bitacora.index') }}" class="row g-2 align-items-end" id="search-form">
            <div class="col-md-3">
                <label class="form-label text-secondary small">Usuario</label>
                <select name="usuario_id" class="form-select">
                    <option value="">Todos los usuarios</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id_usuario }}"
                            {{ request('usuario_id') == $u->id_usuario ? 'selected' : '' }}>
                            {{ $u->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small">Acción</label>
                <select name="accion" class="form-select">
                    <option value="">Todas las acciones</option>
                    @foreach(['crear', 'editar', 'eliminar', 'login', 'logout'] as $a)
                        <option value="{{ $a }}" {{ request('accion') === $a ? 'selected' : '' }}>
                            {{ ucfirst($a) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-secondary small">Fecha Desde</label>
                <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label text-secondary small">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
            </div>
            <div class="col-md-2">
                <a href="{{ route('bitacora.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="ti ti-rotate-clockwise me-1"></i> Limpiar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla de bitácora --}}
<div class="card" id="table-container">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-shield-check me-2 text-primary"></i>
            Registro de Eventos
        </h3>
        <div class="card-options text-secondary small">
            {{ $eventos->total() }} eventos totales
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table">
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Tabla Afectada</th>
                    <th>ID Registro</th>
                    <th>Detalle</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
            @forelse($eventos as $evento)
                <tr>
                    <td>
                        <div class="fw-medium small">
                            {{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y') }}
                        </div>
                        <div class="text-secondary" style="font-size:.78rem">
                            {{ \Carbon\Carbon::parse($evento->fecha)->format('H:i:s') }}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-xs bg-blue-lt text-blue rounded-circle fw-bold">
                                {{ strtoupper(substr($evento->usuario->nombre_completo ?? '?', 0, 2)) }}
                            </span>
                            <div class="small fw-medium">
                                {{ $evento->usuario->nombre_completo ?? 'Sistema' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $accionColor = match($evento->accion) {
                                'crear'   => 'green',
                                'editar'  => 'yellow',
                                'eliminar'=> 'red',
                                'login'   => 'blue',
                                'logout'  => 'secondary',
                                default   => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $accionColor }}-lt text-{{ $accionColor }} text-capitalize">
                            {{ $evento->accion }}
                        </span>
                    </td>
                    <td>
                        <code class="small">{{ $evento->tabla_afectada }}</code>
                    </td>
                    <td class="text-secondary small">{{ $evento->id_registro_afectado ?? '—' }}</td>
                    <td class="text-secondary small" style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap"
                        title="{{ $evento->detalle }}">
                        {{ $evento->detalle ?: '—' }}
                    </td>
                    <td class="text-secondary small font-monospace">{{ $evento->ip_equipo ?: '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-secondary py-5">
                        <i class="ti ti-shield-off fs-2 d-block mb-2"></i>
                        No se encontraron eventos con los filtros aplicados.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($eventos->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Mostrando {{ $eventos->firstItem() }}–{{ $eventos->lastItem() }} de {{ $eventos->total() }} eventos
        </p>
        <ul class="pagination m-0 ms-auto">
            {{ $eventos->links('pagination::bootstrap-5') }}
        </ul>
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

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('table-container');
                if (newTable && tableContainer) {
                    tableContainer.innerHTML = newTable.innerHTML;
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
