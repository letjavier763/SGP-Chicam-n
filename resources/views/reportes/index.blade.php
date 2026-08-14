@extends('layouts.app')

@section('title', 'Reportería Estadística')
@section('page_title', 'Reportería Estadística del CAP Chicamán')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endsection

@section('content')

{{-- Filtros de fecha --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reportes.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-secondary small">Fecha Desde</label>
                <input type="date" name="fecha_desde" class="form-control" value="{{ $fechaDesde }}">
            </div>
            <div class="col-md-4">
                <label class="form-label text-secondary small">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control" value="{{ $fechaHasta }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-filter me-1"></i> Aplicar Filtro
                </button>
                <a href="{{ route('reportes.estadisticas.pdf', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                   class="btn btn-outline-danger" title="Exportar a PDF">
                    <i class="ti ti-file-type-pdf"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tarjetas de resumen --}}
<div class="row row-cards mb-4">
    <div class="col-sm-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-primary-lt rounded"><i class="ti ti-calendar-stats fs-2"></i></span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium fs-2 text-dark">{{ number_format($totalLlegadas) }}</div>
                        <div class="text-secondary">Total Llegadas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-green-lt rounded"><i class="ti ti-user-plus fs-2"></i></span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium fs-2 text-dark">{{ number_format($totalNuevos) }}</div>
                        <div class="text-secondary">Pacientes Nuevos</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-blue-lt rounded"><i class="ti ti-refresh fs-2"></i></span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium fs-2 text-dark">{{ number_format($totalRecurrentes) }}</div>
                        <div class="text-secondary">Pacientes Recurrentes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Gráficas --}}
<div class="row g-3 mb-4">
    {{-- Llegadas por día --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-chart-line me-2 text-primary"></i> Llegadas por Día</h3>
            </div>
            <div class="card-body">
                <canvas id="chartLlegadasDia" height="280"></canvas>
            </div>
        </div>
    </div>

    {{-- Distribución por sexo --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-chart-donut me-2 text-pink"></i> Distribución por Sexo</h3>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartSexo" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Tabla de turnos --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-list-details me-2 text-secondary"></i> Detalle por Turnos
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Turno</th>
                    <th>Personal</th>
                    <th class="text-center">Llegadas</th>
                    <th class="text-end">Acciones</th>
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
                    <td>{{ $turno->usuario->nombre_completo }}</td>
                    <td class="text-center">
                        <span class="fw-bold">{{ $turno->registros_llegada_count }}</span>
                    </td>
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
    @if($turnos->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">{{ $turnos->firstItem() }}–{{ $turnos->lastItem() }} de {{ $turnos->total() }}</p>
        <ul class="pagination m-0 ms-auto">{{ $turnos->links('pagination::bootstrap-5') }}</ul>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
// Datos para las gráficas desde PHP
const llegadasDia = @json($llegadasPorDia);
const sexoData    = @json($porSexo);

// Chart 1: Línea de llegadas por día
const ctxLine = document.getElementById('chartLlegadasDia').getContext('2d');
new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: llegadasDia.map(d => {
            const [year, month, day] = d.fecha.split('-');
            return `${day}/${month}`;
        }),
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
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(0,0,0,0.05)' } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { stepSize: 1 } }
        }
    }
});

// Chart 2: Dona por sexo
const ctxDonut = document.getElementById('chartSexo').getContext('2d');
const sexoLabels = sexoData.map(d => d.sexo === 'F' ? 'Femenino' : 'Masculino');
const sexoColors = sexoData.map(d => d.sexo === 'F' ? '#e83e8c' : '#206bc4');
new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
        labels: sexoLabels,
        datasets: [{
            data: sexoData.map(d => d.total),
            backgroundColor: sexoColors,
            borderWidth: 3,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16, font: { size: 13 } } }
        }
    }
});
</script>
@endsection
