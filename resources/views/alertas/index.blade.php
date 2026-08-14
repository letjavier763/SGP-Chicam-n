@extends('layouts.app')

@section('title', 'Alertas de Duplicidad')
@section('page_title', 'Monitoreo de Alertas de Duplicidad')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1 text-dark">Registro de Detección de Duplicados</h3>
        <p class="text-secondary mb-0 small">Detecciones automáticas por intentos de registro o modificación con DPI, expediente o número de familia duplicados.</p>
    </div>
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

<!-- Filtros de búsqueda Tabler -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('alertas.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="tipo">Tipo de Duplicidad</label>
                <select id="tipo" name="tipo" class="form-select">
                    <option value="">-- Todos los tipos --</option>
                    <option value="dpi" {{ request('tipo') === 'dpi' ? 'selected' : '' }}>DPI Duplicado</option>
                    <option value="numero_expediente" {{ request('tipo') === 'numero_expediente' ? 'selected' : '' }}>Expediente Duplicado</option>
                    <option value="numero_familia" {{ request('tipo') === 'numero_familia' ? 'selected' : '' }}>Familia Duplicada</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label" for="buscar">Buscar Valor</label>
                <input type="text" id="buscar" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Ej. 1987654320101 o EXP-...">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="ti ti-search me-1"></i> Filtrar</button>
                <a href="{{ route('alertas.index') }}" class="btn btn-secondary"><i class="ti ti-rotate-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Alertas -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>ID Alerta</th>
                    <th>Fecha y Hora Detección</th>
                    <th>Usuario Responsable</th>
                    <th>Tipo de Duplicidad</th>
                    <th>Valor Duplicado Intentado</th>
                    <th>Acción / Estado</th>
                    <th class="text-end">Gestionar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alertas as $alerta)
                    <tr>
                        <td><strong>#{{ $alerta->id_alerta }}</strong></td>
                        <td>{{ optional($alerta->fecha_deteccion)->format('d/m/Y H:i A') }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $alerta->usuario->nombre_completo ?? 'Sistema' }}</div>
                            <div class="text-secondary small">{{ $alerta->usuario->username ?? '' }}</div>
                        </td>
                        <td>
                            @if($alerta->tipo_duplicado === 'dpi')
                                <span class="badge bg-amber-lt">DPI</span>
                            @elseif($alerta->tipo_duplicado === 'numero_expediente')
                                <span class="badge bg-red-lt">Expediente Físico</span>
                            @else
                                <span class="badge bg-purple-lt">No. Familia</span>
                            @endif
                        </td>
                        <td><code class="fs-3 fw-bold text-dark">{{ $alerta->valor_duplicado }}</code></td>
                        <td>
                            @if($alerta->accion_tomada === 'registro_bloqueado' || $alerta->accion_tomada === 'modificacion_bloqueada')
                                <span class="badge bg-red-lt"><i class="ti ti-shield-x me-1"></i> Bloqueado por Sistema</span>
                            @elseif($alerta->accion_tomada === 'corregida')
                                <span class="badge bg-green-lt"><i class="ti ti-check me-1"></i> Corregida</span>
                            @elseif($alerta->accion_tomada === 'ignorada')
                                <span class="badge bg-secondary-lt"><i class="ti ti-minus me-1"></i> Ignorada</span>
                            @else
                                <span class="badge bg-warning-lt">Pendiente</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('alertas.resolver', $alerta->id_alerta) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="accion_tomada" value="corregida">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ti ti-check me-1"></i> Marcar Atendida
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">
                            No se registraron alertas de duplicidad con los criterios seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $alertas->links() }}
</div>
@endsection
