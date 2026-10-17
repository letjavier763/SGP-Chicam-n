@extends('layouts.app')

@section('title', 'Reportería Estadística')
@section('page_title', 'Reportería Estadística')
@section('page_subtitle', 'Centro de Atención Permanente · Chicamán')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<style>
/* ── Contenedor General Antidesbordamiento ─────────────────── */
.reportes-wrapper {
    max-width: 100%;
    overflow-x: hidden;
    padding-bottom: 85px; /* Evita que la barra de navegación móvil inferior tape los últimos reportes */
}

/* ── Control Segmentado de Fechas (Presets) ───────────────── */
.preset-segmented-control {
    display: flex;
    background: #f1f5f9;
    padding: 3px;
    border-radius: 9px;
    gap: 3px;
    border: 1px solid rgba(148, 163, 184, 0.2);
    width: 100%;
    box-sizing: border-box;
}
.preset-btn {
    flex: 1 1 0;
    min-width: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 2px;
    border: none;
    background: transparent;
    color: #475569;
    font-size: clamp(0.70rem, 2.5vw, 0.78rem);
    font-weight: 600;
    border-radius: 7px;
    cursor: pointer;
    transition: all 0.18s ease;
    text-decoration: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.preset-btn:hover {
    color: #0b1c30;
    background: rgba(255, 255, 255, 0.7);
}
.preset-btn.active {
    background: #0057cd !important;
    color: #ffffff !important;
    box-shadow: 0 2px 5px rgba(0, 87, 205, 0.25);
}

/* ── Pestañas Principales (Tabs) Adaptables a Cualquier Ancho ── */
.reportes-section-tabs {
    display: flex;
    background: #f1f5f9;
    padding: 3px;
    border-radius: 10px;
    gap: 3px;
    border: 1px solid rgba(148, 163, 184, 0.2);
    width: 100%;
    box-sizing: border-box;
}
.section-tab-btn {
    flex: 1 1 0;
    min-width: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 7px 2px;
    border: none;
    background: transparent;
    color: #475569;
    font-size: clamp(0.72rem, 2.6vw, 0.82rem);
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.18s ease;
    text-decoration: none;
    white-space: nowrap;
    overflow: hidden;
}
.section-tab-btn:hover {
    color: #0b1c30;
    background: rgba(255, 255, 255, 0.7);
}
.section-tab-btn.active {
    background: #0057cd !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 87, 205, 0.25);
}

/* ── Tarjetas de Métricas ─────────────────────────────────── */
.stat-metric-card {
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid rgba(11, 28, 48, 0.08) !important;
    box-shadow: 0 2px 6px rgba(11, 28, 48, 0.03);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.stat-metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(11, 28, 48, 0.07);
}

/* ── Tira Deslizable de Categorías ────────────────────────── */
.report-categories-scroll {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 6px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.report-categories-scroll::-webkit-scrollbar {
    display: none;
}
.report-cat-btn {
    flex: 0 0 auto;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 11px;
    border-radius: 20px;
    transition: all 0.15s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
}
.report-cat-btn:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #94a3b8;
}
.report-cat-btn.active {
    background: #0057cd !important;
    color: #ffffff !important;
    border-color: #0057cd !important;
    box-shadow: 0 2px 5px rgba(0, 87, 205, 0.22);
}

/* ── Fila de Reporte Móvil Compacta ───────────────────────── */
.mobile-report-card {
    border-radius: 10px;
    background: #ffffff;
    border: 1px solid rgba(11, 28, 48, 0.08);
    transition: background 0.15s ease, border-color 0.15s ease;
}
.mobile-report-card:active {
    background: #f8fafc;
}

/* Desktop Report Cards */
.report-card-item {
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid rgba(11, 28, 48, 0.08);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.report-card-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 16px rgba(11, 28, 48, 0.07);
}

/* Donut legend pill */
.donut-stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.74rem;
    font-weight: 600;
}
</style>
@endsection

@section('content')

<div class="reportes-wrapper">

@php
$hoyStr = now()->toDateString();
$inicioMesStr = now()->startOfMonth()->toDateString();
$finMesStr = now()->toDateString();
$inicioMesAntStr = now()->subMonth()->startOfMonth()->toDateString();
$finMesAntStr = now()->subMonth()->endOfMonth()->toDateString();

$preset = request('preset');
if (!$preset) {
    if ($fechaDesde === $hoyStr && $fechaHasta === $hoyStr) {
        $preset = 'hoy';
    } elseif ($fechaDesde === $inicioMesStr && ($fechaHasta === $finMesStr || $fechaHasta === now()->endOfMonth()->toDateString())) {
        $preset = 'mes';
    } elseif ($fechaDesde === $inicioMesAntStr && $fechaHasta === $finMesAntStr) {
        $preset = 'mes-ant';
    } else {
        $preset = 'custom';
    }
}
$esHoy = ($preset === 'hoy');
$esEsteMes = ($preset === 'mes');
$esMesAnt = ($preset === 'mes-ant');

$totalTurnosPeriodo = $turnos->total();
$pctNuevos = $totalLlegadas > 0 ? round(($totalNuevos / $totalLlegadas) * 100) : 0;
$pctRecurrentes = $totalLlegadas > 0 ? round(($totalRecurrentes / $totalLlegadas) * 100) : 0;

$reportes = [
    [
        'num'       => '01',
        'cat'       => 'operativo',
        'cat_label' => 'Operativo',
        'icon'      => 'ti-calendar-check',
        'color'     => '#0284c7',
        'bg_color'  => '#e0f2fe',
        'titulo'    => 'Llegadas por Rango',
        'desc'      => 'Listado cronológico completo de llegadas registradas.',
        'ruta'      => 'reportes.exportar.llegadas-rango',
    ],
    [
        'num'       => '02',
        'cat'       => 'pacientes',
        'cat_label' => 'Pacientes',
        'icon'      => 'ti-user-plus',
        'color'     => '#7c3aed',
        'bg_color'  => '#f3e8ff',
        'titulo'    => 'Pacientes Nuevos',
        'desc'      => 'Pacientes registrados por primera vez en el CAP.',
        'ruta'      => 'reportes.exportar.pacientes-nuevos',
    ],
    [
        'num'       => '03',
        'cat'       => 'pacientes',
        'cat_label' => 'Pacientes',
        'icon'      => 'ti-refresh',
        'color'     => '#059669',
        'bg_color'  => '#ecfdf5',
        'titulo'    => 'Pacientes Recurrentes',
        'desc'      => 'Pacientes con visitas previas de seguimiento o control.',
        'ruta'      => 'reportes.exportar.pacientes-recurrentes',
    ],
    [
        'num'       => '04',
        'cat'       => 'operativo',
        'cat_label' => 'Operativo',
        'icon'      => 'ti-chart-donut',
        'color'     => '#db2777',
        'bg_color'  => '#fce7f3',
        'titulo'    => 'Llegadas por Sexo',
        'desc'      => 'Distribución diaria de atenciones por sexo (M/F).',
        'ruta'      => 'reportes.exportar.por-sexo',
    ],
    [
        'num'       => '05',
        'cat'       => 'operativo',
        'cat_label' => 'Operativo',
        'icon'      => 'ti-clock-hour-4',
        'color'     => '#d97706',
        'bg_color'  => '#fef3c7',
        'titulo'    => 'Resumen por Turno',
        'desc'      => 'Llegadas totales, nuevos y recurrentes por jornada.',
        'ruta'      => 'reportes.exportar.por-turno',
    ],
    [
        'num'       => '06',
        'cat'       => 'pacientes',
        'cat_label' => 'Pacientes',
        'icon'      => 'ti-users',
        'color'     => '#0891b2',
        'bg_color'  => '#cffafe',
        'titulo'    => 'Padrón de Pacientes',
        'desc'      => 'Listado general estructurado de todos los pacientes.',
        'ruta'      => 'reportes.exportar.padron-pacientes',
    ],
    [
        'num'       => '07',
        'cat'       => 'pacientes',
        'cat_label' => 'Familias',
        'icon'      => 'ti-home-2',
        'color'     => '#0d9488',
        'bg_color'  => '#ccfbf1',
        'titulo'    => 'Padrón de Familias',
        'desc'      => 'Familias registradas con correlativo y miembros.',
        'ruta'      => 'reportes.exportar.padron-familias',
    ],
    [
        'num'       => '08',
        'cat'       => 'auditoria',
        'cat_label' => 'Auditoría',
        'icon'      => 'ti-alert-triangle',
        'color'     => '#dc2626',
        'bg_color'  => '#fee2e2',
        'titulo'    => 'Alertas de Duplicidad',
        'desc'      => 'Historial de registros con datos observados.',
        'ruta'      => 'reportes.exportar.alertas-duplicidad',
    ],
    [
        'num'       => '09',
        'cat'       => 'auditoria',
        'cat_label' => 'Personal',
        'icon'      => 'ti-id-badge',
        'color'     => '#4f46e5',
        'bg_color'  => '#e0e7ff',
        'titulo'    => 'Actividad por Personal',
        'desc'      => 'Jornadas laboradas y atenciones por recepcionista.',
        'ruta'      => 'reportes.exportar.actividad-personal',
    ],
    [
        'num'       => '10',
        'cat'       => 'operativo',
        'cat_label' => 'Operativo',
        'icon'      => 'ti-calendar-month',
        'color'     => '#ea580c',
        'bg_color'  => '#ffedd5',
        'titulo'    => 'Resumen Mensual',
        'desc'      => 'Consolidado mensual con desglose por días y turnos.',
        'ruta'      => 'reportes.exportar.resumen-mensual',
    ],
    [
        'num'       => '11',
        'cat'       => 'pacientes',
        'cat_label' => 'Pacientes',
        'icon'      => 'ti-map-pins',
        'color'     => '#0284c7',
        'bg_color'  => '#e0f2fe',
        'titulo'    => 'Personas por Comunidad',
        'desc'      => 'Censo interactivo y seleccionable por comunidad o aldea.',
        'ruta'      => 'reportes.exportar.por-comunidad',
        'ruta_ver'  => 'reportes.por-comunidad',
    ],
];
@endphp

{{-- ── 1. Barra de Filtro Minimalista y 100% Adaptable ────────── --}}
<div class="card mb-2.5 border-0" style="border-radius: 12px; background: #ffffff; border: 1px solid rgba(11,28,48,0.08) !important; box-shadow: 0 2px 8px rgba(11,28,48,0.03);">
    <div class="card-body p-2 p-sm-2.5">
        {{-- Fila 1: Presets rápidos --}}
        <div class="preset-segmented-control mb-2">
            <button type="button" class="preset-btn {{ $esHoy ? 'active' : '' }}" data-preset="hoy">
                Hoy
            </button>
            <button type="button" class="preset-btn {{ $esEsteMes ? 'active' : '' }}" data-preset="mes">
                Este Mes
            </button>
            <button type="button" class="preset-btn {{ $esMesAnt ? 'active' : '' }}" data-preset="mes-ant">
                Mes Anterior
            </button>
        </div>

        {{-- Formulario de Fechas: 2 filas limpias en móvil, 1 fila en escritorio --}}
        <form method="GET" action="{{ route('reportes.index') }}" id="form-filtro">
            <input type="hidden" name="preset" id="input_preset" value="{{ $preset }}">

            {{-- Fila 2: Inputs de fechas (50% cada uno en móvil) --}}
            <div class="row g-1.5 mb-2 align-items-center">
                <div class="col-6 col-md-4">
                    <input type="date" name="fecha_desde" id="input_fecha_desde" 
                           class="form-control form-control-sm text-center px-1" 
                           value="{{ $fechaDesde }}" 
                           title="Fecha Desde"
                           style="font-size: 0.78rem; height: 34px;" required>
                </div>
                <div class="col-6 col-md-4">
                    <input type="date" name="fecha_hasta" id="input_fecha_hasta" 
                           class="form-control form-control-sm text-center px-1" 
                           value="{{ $fechaHasta }}" 
                           title="Fecha Hasta"
                           style="font-size: 0.78rem; height: 34px;" required>
                </div>
                <div class="col-12 col-md-4 mt-2 mt-md-0 d-none d-md-block">
                    <div class="d-flex gap-1.5 justify-content-end">
                        <a href="{{ route('reportes.estadisticas.pdf', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                           class="btn btn-outline-danger btn-sm flex-fill d-inline-flex align-items-center justify-content-center py-1" 
                           style="height: 34px; border-radius: 6px; font-weight: 600; font-size: 0.78rem;"
                           title="Descargar PDF de estadísticas">
                            <i class="ti ti-file-type-pdf me-1"></i>PDF
                        </a>
                        <button type="button"
                                class="btn btn-outline-primary btn-sm px-2.5 py-0 d-inline-flex align-items-center justify-content-center btn-preview-pdf"
                                data-preview-url="{{ route('reportes.estadisticas.pdf', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta, 'preview' => 1]) }}"
                                data-download-url="{{ route('reportes.estadisticas.pdf', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                                data-title="Estadísticas Generales ({{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }})"
                                title="Previsualizar PDF en pantalla"
                                style="height: 34px; border-radius: 6px;">
                            <i class="ti ti-eye fs-3"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Fila 3 en Móvil: Acciones de PDF y Previa (cambio de fechas es automático) --}}
            <div class="d-flex gap-1.5 d-md-none">
                <a href="{{ route('reportes.estadisticas.pdf', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                   class="btn btn-outline-danger btn-sm flex-fill py-1 d-inline-flex align-items-center justify-content-center" 
                   style="height: 34px; border-radius: 6px; font-weight: 600; font-size: 0.78rem;"
                   title="Descargar PDF de estadísticas">
                    <i class="ti ti-file-type-pdf me-1"></i>PDF
                </a>
                <button type="button"
                        class="btn btn-outline-primary btn-sm px-2.5 py-0 d-inline-flex align-items-center justify-content-center btn-preview-pdf"
                        data-preview-url="{{ route('reportes.estadisticas.pdf', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta, 'preview' => 1]) }}"
                        data-download-url="{{ route('reportes.estadisticas.pdf', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                        data-title="Estadísticas Generales ({{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }})"
                        title="Previsualizar PDF en pantalla"
                        style="height: 34px; border-radius: 6px; min-width: 42px;">
                    <i class="ti ti-eye fs-3"></i>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── 2. Pestañas de Navegación (33.3% exacto, texto adaptable) ── --}}
<div class="reportes-section-tabs mb-3">
    <button type="button" class="section-tab-btn active" data-tab="tab-resumen">
        <i class="ti ti-chart-bar fs-4"></i>
        <span>Métricas</span>
    </button>
    <button type="button" class="section-tab-btn" data-tab="tab-reportes">
        <i class="ti ti-file-text fs-4"></i>
        <span>Reportes</span>
    </button>
    <button type="button" class="section-tab-btn" data-tab="tab-turnos">
        <i class="ti ti-calendar-time fs-4"></i>
        <span>Turnos</span>
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════════
     PESTAÑA 1: MÉTRICAS & GRÁFICAS (CENSO Y VENTANILLA)
     ══════════════════════════════════════════════════════════════ --}}
<div id="tab-resumen" class="reportes-tab-pane">

    {{-- Control de Sub-Vistas (Todo / Censo Poblacional / Flujo Ventanilla) --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-1 border-bottom">
        <div>
            <div class="fw-bold text-dark d-flex align-items-center gap-1.5" style="font-size: 0.9rem;">
                <i class="ti ti-chart-dots text-primary fs-3"></i>
                <span>Tablero Estadístico de Salud</span>
            </div>
            <span class="text-secondary small" style="font-size: 0.73rem;">
                Visualice tanto el padrón general censado como las atenciones registradas en ventanilla
            </span>
        </div>
        <div class="btn-group btn-group-sm p-0.5 rounded-pill bg-light border shadow-none" role="group" id="filtro-vista-metricas">
            <button type="button" class="btn btn-sm rounded-pill btn-primary px-3 py-1 fw-semibold active" data-vista="todas" style="font-size: 0.74rem;">
                <i class="ti ti-layout-grid me-1"></i> Todo
            </button>
            <button type="button" class="btn btn-sm rounded-pill btn-ghost-secondary px-3 py-1 fw-semibold text-secondary" data-vista="censo" style="font-size: 0.74rem;">
                <i class="ti ti-users-group me-1"></i> Censo y Padrón
            </button>
            <button type="button" class="btn btn-sm rounded-pill btn-ghost-secondary px-3 py-1 fw-semibold text-secondary" data-vista="ventanilla" style="font-size: 0.74rem;">
                <i class="ti ti-calendar-time me-1"></i> Flujo Ventanilla
            </button>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────
         SECCIÓN A: CENSO GENERAL & PADRÓN DE PACIENTES (FUERA DE VENTANILLA)
         ────────────────────────────────────────────────────────── --}}
    <div id="bloque-censo" class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2.5">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-xs rounded-2" style="background: #e0f2fe; color: #0284c7; width: 30px; height: 30px;">
                    <i class="ti ti-users-group fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-dark mb-0" style="font-size: 0.88rem; color: #0b1c30;">Censo Poblacional y Familias Registradas</h4>
                    <span class="text-secondary small" style="font-size: 0.72rem;">Datos base de personas y núcleos familiares (incluye expedientes físicos y archivo histórico)</span>
                </div>
            </div>
            <a href="{{ route('reportes.por-comunidad') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center py-1 px-2.5" style="border-radius: 8px; font-size: 0.75rem; font-weight: 600;">
                <i class="ti ti-map-pins me-1.5"></i> Explorar Padrón por Comunidad
            </a>
        </div>

        {{-- Métricas del Censo (4 Tarjetas) --}}
        <div class="row g-2 mb-3">
            {{-- Total Pacientes en Censo --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 p-2.5 stat-metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-secondary small fw-semibold" style="font-size: 0.72rem;">Población en Censo</span>
                        <div class="avatar avatar-xs rounded-circle" style="width: 28px; height: 28px; background: #e0f2fe; color: #0284c7;">
                            <i class="ti ti-users fs-3"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-2 text-dark lh-1 tabular my-1" style="color: #0b1c30 !important;">{{ number_format($totalCensoPacientes) }}</div>
                    <div class="text-secondary small text-truncate" style="font-size: 0.68rem;">Personas censadas en total</div>
                </div>
            </div>

            {{-- Núcleos Familiares --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 p-2.5 stat-metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-secondary small fw-semibold" style="font-size: 0.72rem;">Familias</span>
                        <div class="avatar avatar-xs rounded-circle" style="width: 28px; height: 28px; background: #ccfbf1; color: #0d9488;">
                            <i class="ti ti-home-heart fs-3"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-2 text-dark lh-1 tabular my-1" style="color: #0b1c30 !important;">{{ number_format($totalCensoFamilias) }}</div>
                    <div class="text-secondary small text-truncate" style="font-size: 0.68rem;">Núcleos con expediente</div>
                </div>
            </div>

            {{-- Comunidades Activas --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 p-2.5 stat-metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-secondary small fw-semibold" style="font-size: 0.72rem;">Comunidades</span>
                        <div class="avatar avatar-xs rounded-circle" style="width: 28px; height: 28px; background: #ffedd5; color: #ea580c;">
                            <i class="ti ti-map-pin fs-3"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-2 text-dark lh-1 tabular my-1" style="color: #0b1c30 !important;">{{ number_format($totalComunidadesActivas) }}</div>
                    <div class="text-secondary small text-truncate" style="font-size: 0.68rem;">Aldeas con familias</div>
                </div>
            </div>

            {{-- Solo en Archivo (Sin Ventanilla Aún) --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 p-2.5 stat-metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-secondary small fw-semibold" style="font-size: 0.72rem;">En Archivo Histórico</span>
                        <div class="avatar avatar-xs rounded-circle" style="width: 28px; height: 28px; background: #f3e8ff; color: #7c3aed;">
                            <i class="ti ti-archive fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-1 my-1">
                        <span class="fw-bold fs-2 text-dark lh-1 tabular" style="color: #0b1c30 !important;">{{ number_format($censoSinVentanilla) }}</span>
                        @if($totalCensoPacientes > 0)
                            <span class="badge rounded-pill fw-semibold px-1.5 py-0.5" style="font-size: 0.65rem; background: #f5f3ff; color: #7c3aed;">
                                {{ round(($censoSinVentanilla / $totalCensoPacientes) * 100) }}%
                            </span>
                        @endif
                    </div>
                    <div class="text-secondary small text-truncate" style="font-size: 0.68rem;">Pendiente de atención en ventanilla</div>
                </div>
            </div>
        </div>

        {{-- 3 Gráficas del Censo Poblacional --}}
        <div class="row g-2.5">
            {{-- Gráfica Horizontal: Top Comunidades --}}
            <div class="col-12 col-lg-6">
                <div class="card h-100 border-0" style="border-radius: 12px; border: 1px solid rgba(11,28,48,0.08) !important; box-shadow: 0 2px 8px rgba(11,28,48,0.04);">
                    <div class="card-header py-2 px-3 bg-white border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="ti ti-building-community text-primary fs-3"></i>
                                <h4 class="card-title mb-0 fw-bold" style="color: #0b1c30; font-size: 0.86rem;">Top Comunidades con Mayor Población</h4>
                            </div>
                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.68rem;">
                                Top {{ $censoComunidades->count() }} aldeas
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        @if($censoComunidades->count() > 0)
                            <div style="position: relative; width: 100%; height: 230px;">
                                <canvas id="chartCensoComunidades"></canvas>
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-secondary py-4" style="min-height: 200px;">
                                <i class="ti ti-map-off fs-1 mb-2 opacity-50"></i>
                                <span class="small fw-semibold">No hay comunidades asignadas a pacientes.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Gráfica de Barras: Pirámide de Grupos Etarios --}}
            <div class="col-12 col-md-7 col-lg-3">
                <div class="card h-100 border-0" style="border-radius: 12px; border: 1px solid rgba(11,28,48,0.08) !important; box-shadow: 0 2px 8px rgba(11,28,48,0.04);">
                    <div class="card-header py-2 px-3 bg-white border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="ti ti-chart-bar text-teal fs-3"></i>
                                <h4 class="card-title mb-0 fw-bold" style="color: #0b1c30; font-size: 0.86rem;">Grupos de Edad</h4>
                            </div>
                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.68rem;">
                                Padrón
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        <div style="position: relative; width: 100%; height: 230px;">
                            <canvas id="chartCensoEdades"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gráfica Dona: Sexo del Censo General --}}
            <div class="col-12 col-md-5 col-lg-3">
                <div class="card h-100 border-0" style="border-radius: 12px; border: 1px solid rgba(11,28,48,0.08) !important; box-shadow: 0 2px 8px rgba(11,28,48,0.04);">
                    <div class="card-header py-2 px-3 bg-white border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="ti ti-gender-intergender text-pink fs-3"></i>
                                <h4 class="card-title mb-0 fw-bold" style="color: #0b1c30; font-size: 0.86rem;">Sexo (Censo)</h4>
                            </div>
                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.68rem;">
                                {{ number_format($totalCensoPacientes) }} total
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-2 p-md-3 d-flex flex-column align-items-center justify-content-center">
                        @if($totalCensoPacientes > 0)
                            <div style="position: relative; width: 100%; height: 155px;">
                                <canvas id="chartCensoSexo"></canvas>
                            </div>
                            <div class="d-flex justify-content-center gap-1.5 mt-2 pt-2 border-top w-100 flex-wrap">
                                @php
                                    $pctM = $totalCensoPacientes > 0 ? round(($censoMujeres / $totalCensoPacientes) * 100) : 0;
                                    $pctH = $totalCensoPacientes > 0 ? round(($censoHombres / $totalCensoPacientes) * 100) : 0;
                                @endphp
                                <div class="donut-stat-pill" style="background: #fce7f3; color: #be185d;">
                                    <span class="badge rounded-circle p-1" style="background: #e11d48;"></span>
                                    <span>Mujeres: <strong>{{ $censoMujeres }}</strong> ({{ $pctM }}%)</span>
                                </div>
                                <div class="donut-stat-pill" style="background: #e0f2fe; color: #0369a1;">
                                    <span class="badge rounded-circle p-1" style="background: #0057cd;"></span>
                                    <span>Hombres: <strong>{{ $censoHombres }}</strong> ({{ $pctH }}%)</span>
                                </div>
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-secondary py-4" style="min-height: 180px;">
                                <i class="ti ti-chart-pie-off fs-1 mb-2 opacity-50"></i>
                                <span class="small fw-semibold">Sin datos demográficos en censo</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────
         SECCIÓN B: ATENCIONES Y FLUJO EN VENTANILLA (FILTRADO POR FECHAS)
         ────────────────────────────────────────────────────────── --}}
    <div id="bloque-ventanilla">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2.5">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-xs rounded-2" style="background: #e0f2fe; color: #0284c7; width: 30px; height: 30px;">
                    <i class="ti ti-calendar-time fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-dark mb-0" style="font-size: 0.88rem; color: #0b1c30;">Atenciones y Flujo en Ventanilla</h4>
                    <span class="text-secondary small" style="font-size: 0.72rem;">
                        Actividad operativa filtrada del <strong>{{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }}</strong> al <strong>{{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}</strong>
                    </span>
                </div>
            </div>
            <span class="badge bg-blue-lt fw-semibold" style="font-size: 0.72rem;">
                <i class="ti ti-filter me-1"></i> Período seleccionado
            </span>
        </div>

        {{-- Tarjetas de Métricas de Ventanilla --}}
        <div class="row g-2 mb-3">
            {{-- Total Llegadas --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 p-2.5 stat-metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-secondary small fw-semibold" style="font-size: 0.72rem;">Llegadas Ventanilla</span>
                        <div class="avatar avatar-xs rounded-circle" style="width: 28px; height: 28px; background: #e0f2fe; color: #0284c7;">
                            <i class="ti ti-calendar-stats fs-3"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-2 text-dark lh-1 tabular my-1" style="color: #0b1c30 !important;">{{ number_format($totalLlegadas) }}</div>
                    <div class="text-secondary small text-truncate" style="font-size: 0.68rem;">Atenciones en el rango</div>
                </div>
            </div>

            {{-- Pacientes Nuevos --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 p-2.5 stat-metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-secondary small fw-semibold" style="font-size: 0.72rem;">Nuevos</span>
                        <div class="avatar avatar-xs rounded-circle" style="width: 28px; height: 28px; background: #f3e8ff; color: #7c3aed;">
                            <i class="ti ti-user-plus fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-1 my-1">
                        <span class="fw-bold fs-2 text-dark lh-1 tabular" style="color: #0b1c30 !important;">{{ number_format($totalNuevos) }}</span>
                        @if($totalLlegadas > 0)
                            <span class="badge rounded-pill fw-semibold px-1.5 py-0.5" style="font-size: 0.65rem; background: #f5f3ff; color: #7c3aed;">{{ $pctNuevos }}%</span>
                        @endif
                    </div>
                    <div class="text-secondary small text-truncate" style="font-size: 0.68rem;">Primera vez en CAP</div>
                </div>
            </div>

            {{-- Recurrentes --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 p-2.5 stat-metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-secondary small fw-semibold" style="font-size: 0.72rem;">Recurrentes</span>
                        <div class="avatar avatar-xs rounded-circle" style="width: 28px; height: 28px; background: #ecfdf5; color: #059669;">
                            <i class="ti ti-refresh fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-1 my-1">
                        <span class="fw-bold fs-2 text-dark lh-1 tabular" style="color: #0b1c30 !important;">{{ number_format($totalRecurrentes) }}</span>
                        @if($totalLlegadas > 0)
                            <span class="badge rounded-pill fw-semibold px-1.5 py-0.5" style="font-size: 0.65rem; background: #ecfdf5; color: #059669;">{{ $pctRecurrentes }}%</span>
                        @endif
                    </div>
                    <div class="text-secondary small text-truncate" style="font-size: 0.68rem;">Seguimiento / Control</div>
                </div>
            </div>

            {{-- Turnos Cubiertos --}}
            <div class="col-6 col-md-3">
                <div class="card h-100 p-2.5 stat-metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-secondary small fw-semibold" style="font-size: 0.72rem;">Turnos</span>
                        <div class="avatar avatar-xs rounded-circle" style="width: 28px; height: 28px; background: #fff7ed; color: #ea580c;">
                            <i class="ti ti-clock-check fs-3"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-2 text-dark lh-1 tabular my-1" style="color: #0b1c30 !important;">{{ number_format($totalTurnosPeriodo) }}</div>
                    <div class="text-secondary small text-truncate" style="font-size: 0.68rem;">Jornadas en el rango</div>
                </div>
            </div>
        </div>

        {{-- Gráficas de Ventanilla --}}
        <div class="row g-2.5 mb-3">
            {{-- Gráfica de línea: Llegadas por Día --}}
            <div class="col-12 col-lg-8">
                <div class="card h-100 border-0" style="border-radius: 12px; border: 1px solid rgba(11,28,48,0.08) !important; box-shadow: 0 2px 8px rgba(11,28,48,0.04);">
                    <div class="card-header py-2 px-3 bg-white border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="ti ti-chart-line text-primary fs-3"></i>
                                <h4 class="card-title mb-0 fw-bold" style="color: #0b1c30; font-size: 0.88rem;">Llegadas por Día</h4>
                            </div>
                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.70rem;">
                                {{ $llegadasPorDia->count() }} día(s)
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        @if($llegadasPorDia->count() > 0)
                            <div style="position: relative; width: 100%; height: 210px;">
                                <canvas id="chartLlegadasDia"></canvas>
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-secondary py-4" style="min-height: 180px;">
                                <i class="ti ti-chart-dots fs-1 mb-2 opacity-50"></i>
                                <span class="small fw-semibold">No se registran llegadas en ventanilla en el período seleccionado.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Gráfica dona: Distribución por Sexo en Ventanilla --}}
            <div class="col-12 col-lg-4">
                <div class="card h-100 border-0" style="border-radius: 12px; border: 1px solid rgba(11,28,48,0.08) !important; box-shadow: 0 2px 8px rgba(11,28,48,0.04);">
                    <div class="card-header py-2 px-3 bg-white border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="ti ti-chart-donut text-pink fs-3"></i>
                                <h4 class="card-title mb-0 fw-bold" style="color: #0b1c30; font-size: 0.88rem;">Sexo en Ventanilla</h4>
                            </div>
                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.70rem;">
                                {{ number_format($totalLlegadas) }} atenciones
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-2 p-md-3 d-flex flex-column align-items-center justify-content-center">
                        @if($porSexo->count() > 0)
                            <div style="position: relative; width: 100%; height: 160px;">
                                <canvas id="chartSexo"></canvas>
                            </div>
                            <div class="d-flex justify-content-center gap-2 mt-2 pt-2 border-top w-100 flex-wrap">
                                @php
                                    $totalSexo = $porSexo->sum('total');
                                @endphp
                                @foreach($porSexo as $s)
                                    @php
                                        $esF = ($s->sexo === 'F');
                                        $pct = $totalSexo > 0 ? round(($s->total / $totalSexo) * 100) : 0;
                                    @endphp
                                    <div class="donut-stat-pill" style="background: {{ $esF ? '#fce7f3' : '#e0f2fe' }}; color: {{ $esF ? '#be185d' : '#0369a1' }};">
                                        <span class="badge rounded-circle p-1" style="background: {{ $esF ? '#e11d48' : '#0057cd' }};"></span>
                                        <span>{{ $esF ? 'Femenino' : 'Masculino' }}: <strong>{{ $s->total }}</strong> ({{ $pct }}%)</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-secondary py-4" style="min-height: 180px;">
                                <i class="ti ti-chart-pie-off fs-1 mb-2 opacity-50"></i>
                                <span class="small fw-semibold">Sin atenciones en ventanilla en este rango</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     PESTAÑA 2: CATÁLOGO DE REPORTES EN PDF (10 Reportes)
     ══════════════════════════════════════════════════════════════ --}}
<div id="tab-reportes" class="reportes-tab-pane d-none">
    {{-- Buscador y Filtros de Categoría Minimalistas --}}
    <div class="mb-3.5" style="margin-bottom: 20px !important;">
        {{-- Buscador con espacio garantizado para el ícono --}}
        <div class="position-relative mb-2.5">
            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-2.5 text-secondary" style="font-size: 0.95rem; pointer-events: none; z-index: 2;"></i>
            <input type="text" id="filtro-reportes-input" 
                   class="form-control form-control-sm rounded-pill" 
                   placeholder="Buscar reporte por nombre o tema..." 
                   style="padding-left: 36px !important; font-size: 0.80rem; height: 36px; border-color: #cbd5e1;">
        </div>

        {{-- Pestañas de categoría en fila deslizable táctil --}}
        <div class="report-categories-scroll">
            <button type="button" class="report-cat-btn active" data-cat="todos">
                Todos <span class="badge rounded-pill bg-light text-secondary ms-1">10</span>
            </button>
            <button type="button" class="report-cat-btn" data-cat="operativo">
                <i class="ti ti-activity me-1 text-primary"></i>Operativos <span class="badge rounded-pill bg-light text-secondary ms-1">4</span>
            </button>
            <button type="button" class="report-cat-btn" data-cat="pacientes">
                <i class="ti ti-users me-1 text-purple"></i>Pacientes & Familias <span class="badge rounded-pill bg-light text-secondary ms-1">4</span>
            </button>
            <button type="button" class="report-cat-btn" data-cat="auditoria">
                <i class="ti ti-shield-check me-1 text-danger"></i>Auditoría & Personal <span class="badge rounded-pill bg-light text-secondary ms-1">2</span>
            </button>
        </div>
    </div>

    {{-- Vista escritorio: Grid de 3 columnas (d-none d-md-block) --}}
    <div class="d-none d-md-block">
        <div class="row g-2.5">
            @foreach($reportes as $r)
            <div class="col-12 col-md-6 col-lg-4 item-reporte-card" 
                 data-cat="{{ $r['cat'] }}"
                 data-search="{{ strtolower($r['titulo'] . ' ' . $r['desc'] . ' ' . $r['num']) }}">
                <div class="card h-100 p-3 report-card-item">
                    <div class="d-flex align-items-start gap-2.5 mb-2">
                        <div class="avatar avatar-md rounded-3 flex-shrink-0" style="background: {{ $r['bg_color'] }}; color: {{ $r['color'] }};">
                            <i class="ti {{ $r['icon'] }} fs-3"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="mb-1">
                                <span class="badge border-0 fw-bold px-1.5 py-0.5" style="font-size: 0.65rem; background: #f1f5f9; color: #475569;">
                                    #{{ $r['num'] }}
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 0.88rem; color: #0b1c30;">
                                {{ $r['titulo'] }}
                            </h5>
                        </div>
                    </div>

                    <p class="text-secondary small mb-3 flex-grow-1" style="font-size: 0.76rem; line-height: 1.4;">
                        {{ $r['desc'] }}
                    </p>

                    <div class="d-flex gap-1.5 pt-1 border-top">
                        @if(isset($r['ruta_ver']))
                        <a href="{{ route($r['ruta_ver']) }}"
                           class="btn btn-sm btn-outline-info flex-fill d-inline-flex align-items-center justify-content-center py-1.5"
                           style="border-radius: 6px; font-weight: 600; font-size: 0.78rem;"
                           title="Abrir censo interactivo con filtros">
                            <i class="ti ti-table me-1"></i> Filtrar
                        </a>
                        @endif
                        <button type="button"
                                class="btn btn-sm btn-outline-primary flex-fill btn-preview-pdf d-inline-flex align-items-center justify-content-center py-1.5"
                                data-preview-url="{{ route($r['ruta'], ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta, 'preview' => 1]) }}"
                                data-download-url="{{ route($r['ruta'], ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                                data-title="{{ $r['titulo'] }}"
                                style="border-radius: 6px; font-weight: 600; font-size: 0.78rem;">
                            <i class="ti ti-eye me-1"></i> Previa
                        </button>
                        <a href="{{ route($r['ruta'], ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                           class="btn btn-sm btn-outline-danger flex-fill d-inline-flex align-items-center justify-content-center py-1.5"
                           style="border-radius: 6px; font-weight: 600; font-size: 0.78rem;"
                           title="Descargar archivo PDF">
                            <i class="ti ti-download me-1"></i> Descargar
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Vista móvil compacta (d-md-none): Tarjetas ergonómicas con botones 100% garantizados --}}
    <div class="d-md-none">
        <div class="d-flex flex-column gap-2">
            @foreach($reportes as $r)
            <div class="mobile-report-card p-2.5 item-reporte-card"
                 data-cat="{{ $r['cat'] }}"
                 data-search="{{ strtolower($r['titulo'] . ' ' . $r['desc'] . ' ' . $r['num']) }}"
                 style="border-radius: 12px; background: #ffffff; border: 1px solid rgba(11,28,48,0.08); box-shadow: 0 1px 4px rgba(11,28,48,0.03);">
                <div class="d-flex align-items-start gap-2 mb-2">
                    <div class="avatar avatar-xs rounded-2 flex-shrink-0 mt-0.5" style="background: {{ $r['bg_color'] }}; color: {{ $r['color'] }}; width: 32px; height: 32px;">
                        <i class="ti {{ $r['icon'] }} fs-4"></i>
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-bold text-dark mb-0.5" style="font-size: 0.85rem; color: #0b1c30; line-height: 1.25;">
                            {{ $r['titulo'] }}
                        </div>
                        <div class="text-secondary small" style="font-size: 0.73rem; line-height: 1.35; color: #64748b;">
                            {{ $r['desc'] }}
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-1.5 pt-1.5 border-top">
                    @if(isset($r['ruta_ver']))
                    <a href="{{ route($r['ruta_ver']) }}"
                       class="btn btn-sm btn-outline-info flex-fill d-inline-flex align-items-center justify-content-center py-1"
                       style="height: 32px; border-radius: 6px; font-weight: 600; font-size: 0.76rem;"
                       title="Abrir censo con filtros">
                        <i class="ti ti-table me-1"></i>Ver
                    </a>
                    @endif
                    <button type="button"
                            class="btn btn-sm btn-outline-primary flex-fill btn-preview-pdf d-inline-flex align-items-center justify-content-center py-1"
                            data-preview-url="{{ route($r['ruta'], ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta, 'preview' => 1]) }}"
                            data-download-url="{{ route($r['ruta'], ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                            data-title="{{ $r['titulo'] }}"
                            style="height: 32px; border-radius: 6px; font-weight: 600; font-size: 0.76rem;">
                        <i class="ti ti-eye me-1"></i>Previa
                    </button>
                    <a href="{{ route($r['ruta'], ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}"
                       class="btn btn-sm btn-outline-danger flex-fill d-inline-flex align-items-center justify-content-center py-1"
                       style="height: 32px; border-radius: 6px; font-weight: 600; font-size: 0.76rem;"
                       title="Descargar PDF">
                        <i class="ti ti-download me-1"></i>PDF
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Mensaje de búsqueda vacía --}}
    <div id="reportes-sin-resultados" class="text-center py-4 text-secondary d-none">
        <i class="ti ti-file-search fs-1 d-block mb-1 text-muted"></i>
        <span class="small">No se encontraron reportes que coincidan con la búsqueda.</span>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     PESTAÑA 3: DETALLE DE TURNOS REALIZADOS
     ══════════════════════════════════════════════════════════════ --}}
<div id="tab-turnos" class="reportes-tab-pane d-none">
    <div class="card border-0 overflow-hidden" style="border-radius: 12px; border: 1px solid rgba(11,28,48,0.08) !important; box-shadow: 0 2px 8px rgba(11,28,48,0.04);">
        <div class="card-header py-2 px-3 bg-white border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-1.5">
                    <i class="ti ti-list-details text-primary fs-3"></i>
                    <div>
                        <h4 class="card-title mb-0 fw-bold" style="color: #0b1c30; font-size: 0.88rem;">Detalle de Turnos</h4>
                    </div>
                </div>
                <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.70rem;">
                    {{ $turnos->total() }} turno(s)
                </span>
            </div>
        </div>

        {{-- Vista desktop: Tabla limpia --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-vcenter table-hover card-table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="font-size: 0.75rem; color: #475569;">Fecha</th>
                        <th style="font-size: 0.75rem; color: #475569;">Turno</th>
                        <th style="font-size: 0.75rem; color: #475569;">Responsable</th>
                        <th class="text-center" style="font-size: 0.75rem; color: #475569;">Atenciones</th>
                        <th class="text-end" style="font-size: 0.75rem; color: #475569;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($turnos as $turno)
                    <tr>
                        <td>
                            <span class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $turno->fecha->format('d/m/Y') }}</span>
                            @if($turno->fecha->isToday())
                                <span class="badge bg-success-subtle text-success border-0 ms-1 fw-bold" style="font-size: 0.65rem;">Hoy</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $bc = match($turno->tipo_turno) {
                                    'matutino'   => ['bg' => '#fef3c7', 'text' => '#b45309', 'icon' => 'ti-sun'],
                                    'vespertino' => ['bg' => '#e0f2fe', 'text' => '#0369a1', 'icon' => 'ti-sunset'],
                                    'nocturno'   => ['bg' => '#e2e8f0', 'text' => '#334155', 'icon' => 'ti-moon'],
                                    default      => ['bg' => '#f1f5f9', 'text' => '#475569', 'icon' => 'ti-clock']
                                };
                            @endphp
                            <span class="badge border-0 text-capitalize px-2 py-1 fw-semibold d-inline-flex align-items-center gap-1" 
                                  style="background: {{ $bc['bg'] }}; color: {{ $bc['text'] }}; font-size: 0.75rem;">
                                <i class="ti {{ $bc['icon'] }}"></i>
                                {{ $turno->tipo_turno }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1.5">
                                <span class="avatar avatar-xs rounded-circle bg-light text-secondary border flex-shrink-0" style="font-size: 0.68rem; width: 26px; height: 26px;">
                                    <i class="ti ti-user"></i>
                                </span>
                                <span class="small fw-semibold text-dark">{{ $turno->nombre_responsable }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-blue-subtle text-primary border-0 rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.78rem;">
                                {{ $turno->registros_llegada_count }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('reportes.diario', $turno->id_turno) }}"
                                   class="btn btn-sm btn-outline-primary" 
                                   style="font-size: 0.76rem; border-radius: 6px;"
                                   title="Ver resumen y detalle en pantalla">
                                    <i class="ti ti-eye me-1"></i>Ver
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-info btn-preview-pdf"
                                        data-preview-url="{{ route('reportes.pdf', ['turnoId' => $turno->id_turno, 'preview' => 1]) }}"
                                        data-download-url="{{ route('reportes.pdf', $turno->id_turno) }}"
                                        data-title="Reporte Diario — Turno #{{ $turno->id_turno }} ({{ $turno->fecha->format('d/m/Y') }})"
                                        style="font-size: 0.76rem; border-radius: 6px;"
                                        title="Previsualizar PDF del turno en pantalla">
                                    <i class="ti ti-file-search me-1"></i>Previa
                                </button>
                                <a href="{{ route('reportes.pdf', $turno->id_turno) }}"
                                   class="btn btn-sm btn-outline-danger" 
                                   style="font-size: 0.76rem; border-radius: 6px;"
                                   title="Descargar PDF oficial del turno">
                                    <i class="ti ti-download me-1"></i>PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">
                            <i class="ti ti-calendar-off fs-1 d-block mb-1 text-muted"></i>
                            <span class="small">No se encontraron turnos registrados en las fechas seleccionadas.</span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Vista móvil: Filas táctiles compactas --}}
        <div class="divide-y d-md-none">
            @forelse($turnos as $turno)
            <div class="p-2 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-1.5">
                        <span class="fw-bold text-dark" style="font-size: 0.84rem;">{{ $turno->fecha->format('d/m/Y') }}</span>
                        @if($turno->fecha->isToday())
                            <span class="badge bg-success-subtle text-success border-0 py-0 px-1 fw-bold" style="font-size: 0.62rem;">Hoy</span>
                        @endif
                    </div>
                    @php
                        $bc = match($turno->tipo_turno) {
                            'matutino'   => ['bg' => '#fef3c7', 'text' => '#b45309', 'icon' => 'ti-sun'],
                            'vespertino' => ['bg' => '#e0f2fe', 'text' => '#0369a1', 'icon' => 'ti-sunset'],
                            'nocturno'   => ['bg' => '#e2e8f0', 'text' => '#334155', 'icon' => 'ti-moon'],
                            default      => ['bg' => '#f1f5f9', 'text' => '#475569', 'icon' => 'ti-clock']
                        };
                    @endphp
                    <span class="badge border-0 text-capitalize px-1.5 py-0.5 fw-semibold d-inline-flex align-items-center gap-1" 
                          style="background: {{ $bc['bg'] }}; color: {{ $bc['text'] }}; font-size: 0.68rem;">
                        <i class="ti {{ $bc['icon'] }}"></i>
                        {{ $turno->tipo_turno }}
                    </span>
                </div>

                <div class="text-secondary small mb-1.5 d-flex justify-content-between align-items-center">
                    <span class="text-truncate me-2 fw-medium" style="font-size: 0.74rem;">
                        <i class="ti ti-user me-0.5 text-muted"></i>{{ $turno->nombre_responsable }}
                    </span>
                    <span class="badge bg-blue-subtle text-primary rounded-pill px-2 flex-shrink-0 fw-bold" style="font-size: 0.72rem;">
                        {{ $turno->registros_llegada_count }} llegadas
                    </span>
                </div>

                <div class="d-flex gap-1">
                    <a href="{{ route('reportes.diario', $turno->id_turno) }}" class="btn btn-sm btn-outline-primary flex-fill py-1" style="font-size: 0.74rem; border-radius: 6px;">
                        <i class="ti ti-eye me-1"></i>Ver
                    </a>
                    <button type="button" 
                            class="btn btn-sm btn-outline-info flex-fill py-1 btn-preview-pdf"
                            data-preview-url="{{ route('reportes.pdf', ['turnoId' => $turno->id_turno, 'preview' => 1]) }}"
                            data-download-url="{{ route('reportes.pdf', $turno->id_turno) }}"
                            data-title="Reporte Diario — Turno #{{ $turno->id_turno }} ({{ $turno->fecha->format('d/m/Y') }})"
                            style="font-size: 0.74rem; border-radius: 6px;">
                        <i class="ti ti-file-search me-1"></i>Previa
                    </button>
                    <a href="{{ route('reportes.pdf', $turno->id_turno) }}" class="btn btn-sm btn-outline-danger flex-fill py-1" style="font-size: 0.74rem; border-radius: 6px;">
                        <i class="ti ti-download me-1"></i>PDF
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center text-secondary py-3 bg-white">
                <i class="ti ti-calendar-off fs-2 d-block mb-1 text-muted opacity-50"></i>
                <span class="small" style="font-size: 0.76rem;">No se encontraron turnos.</span>
            </div>
            @endforelse
        </div>

        @if($turnos->hasPages())
        <div class="card-footer d-flex flex-column flex-sm-row align-items-center justify-content-between gap-1 bg-white py-1.5 px-2.5">
            <p class="m-0 text-secondary small text-center text-sm-start" style="font-size: 0.72rem;">{{ $turnos->firstItem() }}–{{ $turnos->lastItem() }} de {{ $turnos->total() }} turnos</p>
            <ul class="pagination m-0">{{ $turnos->links('pagination::bootstrap-5') }}</ul>
        </div>
        @endif
    </div>
</div>

</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── 1. Manejo de Pestañas Principales (Tabs) ────────────────
    const tabButtons = document.querySelectorAll('.section-tab-btn');
    const tabPanes = document.querySelectorAll('.reportes-tab-pane');

    function switchSectionTab(tabId) {
        tabButtons.forEach(btn => {
            if (btn.getAttribute('data-tab') === tabId) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        tabPanes.forEach(pane => {
            if (pane.id === tabId) {
                pane.classList.remove('d-none');
            } else {
                pane.classList.add('d-none');
            }
        });

        try {
            sessionStorage.setItem('sgp_active_report_tab', tabId);
            history.replaceState(null, null, '#' + tabId);
        } catch(e) {}

        if (tabId === 'tab-resumen') {
            window.dispatchEvent(new Event('resize'));
        }
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            switchSectionTab(this.getAttribute('data-tab'));
        });
    });

    const currentHash = window.location.hash.replace('#', '');
    const savedTab = sessionStorage.getItem('sgp_active_report_tab');
    const targetTab = currentHash || savedTab || 'tab-resumen';
    if (document.getElementById(targetTab)) {
        switchSectionTab(targetTab);
    } else {
        switchSectionTab('tab-resumen');
    }

    // ── 2. Chart 1: Llegadas por Día ────────────────────────────
    const canvasLlegadas = document.getElementById('chartLlegadasDia');
    if (canvasLlegadas) {
        const llegadasDia = @json($llegadasPorDia);
        if (llegadasDia && llegadasDia.length > 0) {
            const ctx = canvasLlegadas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 210);
            gradient.addColorStop(0, 'rgba(0, 87, 205, 0.22)');
            gradient.addColorStop(1, 'rgba(0, 87, 205, 0.00)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: llegadasDia.map(d => {
                        const parts = d.fecha.split('-');
                        return parts.length === 3 ? `${parts[2]}/${parts[1]}` : d.fecha;
                    }),
                    datasets: [{
                        label: 'Llegadas registradas',
                        data: llegadasDia.map(d => d.total),
                        borderColor: '#0057cd',
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#0057cd',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3.5,
                        pointHoverRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0b1c30',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 8,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `${context.parsed.y} paciente(s)`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: { font: { size: 10 }, color: '#64748b' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { stepSize: 1, font: { size: 10 }, color: '#64748b' }
                        }
                    }
                }
            });
        }
    }

    // ── 3. Chart 2: Dona por Sexo ────────────────────────────────
    const canvasSexo = document.getElementById('chartSexo');
    if (canvasSexo) {
        const sexoData = @json($porSexo);
        if (sexoData && sexoData.length > 0) {
            new Chart(canvasSexo.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: sexoData.map(d => d.sexo === 'F' ? 'Femenino' : 'Masculino'),
                    datasets: [{
                        data: sexoData.map(d => d.total),
                        backgroundColor: sexoData.map(d => d.sexo === 'F' ? '#e11d48' : '#0057cd'),
                        hoverBackgroundColor: sexoData.map(d => d.sexo === 'F' ? '#be185d' : '#00419e'),
                        borderWidth: 2.5,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0b1c30',
                            padding: 8,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.label}: ${context.raw} pacientes`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // ── 3.1 Chart 3: Top Comunidades del Censo (Horizontal Bar) ──
    const canvasCensoCom = document.getElementById('chartCensoComunidades');
    if (canvasCensoCom) {
        const comData = @json($censoComunidades);
        if (comData && comData.length > 0) {
            new Chart(canvasCensoCom.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: comData.map(c => c.nombre),
                    datasets: [{
                        label: 'Personas censadas',
                        data: comData.map(c => c.total),
                        backgroundColor: '#0057cd',
                        hoverBackgroundColor: '#00419e',
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.75,
                        categoryPercentage: 0.85
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0b1c30',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 8,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `${context.parsed.x} persona(s) registrada(s)`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { stepSize: 1, font: { size: 10 }, color: '#64748b' }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '500' }, color: '#1e293b' }
                        }
                    }
                }
            });
        }
    }

    // ── 3.2 Chart 4: Grupos de Edad (Censo General) ─────────────
    const canvasCensoEdades = document.getElementById('chartCensoEdades');
    if (canvasCensoEdades) {
        const edadesData = @json($gruposEdad);
        const labels = Object.keys(edadesData);
        const values = Object.values(edadesData);
        
        new Chart(canvasCensoEdades.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Población',
                    data: values,
                    backgroundColor: '#0d9488',
                    hoverBackgroundColor: '#0f766e',
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0b1c30',
                        padding: 8,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `${context.parsed.y} paciente(s)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9 }, color: '#64748b', maxRotation: 45, minRotation: 45 }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { stepSize: 1, font: { size: 10 }, color: '#64748b' }
                    }
                }
            }
        });
    }

    // ── 3.3 Chart 5: Demografía por Sexo (Censo General) ────────
    const canvasCensoSexo = document.getElementById('chartCensoSexo');
    if (canvasCensoSexo) {
        const censoM = {{ (int) $censoHombres }};
        const censoF = {{ (int) $censoMujeres }};
        const totalCensoS = censoM + censoF;

        if (totalCensoS > 0) {
            new Chart(canvasCensoSexo.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Femenino', 'Masculino'],
                    datasets: [{
                        data: [censoF, censoM],
                        backgroundColor: ['#e11d48', '#0057cd'],
                        hoverBackgroundColor: ['#be185d', '#00419e'],
                        borderWidth: 2.5,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0b1c30',
                            padding: 8,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const pct = totalCensoS > 0 ? Math.round((context.raw / totalCensoS) * 100) : 0;
                                    return ` ${context.label}: ${context.raw} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // ── 3.4 Filtro de Sub-vistas en Estadísticas (Todo / Censo / Ventanilla) ──
    const subViewButtons = document.querySelectorAll('#filtro-vista-metricas button');
    const bloqueCenso = document.getElementById('bloque-censo');
    const bloqueVentanilla = document.getElementById('bloque-ventanilla');

    if (subViewButtons && bloqueCenso && bloqueVentanilla) {
        subViewButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                subViewButtons.forEach(b => {
                    b.classList.remove('active', 'btn-primary');
                    b.classList.add('btn-ghost-secondary', 'text-secondary');
                });
                this.classList.add('active', 'btn-primary');
                this.classList.remove('btn-ghost-secondary', 'text-secondary');
                const vista = this.getAttribute('data-vista');

                if (vista === 'todas') {
                    bloqueCenso.classList.remove('d-none');
                    bloqueVentanilla.classList.remove('d-none');
                } else if (vista === 'censo') {
                    bloqueCenso.classList.remove('d-none');
                    bloqueVentanilla.classList.add('d-none');
                } else if (vista === 'ventanilla') {
                    bloqueCenso.classList.add('d-none');
                    bloqueVentanilla.classList.remove('d-none');
                }
            });
        });
    }

    // ── 4. Selector rápido de fechas (Presets) ───────────────────
    const inputPreset = document.getElementById('input_preset');
    const inputDesde  = document.getElementById('input_fecha_desde');
    const inputHasta  = document.getElementById('input_fecha_hasta');
    const formFiltro  = document.getElementById('form-filtro');

    document.querySelectorAll('.preset-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const p = this.getAttribute('data-preset');
            const now = new Date();

            function formatYMD(d) {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            }

            let desde = '';
            let hasta = '';

            if (p === 'hoy') {
                desde = formatYMD(now);
                hasta = formatYMD(now);
            } else if (p === 'mes') {
                const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                desde = formatYMD(firstDay);
                hasta = formatYMD(now);
            } else if (p === 'mes-ant') {
                const firstDayLastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                const lastDayLastMonth = new Date(now.getFullYear(), now.getMonth(), 0);
                desde = formatYMD(firstDayLastMonth);
                hasta = formatYMD(lastDayLastMonth);
            }

            if (desde && hasta) {
                inputDesde.value = desde;
                inputHasta.value = hasta;
                if (inputPreset) inputPreset.value = p;
                const activeTab = sessionStorage.getItem('sgp_active_report_tab') || 'tab-resumen';
                if (formFiltro) {
                    formFiltro.action = '{{ route("reportes.index") }}#' + activeTab;
                    formFiltro.submit();
                }
            }
        });
    });

    function submitOnDateChange() {
        if (inputDesde && inputHasta && inputDesde.value && inputHasta.value) {
            if (inputDesde.value > inputHasta.value) {
                inputHasta.value = inputDesde.value;
            }
            if (inputPreset) inputPreset.value = 'custom';
            const activeTab = sessionStorage.getItem('sgp_active_report_tab') || 'tab-resumen';
            if (formFiltro) {
                formFiltro.action = '{{ route("reportes.index") }}#' + activeTab;
                formFiltro.submit();
            }
        }
    }

    if (inputDesde && inputHasta) {
        inputDesde.addEventListener('change', submitOnDateChange);
        inputHasta.addEventListener('change', submitOnDateChange);
    }

    // ── 5. Filtro de Categorías y Buscador de Reportes ───────────
    const catButtons = document.querySelectorAll('.report-cat-btn');
    const searchInput = document.getElementById('filtro-reportes-input');
    const reportItems = document.querySelectorAll('.item-reporte-card');
    const sinResultados = document.getElementById('reportes-sin-resultados');

    let categoriaActual = 'todos';

    function filtrarReportes() {
        const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
        let visibles = 0;

        reportItems.forEach(function(item) {
            const cat = item.getAttribute('data-cat');
            const searchData = item.getAttribute('data-search') || '';

            const coincideCat = (categoriaActual === 'todos' || cat === categoriaActual);
            const coincideTexto = !query || searchData.includes(query);

            if (coincideCat && coincideTexto) {
                item.classList.remove('d-none');
                visibles++;
            } else {
                item.classList.add('d-none');
            }
        });

        if (sinResultados) {
            if (visibles === 0) {
                sinResultados.classList.remove('d-none');
            } else {
                sinResultados.classList.add('d-none');
            }
        }
    }

    catButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            catButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            categoriaActual = this.getAttribute('data-cat');
            filtrarReportes();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', filtrarReportes);
    }
});
</script>

{{-- Modal para previsualización en pantalla del PDF --}}
@include('reportes.preview-modal')

@endsection
