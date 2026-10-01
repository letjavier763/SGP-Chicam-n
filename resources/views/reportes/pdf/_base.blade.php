{{--
    _base.blade.php — CSS estándar compartido para todos los reportes PDF del SGP.
    Incluir con: @include('reportes.pdf._base')
    Variables opcionales:
      $orientacion  → 'portrait' | 'landscape'  (default: portrait)
--}}
<style>
    /* ── Fuente Inter (misma del proyecto) ── */
    @font-face {
        font-family: 'Inter';
        font-style: normal;
        font-weight: 400;
        src: url('{{ public_path("fonts/Inter-Regular.ttf") }}') format('truetype');
    }
    @font-face {
        font-family: 'Inter';
        font-style: normal;
        font-weight: 600;
        src: url('{{ public_path("fonts/Inter-SemiBold.ttf") }}') format('truetype');
    }
    @font-face {
        font-family: 'Inter';
        font-style: normal;
        font-weight: 700;
        src: url('{{ public_path("fonts/Inter-Bold.ttf") }}') format('truetype');
    }

    /* ── Página ── */
    @page {
        margin: 0;
        size: letter {{ $orientacion ?? 'portrait' }};
    }
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    body {
        font-family: 'Inter', sans-serif;
        font-size: 7.3pt;
        color: #1e293b;
        line-height: 1.4;
        background: #ffffff;
        padding: 20mm 15mm 20mm 15mm;
    }

    /* ── Encabezado institucional ── */
    .header {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        border-bottom: 1.5px solid #0284c7;
        padding-bottom: 6px;
    }
    .header td { vertical-align: top; }
    .inst-title {
        font-size: 11pt;
        font-weight: 700;
        color: #0f2744;
    }
    .inst-sub {
        font-size: 8pt;
        color: #475569;
        margin-top: 2px;
    }
    .header-meta {
        text-align: right;
        font-size: 6.8pt;
        color: #64748b;
        line-height: 1.5;
    }
    .header-meta strong { color: #1e293b; }

    /* ── Títulos de sección ── */
    .section-title {
        font-size: 7.5pt;
        font-weight: 700;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 4px;
    }
    .section-title span {
        font-weight: 400;
        color: #64748b;
        font-size: 6.8pt;
        margin-left: 4px;
    }

    /* ── Tabla de datos ── */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
    }
    .data-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 6.8pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 5px 7px;
        border: none;
        border-bottom: 1.5px solid #cbd5e1;
        text-align: left;
    }
    .data-table thead th.center { text-align: center; }
    .data-table tbody td {
        padding: 5px 7px;
        font-size: 7.2pt;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .data-table tbody tr:nth-child(even) td { background-color: #fafbfc; }
    .data-table tbody td.center { text-align: center; }
    .data-table tbody td.right  { text-align: right; }

    /* ── Tarjetas de resumen ── */
    .stats-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12px;
        background: #f8fafc;
        border-radius: 4px;
    }
    .stat-card {
        padding: 6px 12px;
        border: none;
        vertical-align: middle;
    }
    .stat-num {
        font-size: 12pt;
        font-weight: 700;
        display: inline-block;
        vertical-align: middle;
        margin-right: 4px;
        line-height: 1;
    }
    .stat-label {
        font-size: 6.8pt;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        display: inline-block;
        vertical-align: middle;
        letter-spacing: 0.3px;
    }
    .stat-card.primary .stat-num { color: #0284c7; }
    .stat-card.success .stat-num { color: #16a34a; }
    .stat-card.neutral .stat-num { color: #64748b; }
    .stat-card.warning .stat-num { color: #d97706; }

    /* ── Badges ── */
    .badge {
        display: inline-block;
        padding: 1px 5px;
        border-radius: 3px;
        font-size: 6.2pt;
        font-weight: 700;
    }
    .badge-nuevo      { background: #dcfce7; color: #15803d; }
    .badge-recurrente { background: #f1f5f9; color: #475569; }
    .badge-info       { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-warning    { background: #fffbeb; color: #92400e; }

    /* ── Tipografía auxiliar ── */
    .text-muted { color: #64748b; }
    .text-bold  { font-weight: 700; }
    .text-sm    { font-size: 6.2pt; color: #64748b; margin-top: 1px; }

    /* ── Pie de página fijo ── */
    .footer {
        position: fixed;
        bottom: -14mm;
        left: 0;
        right: 0;
        border-top: 1px solid #e2e8f0;
        padding-top: 4px;
        font-size: 6.2pt;
        color: #94a3b8;
        width: 100%;
    }
    .footer table { width: 100%; border-collapse: collapse; }
    .footer td    { padding: 0; }

    /* ── Separador ── */
    .divider {
        border: none;
        border-top: 1px solid #e2e8f0;
        margin: 8px 0;
    }
</style>
