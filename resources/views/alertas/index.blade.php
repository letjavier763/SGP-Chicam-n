@extends('layouts.app')

@section('title', 'Alertas de Duplicidad')
@section('page_title', 'Monitoreo de Alertas de Duplicidad')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 d-none d-md-flex">
    <div>
        <h3 class="mb-1 text-dark">Registro de Detección de Duplicados</h3>
        <p class="text-secondary mb-0 small">Detecciones automáticas por intentos de registro o modificación con DPI, expediente o número de familia duplicados.</p>
    </div>
</div>

<!-- Filtros de búsqueda -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('alertas.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label" for="tipo">Tipo de Duplicidad</label>
                <select id="tipo" name="tipo" class="form-select">
                    <option value="">-- Todos los tipos --</option>
                    <option value="dpi" {{ request('tipo') === 'dpi' ? 'selected' : '' }}>DPI Duplicado</option>
                    <option value="numero_expediente" {{ request('tipo') === 'numero_expediente' ? 'selected' : '' }}>Expediente Duplicado</option>
                    <option value="numero_familia" {{ request('tipo') === 'numero_familia' ? 'selected' : '' }}>Familia Duplicada</option>
                </select>
            </div>
            <div class="col-12 col-md-5">
                <label class="form-label" for="buscar">Buscar Valor</label>
                <input type="text" id="buscar" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Ej. 1987654320101 o EXP-...">
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="ti ti-search me-1"></i> Filtrar</button>
                <a href="{{ route('alertas.index') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="ti ti-rotate-clockwise me-1"></i> Limpiar</a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Alertas -->
<div class="card shadow-sm border-0">
    <!-- Vista de Tabla para Escritorio -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-vcenter card-table">
            <thead class="bg-light">
                <tr>
                    <th>ID Alerta</th>
                    <th>Fecha y Hora Detección</th>
                    <th>Usuario Responsable</th>
                    <th>Tipo de Duplicidad</th>
                    <th>Valor Duplicado Intentado</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alertas as $alerta)
                    <tr>
                        <td><strong class="text-primary font-monospace">#{{ $alerta->id_alerta }}</strong></td>
                        <td>{{ optional($alerta->fecha_deteccion)->format('d/m/Y H:i A') }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $alerta->usuario->nombre_completo ?? 'Sistema' }}</div>
                            <div class="text-secondary small font-monospace">{{ $alerta->usuario->username ?? '' }}</div>
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
                        <td><code class="fw-bold text-dark">{{ $alerta->valor_duplicado }}</code></td>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-5">
                            <i class="ti ti-bell-off fs-1 d-block mb-2 text-muted opacity-50"></i>
                            No se registraron alertas de duplicidad con los criterios seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Vista de Tarjetas para Móviles -->
    <div class="divide-y d-md-none">
        @forelse($alertas as $alerta)
            <div class="p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-secondary font-monospace border">
                            #{{ $alerta->id_alerta }}
                        </span>
                        @if($alerta->tipo_duplicado === 'dpi')
                            <span class="badge bg-amber-lt">DPI</span>
                        @elseif($alerta->tipo_duplicado === 'numero_expediente')
                            <span class="badge bg-red-lt">Exp. Físico</span>
                        @else
                            <span class="badge bg-purple-lt">No. Familia</span>
                        @endif
                    </div>
                    <div class="text-secondary small" style="font-size: 0.775rem;">
                        <i class="ti ti-clock me-1 text-muted"></i>{{ optional($alerta->fecha_deteccion)->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div class="bg-light rounded p-2 my-2 border">
                    <div class="text-secondary small mb-1">Valor duplicado intentado:</div>
                    <code class="fw-bold text-dark fs-3 d-block word-break-all">{{ $alerta->valor_duplicado }}</code>
                </div>

                <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                    <div class="small min-w-0">
                        <div class="text-dark fw-bold text-truncate">{{ $alerta->usuario->nombre_completo ?? 'Sistema' }}</div>
                        <div class="text-secondary" style="font-size: 0.75rem;">
                            @if($alerta->accion_tomada === 'registro_bloqueado' || $alerta->accion_tomada === 'modificacion_bloqueada')
                                <span class="text-danger"><i class="ti ti-shield-x me-1"></i>Bloqueado</span>
                            @elseif($alerta->accion_tomada === 'corregida')
                                <span class="text-success"><i class="ti ti-check me-1"></i>Corregida</span>
                            @elseif($alerta->accion_tomada === 'ignorada')
                                <span class="text-secondary"><i class="ti ti-minus me-1"></i>Ignorada</span>
                            @else
                                <span class="text-warning"><i class="ti ti-clock me-1"></i>Pendiente</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-secondary py-5 px-3">
                <i class="ti ti-bell-off fs-1 d-block mb-2 text-muted opacity-50"></i>
                <div class="fw-bold text-dark mb-1">Sin alertas de duplicidad</div>
                <div class="small text-secondary">No hay registros con los criterios seleccionados.</div>
            </div>
        @endforelse
    </div>
</div>

<div class="mt-3">
    {{ $alertas->links() }}
</div>
@endsection
