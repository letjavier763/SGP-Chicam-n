@extends('layouts.app')

@section('title', 'Reportería Estadística')
@section('page_title', 'Reportería Estadística del CAP Chicamán')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endsection

@section('content')

{{-- ── Filtros de fecha ────────────────────────────────────── --}}
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body p-2 p-md-3">
        <form method="GET" action="{{ route('reportes.index') }}" id="form-filtro" class="row g-2 align-items-end">
            <div class="col-6 col-md-4">
                <label class="form-label text-secondary small mb-1">Fecha Desde</label>
                <input type="date" name="fecha_desde" class="form-control form-control-sm form-control-md-normal" value="{{ $fechaDesde }}">
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label text-secondary small mb-1">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control form-control-sm form-control-md-normal" value="{{ $fechaHasta }}">
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm btn-md-normal flex-fill">
                    <i class="ti ti-filter me-1"></i> Aplicar
                </button>
                <a href="{{ route('reportes.estadisticas.pdf', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                   class="btn btn-outline-danger btn-sm btn-md-normal" title="Exportar estadísticas generales a PDF">
                    <i class="ti ti-file-type-pdf me-1"></i> <span class="d-none d-sm-inline">PDF General</span><span class="d-sm-none">PDF</span>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ── Tarjetas de resumen ─────────────────────────────────── --}}
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body p-2 p-md-3">
        <div class="row g-0 text-center align-items-center">
            <div class="col-4 border-end pe-1">
                <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                    <span class="avatar avatar-xs bg-primary-lt rounded flex-shrink-0"><i class="ti ti-calendar-stats fs-4"></i></span>
                    <span class="fs-4 fs-md-2 fw-bold text-dark">{{ number_format($totalLlegadas) }}</span>
                </div>
                <div class="text-secondary text-truncate" style="font-size:.72rem">Total Llegadas</div>
            </div>
            <div class="col-4 border-end px-1">
                <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                    <span class="avatar avatar-xs bg-green-lt rounded flex-shrink-0"><i class="ti ti-user-plus fs-4"></i></span>
                    <span class="fs-4 fs-md-2 fw-bold text-dark">{{ number_format($totalNuevos) }}</span>
                </div>
                <div class="text-secondary text-truncate" style="font-size:.72rem">Pacientes Nuevos</div>
            </div>
            <div class="col-4 ps-1">
                <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                    <span class="avatar avatar-xs bg-blue-lt rounded flex-shrink-0"><i class="ti ti-refresh fs-4"></i></span>
                    <span class="fs-4 fs-md-2 fw-bold text-dark">{{ number_format($totalRecurrentes) }}</span>
                </div>
                <div class="text-secondary text-truncate" style="font-size:.72rem">Recurrentes</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Panel: 10 tipos de reportes exportables ─────────────── --}}
<div class="card mb-3 shadow-sm border-0 overflow-hidden">
    <div class="card-header py-2 px-3 bg-white border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="ti ti-file-export text-primary fs-4"></i>
            <div>
                <h4 class="card-title mb-0 fw-bold">Exportar Reportes en PDF</h4>
                <div class="text-secondary small">Selecciona el rango de fechas arriba y elige el tipo de reporte</div>
            </div>
        </div>
    </div>
    <div class="card-body p-0 p-md-3">

        @php
        $reportes = [
            [
                'num'   => 1,
                'icon'  => 'ti-calendar-check',
                'color' => 'primary',
                'titulo' => 'Llegadas por Rango',
                'desc'  => 'Listado completo de todas las llegadas registradas en el período seleccionado.',
                'ruta'  => 'reportes.exportar.llegadas-rango',
            ],
            [
                'num'   => 2,
                'icon'  => 'ti-user-plus',
                'color' => 'green',
                'titulo' => 'Pacientes Nuevos',
                'desc'  => 'Pacientes que se registraron por primera vez durante el período.',
                'ruta'  => 'reportes.exportar.pacientes-nuevos',
            ],
            [
                'num'   => 3,
                'icon'  => 'ti-refresh',
                'color' => 'blue',
                'titulo' => 'Pacientes Recurrentes',
                'desc'  => 'Pacientes que ya tenían registro previo y volvieron al CAP.',
                'ruta'  => 'reportes.exportar.pacientes-recurrentes',
            ],
            [
                'num'   => 4,
                'icon'  => 'ti-chart-donut',
                'color' => 'pink',
                'titulo' => 'Llegadas por Sexo',
                'desc'  => 'Distribución diaria de llegadas desglosada por sexo (M/F).',
                'ruta'  => 'reportes.exportar.por-sexo',
            ],
            [
                'num'   => 5,
                'icon'  => 'ti-clock-hour-4',
                'color' => 'yellow',
                'titulo' => 'Resumen por Turno',
                'desc'  => 'Llegadas totales, nuevos y recurrentes agrupados por cada turno de trabajo.',
                'ruta'  => 'reportes.exportar.por-turno',
            ],
            [
                'num'   => 6,
                'icon'  => 'ti-users',
                'color' => 'cyan',
                'titulo' => 'Padrón de Pacientes',
                'desc'  => 'Listado general de todos los pacientes registrados en el sistema.',
                'ruta'  => 'reportes.exportar.padron-pacientes',
            ],
            [
                'num'   => 7,
                'icon'  => 'ti-home-2',
                'color' => 'teal',
                'titulo' => 'Padrón de Familias',
                'desc'  => 'Todas las familias registradas con número de miembros por familia.',
                'ruta'  => 'reportes.exportar.padron-familias',
            ],
            [
                'num'   => 8,
                'icon'  => 'ti-alert-triangle',
                'color' => 'red',
                'titulo' => 'Alertas de Duplicidad',
                'desc'  => 'Historial de intentos de registro con datos duplicados (DPI, expediente, familia).',
                'ruta'  => 'reportes.exportar.alertas-duplicidad',
            ],
            [
                'num'   => 9,
                'icon'  => 'ti-id-badge',
                'color' => 'indigo',
                'titulo' => 'Actividad por Personal',
                'desc'  => 'Turnos trabajados y llegadas atendidas por cada recepcionista o trabajador.',
                'ruta'  => 'reportes.exportar.actividad-personal',
            ],
            [
                'num'   => 10,
                'icon'  => 'ti-calendar-month',
                'color' => 'orange',
                'titulo' => 'Resumen Mensual',
                'desc'  => 'Consolidado diario del mes: total llegadas, nuevos y recurrentes.',
                'ruta'  => 'reportes.exportar.resumen-mensual',
            ],
        ];
        @endphp

        {{-- Vista desktop (d-none d-md-block): Grid de tarjetas --}}
        <div class="d-none d-md-block">
            <div class="row g-2">
                @foreach($reportes as $r)
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card h-100 border shadow-none" style="border-color: #e8edf2 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="avatar avatar-md bg-{{ $r['color'] }}-lt rounded flex-shrink-0">
                                    <i class="ti {{ $r['icon'] }} fs-3 text-{{ $r['color'] }}"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center gap-1 mb-1">
                                        <span class="badge bg-light text-secondary border" style="font-size:.65rem">
                                            #{{ $r['num'] }}
                                        </span>
                                        <span class="fw-bold text-dark" style="font-size:.88rem">{{ $r['titulo'] }}</span>
                                    </div>
                                    <p class="text-secondary mb-2" style="font-size:.78rem; line-height:1.4">{{ $r['desc'] }}</p>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route($r['ruta'], ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta, 'preview' => 1]) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="ti ti-eye me-1"></i> Ver
                                        </a>
                                        <a href="{{ route($r['ruta'], ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                                           class="btn btn-sm btn-outline-danger flex-fill">
                                            <i class="ti ti-download me-1"></i> PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Vista móvil (d-md-none): Lista compacta de reportes --}}
        <div class="divide-y d-md-none">
            @foreach($reportes as $r)
            <div class="p-2.5 p-sm-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="avatar avatar-xs bg-{{ $r['color'] }}-lt rounded flex-shrink-0">
                        <i class="ti {{ $r['icon'] }} text-{{ $r['color'] }}"></i>
                    </span>
                    <span class="badge bg-light text-secondary border py-0 px-1" style="font-size:.65rem">#{{ $r['num'] }}</span>
                    <span class="fw-bold text-dark" style="font-size:.86rem">{{ $r['titulo'] }}</span>
                </div>
                <div class="text-secondary small mb-2 ps-4" style="font-size:.78rem; line-height:1.35">
                    {{ $r['desc'] }}
                </div>
                <div class="d-flex gap-2 ps-4">
                    <a href="{{ route($r['ruta'], ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta, 'preview' => 1]) }}"
                       target="_blank"
                       class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="ti ti-eye me-1"></i> Ver
                    </a>
                    <a href="{{ route($r['ruta'], ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                       class="btn btn-sm btn-outline-danger flex-fill">
                        <i class="ti ti-file-type-pdf me-1"></i> PDF
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Gráficas ─────────────────────────────────────────────── --}}
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header py-2 px-3 bg-white border-bottom">
                <h4 class="card-title mb-0"><i class="ti ti-chart-line me-2 text-primary"></i>Llegadas por Día</h4>
            </div>
            <div class="card-body p-2 p-md-3">
                <div style="position: relative; width: 100%; height: 220px;">
                    <canvas id="chartLlegadasDia"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header py-2 px-3 bg-white border-bottom">
                <h4 class="card-title mb-0"><i class="ti ti-chart-donut me-2 text-pink"></i>Por Sexo</h4>
            </div>
            <div class="card-body p-2 p-md-3 d-flex align-items-center justify-content-center">
                <div style="position: relative; width: 100%; height: 220px;">
                    <canvas id="chartSexo"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Tabla de turnos ──────────────────────────────────────── --}}
<div class="card shadow-sm border-0">
    <div class="card-header py-2 px-3 bg-white border-bottom">
        <h4 class="card-title mb-0"><i class="ti ti-list-details me-2 text-secondary"></i>Detalle por Turnos</h4>
    </div>
    <div class="table-responsive d-none d-md-block">
        <table class="table table-vcenter table-hover card-table mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Fecha</th>
                    <th>Turno</th>
                    <th>Personal</th>
                    <th class="text-center">Llegadas</th>
                    <th class="text-end">PDF</th>
                </tr>
            </thead>
            <tbody>
            @forelse($turnos as $turno)
                <tr>
                    <td>
                        <span class="fw-medium">{{ $turno->fecha->format('d/m/Y') }}</span>
                        @if($turno->fecha->isToday())
                            <span class="badge bg-green-lt text-green ms-1">Hoy</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $bc = match($turno->tipo_turno) {
                                'matutino'   => 'warning',
                                'vespertino' => 'primary',
                                'nocturno'   => 'dark',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $bc }}-lt text-{{ $bc }} text-capitalize">
                            {{ $turno->tipo_turno }}
                        </span>
                    </td>
                    <td class="small">{{ $turno->usuario->nombre_completo }}</td>
                    <td class="text-center fw-bold">{{ $turno->registros_llegada_count }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('reportes.diario', $turno->id_turno) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-eye me-1"></i>Ver
                            </a>
                            <a href="{{ route('reportes.pdf', $turno->id_turno) }}"
                               class="btn btn-sm btn-outline-danger">
                                <i class="ti ti-file-type-pdf me-1"></i>PDF
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-secondary py-5">
                        <i class="ti ti-chart-off fs-2 d-block mb-2"></i>
                        No se encontraron turnos en el rango seleccionado.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Vista móvil de turnos --}}
    <div class="divide-y d-md-none">
        @forelse($turnos as $turno)
        <div class="p-2 p-sm-3">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="d-flex align-items-center gap-1">
                    <span class="fw-semibold text-dark" style="font-size:.85rem">{{ $turno->fecha->format('d/m/Y') }}</span>
                    @if($turno->fecha->isToday())
                        <span class="badge bg-green-lt text-green">Hoy</span>
                    @endif
                </div>
                @php
                    $bc = match($turno->tipo_turno) { 'matutino' => 'warning', 'vespertino' => 'primary', 'nocturno' => 'dark', default => 'secondary' };
                @endphp
                <span class="badge bg-{{ $bc }}-lt text-{{ $bc }} text-capitalize">{{ $turno->tipo_turno }}</span>
            </div>
            <div class="text-secondary small mb-2 d-flex justify-content-between align-items-center">
                <span class="text-truncate me-2" style="font-size:.78rem">{{ $turno->usuario->nombre_completo }}</span>
                <span class="badge bg-blue-lt text-blue rounded-pill px-2 flex-shrink-0" style="font-size:.75rem">
                    <strong>{{ $turno->registros_llegada_count }}</strong> llegadas
                </span>
            </div>
            <div class="d-flex gap-1">
                <a href="{{ route('reportes.diario', $turno->id_turno) }}" class="btn btn-sm btn-outline-primary flex-fill py-1">
                    <i class="ti ti-eye me-1"></i>Ver
                </a>
                <a href="{{ route('reportes.pdf', $turno->id_turno) }}" class="btn btn-sm btn-outline-danger flex-fill py-1">
                    <i class="ti ti-file-type-pdf me-1"></i>PDF
                </a>
            </div>
        </div>
        @empty
        <div class="text-center text-secondary py-4">
            <i class="ti ti-chart-off fs-1 d-block mb-2 opacity-50"></i>
            No se encontraron turnos.
        </div>
        @endforelse
    </div>

    @if($turnos->hasPages())
    <div class="card-footer d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 bg-white py-2">
        <p class="m-0 text-secondary small text-center text-sm-start">{{ $turnos->firstItem() }}–{{ $turnos->lastItem() }} de {{ $turnos->total() }}</p>
        <ul class="pagination m-0">{{ $turnos->links('pagination::bootstrap-5') }}</ul>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
const llegadasDia = @json($llegadasPorDia);
const sexoData    = @json($porSexo);

// Chart 1: Llegadas por día
new Chart(document.getElementById('chartLlegadasDia').getContext('2d'), {
    type: 'line',
    data: {
        labels: llegadasDia.map(d => { const [y,m,day] = d.fecha.split('-'); return `${day}/${m}`; }),
        datasets: [{
            label: 'Llegadas',
            data: llegadasDia.map(d => d.total),
            borderColor: '#206bc4',
            backgroundColor: 'rgba(32,107,196,0.08)',
            tension: 0.35,
            fill: true,
            pointBackgroundColor: '#206bc4',
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(0,0,0,0.05)' } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { stepSize: 1 } }
        }
    }
});

// Chart 2: Dona por sexo
new Chart(document.getElementById('chartSexo').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: sexoData.map(d => d.sexo === 'F' ? 'Femenino' : 'Masculino'),
        datasets: [{
            data: sexoData.map(d => d.total),
            backgroundColor: sexoData.map(d => d.sexo === 'F' ? '#e83e8c' : '#206bc4'),
            borderWidth: 3,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } }
        }
    }
});
</script>
@endsection
