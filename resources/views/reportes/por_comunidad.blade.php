@extends('layouts.app')

@section('title', 'Personas por Comunidad')
@section('page_title', 'Reporte de Personas por Comunidad')
@section('page_subtitle', 'Centro de Atención Permanente · Chicamán')

@section('styles')
<style>
.community-pill {
    cursor: pointer;
    transition: all 0.15s ease;
    border: 1px solid rgba(148, 163, 184, 0.3);
    text-decoration: none;
    font-size: 0.78rem;
}
.community-pill:hover {
    background: #0057cd !important;
    color: #ffffff !important;
    border-color: #0057cd !important;
    transform: translateY(-1px);
}
.community-pill.active {
    background: #0057cd !important;
    color: #ffffff !important;
    border-color: #0057cd !important;
    font-weight: 600;
}
.stat-card-clean {
    border-radius: 10px;
    border: 1px solid rgba(15, 39, 68, 0.08);
    background: #ffffff;
    box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    transition: transform 0.15s ease;
}
.stat-card-clean:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);
}
@media print {
    .no-print { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    .table td, .table th { font-size: 0.8rem !important; }
}
</style>
@endsection

@section('content')
<div class="container-xl">

    {{-- ── Barra de Navegación y Acciones Superiores ──────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 no-print">
        <div>
            <a href="{{ route('reportes.index') }}" class="btn btn-link text-decoration-none p-0 text-muted">
                <i class="ti ti-arrow-left me-1"></i> Volver a Reportería Estadística
            </a>
            <h2 class="page-title text-primary fw-bold mt-1 mb-0">
                <i class="ti ti-map-pins me-1 text-azure"></i> Personas por Comunidad
            </h2>
            <span class="text-muted small">Censo y padrón de pacientes clasificados por aldea, caserío o sector</span>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reportes.exportar.por-comunidad', request()->all()) }}" class="btn btn-outline-danger shadow-sm">
                <i class="ti ti-file-type-pdf me-1"></i> Descargar PDF
            </a>
            <a href="{{ route('reportes.exportar.por-comunidad', array_merge(request()->all(), ['preview' => 1])) }}" target="_blank" class="btn btn-outline-primary shadow-sm">
                <i class="ti ti-eye me-1"></i> Vista Previa PDF
            </a>
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary shadow-sm">
                <i class="ti ti-printer me-1"></i> Imprimir
            </button>
        </div>
    </div>

    {{-- ── Tarjeta de Filtros Seleccionables ───────────────────────── --}}
    <div class="card mb-3 border-0 shadow-sm no-print" style="border-radius: 12px; border: 1px solid rgba(11,28,48,0.08) !important;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('reportes.por-comunidad') }}" id="form-filtro-comunidad">
                <div class="row g-2 align-items-end">
                    
                    {{-- 1. Selector de Comunidad --}}
                    <div class="col-md-5 col-lg-4">
                        <label class="form-label fw-bold mb-1" for="select_comunidad">
                            <i class="ti ti-building-community me-1 text-primary"></i> Seleccionar Comunidad / Aldea:
                        </label>
                        <select class="form-select form-select-sm" name="id_comunidad" id="select_comunidad" onchange="document.getElementById('form-filtro-comunidad').submit()">
                            <option value="">-- Todas las Comunidades ({{ $comunidades->sum('total_pacientes') }} personas) --</option>
                            @foreach($comunidades as $com)
                                <option value="{{ $com->id_comunidad }}" {{ (string)$idComunidad === (string)$com->id_comunidad ? 'selected' : '' }}>
                                    {{ $com->nombre }} ({{ $com->total_pacientes }} {{ $com->total_pacientes == 1 ? 'persona' : 'personas' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Filtro por Sexo --}}
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label fw-bold mb-1" for="select_sexo">
                            <i class="ti ti-gender-intergender me-1 text-secondary"></i> Sexo:
                        </label>
                        <select class="form-select form-select-sm" name="sexo" id="select_sexo" onchange="document.getElementById('form-filtro-comunidad').submit()">
                            <option value="">Todos (M y F)</option>
                            <option value="F" {{ $sexo === 'F' ? 'selected' : '' }}>Mujeres (F)</option>
                            <option value="M" {{ $sexo === 'M' ? 'selected' : '' }}>Hombres (M)</option>
                        </select>
                    </div>

                    {{-- 3. Búsqueda Rápida --}}
                    <div class="col-md-4 col-lg-4">
                        <label class="form-label fw-bold mb-1" for="input_buscar">
                            <i class="ti ti-search me-1 text-muted"></i> Buscar en lista:
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="buscar" id="input_buscar" value="{{ $buscar }}" placeholder="Nombre, apellido, exp, DPI...">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i>
                            </button>
                            @if($idComunidad || $sexo || $buscar)
                                <a href="{{ route('reportes.por-comunidad') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                                    <i class="ti ti-x"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- 4. Contador de Resultados --}}
                    <div class="col-12 col-lg-2 text-lg-end">
                        <span class="badge bg-primary-lt p-2 fs-6">
                            <i class="ti ti-user-check me-1"></i> {{ $totalPersonas }} {{ $totalPersonas == 1 ? 'persona' : 'personas' }}
                        </span>
                    </div>
                </div>

                {{-- ── Acceso Rápido por Pills de Comunidades Más Frecuentes ── --}}
                @php
                    $comunidadesPrincipales = $comunidades->sortByDesc('total_pacientes')->where('total_pacientes', '>', 0)->take(12);
                @endphp
                @if($comunidadesPrincipales->isNotEmpty())
                <div class="mt-2 pt-2 border-top">
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <span class="text-muted small fw-bold me-1">
                            <i class="ti ti-flame me-1 text-danger"></i> Comunidades destacadas:
                        </span>
                        <a href="{{ route('reportes.por-comunidad') }}" class="badge rounded-pill bg-light text-dark p-1.5 community-pill {{ empty($idComunidad) ? 'active' : '' }}">
                            Todas ({{ $comunidades->sum('total_pacientes') }})
                        </a>
                        @foreach($comunidadesPrincipales as $cTop)
                            <a href="{{ route('reportes.por-comunidad', ['id_comunidad' => $cTop->id_comunidad]) }}" class="badge rounded-pill bg-light text-dark p-1.5 community-pill {{ (string)$idComunidad === (string)$cTop->id_comunidad ? 'active' : '' }}">
                                {{ $cTop->nombre }} <span class="badge bg-secondary text-white rounded-pill ms-1">{{ $cTop->total_pacientes }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- ── Métricas y Estadísticas de la Selección ────────────────── --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="stat-card-clean p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Total Personas</span>
                    <i class="ti ti-users text-primary fs-3"></i>
                </div>
                <div class="h2 mb-0 fw-bold text-primary">{{ $totalPersonas }}</div>
                <span class="text-muted small">
                    @if($comunidadSeleccionada)
                        En <strong>{{ $comunidadSeleccionada->nombre }}</strong>
                    @else
                        En todas las comunidades
                    @endif
                </span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card-clean p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Núcleos Familiares</span>
                    <i class="ti ti-home-heart text-teal fs-3"></i>
                </div>
                <div class="h2 mb-0 fw-bold text-teal">{{ $totalFamilias }}</div>
                <span class="text-muted small">Familias / Expedientes</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card-clean p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Mujeres (F)</span>
                    <i class="ti ti-gender-female text-pink fs-3"></i>
                </div>
                <div class="h2 mb-0 fw-bold text-pink">
                    {{ $mujeres }}
                    <span class="fs-6 fw-normal text-muted">
                        ({{ $totalPersonas > 0 ? round(($mujeres / $totalPersonas) * 100) : 0 }}%)
                    </span>
                </div>
                <div class="progress progress-xs mt-1">
                    <div class="progress-bar bg-pink" style="width: {{ $totalPersonas > 0 ? ($mujeres / $totalPersonas) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card-clean p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Hombres (M)</span>
                    <i class="ti ti-gender-male text-blue fs-3"></i>
                </div>
                <div class="h2 mb-0 fw-bold text-blue">
                    {{ $hombres }}
                    <span class="fs-6 fw-normal text-muted">
                        ({{ $totalPersonas > 0 ? round(($hombres / $totalPersonas) * 100) : 0 }}%)
                    </span>
                </div>
                <div class="progress progress-xs mt-1">
                    <div class="progress-bar bg-blue" style="width: {{ $totalPersonas > 0 ? ($hombres / $totalPersonas) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tabla de Personas / Pacientes ──────────────────────────── --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px; border: 1px solid rgba(11,28,48,0.08) !important;">
        <div class="card-header bg-white py-2 px-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-secondary">
                <i class="ti ti-list-details me-1 text-primary"></i>
                Listado Detallado de Personas
                @if($comunidadSeleccionada)
                    <span class="badge bg-primary ms-1">{{ $comunidadSeleccionada->nombre }}</span>
                @endif
            </h5>
            <span class="badge bg-secondary-lt">
                Página {{ $pacientes->currentPage() }} de {{ $pacientes->lastPage() ?: 1 }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter table-hover table-striped mb-0">
                <thead class="table-light text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th style="width: 100px;">No. Exp.</th>
                        <th>Nombres y Apellidos</th>
                        <th>DPI / CUI</th>
                        <th style="width: 80px;" class="text-center">Sexo</th>
                        <th>Fecha Nac. / Edad</th>
                        <th>Comunidad Asignada</th>
                        <th>Dirección Registrada</th>
                        <th style="width: 80px;" class="text-end no-print">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pacientes as $p)
                    <tr>
                        {{-- No. Expediente --}}
                        <td>
                            <span class="badge bg-blue-lt fw-bold font-monospace">
                                #{{ $p->numero_expediente_fisico ?? 'S/N' }}
                            </span>
                        </td>

                        {{-- Nombre --}}
                        <td>
                            <div class="fw-bold text-dark">{{ $p->nombres }} {{ $p->apellidos }}</div>
                            @if($p->familia && $p->familia->apellido_cabeza)
                                <span class="text-muted small" style="font-size: 0.75rem;">
                                    Familia: {{ $p->familia->apellido_cabeza }}
                                </span>
                            @endif
                        </td>

                        {{-- DPI --}}
                        <td>
                            @if($p->dpi)
                                <span class="font-monospace text-dark">{{ $p->dpi }}</span>
                            @else
                                <span class="text-muted small fst-italic">Sin DPI</span>
                            @endif
                        </td>

                        {{-- Sexo --}}
                        <td class="text-center">
                            @if($p->sexo === 'F')
                                <span class="badge bg-pink-lt" title="Femenino">
                                    <i class="ti ti-gender-female me-0.5"></i> F
                                </span>
                            @else
                                <span class="badge bg-blue-lt" title="Masculino">
                                    <i class="ti ti-gender-male me-0.5"></i> M
                                </span>
                            @endif
                        </td>

                        {{-- Fecha Nacimiento / Edad --}}
                        <td>
                            @if($p->fecha_nacimiento)
                                <div>{{ $p->fecha_nacimiento->format('d/m/Y') }}</div>
                                <span class="badge bg-light text-muted" style="font-size: 0.7rem;">
                                    {{ $p->fecha_nacimiento->age }} años
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>

                        {{-- Comunidad --}}
                        <td>
                            @if($p->familia && $p->familia->comunidad)
                                <a href="{{ route('reportes.por-comunidad', ['id_comunidad' => $p->familia->id_comunidad]) }}" class="badge bg-azure-lt text-decoration-none">
                                    <i class="ti ti-map-pin me-1"></i> {{ $p->familia->comunidad->nombre }}
                                </a>
                            @else
                                <span class="badge bg-secondary-lt">Sin asignar</span>
                            @endif
                        </td>

                        {{-- Dirección --}}
                        <td>
                            <span class="text-muted small">
                                {{ $p->direccion ?? ($p->familia->comunidad->nombre ?? '—') }}
                            </span>
                        </td>

                        {{-- Acción --}}
                        <td class="text-end no-print">
                            <a href="{{ route('pacientes.edit', $p->id_paciente) }}" class="btn btn-xs btn-outline-secondary" title="Editar paciente" target="_blank">
                                <i class="ti ti-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="ti ti-users-minus fs-1 d-block mb-1 text-secondary opacity-50"></i>
                            No se encontraron personas para la comunidad o filtros seleccionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pacientes->hasPages())
        <div class="card-footer bg-white d-flex align-items-center justify-content-between py-2 px-3 no-print">
            <span class="text-muted small">
                Mostrando del {{ $pacientes->firstItem() }} al {{ $pacientes->lastItem() }} de {{ $pacientes->total() }} registros
            </span>
            <div>
                {{ $pacientes->links() }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
