<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>{{ $titulo }} — CAP Chicamán</title>
@include('reportes.pdf._base')
<style>
    /* Estilos específicos del reporte genérico */
    .total-row td {
        background: #f1f5f9 !important;
        font-weight: 700;
        color: #0f2744;
        border-top: 1.5px solid #cbd5e1 !important;
        border-bottom: 1px solid #cbd5e1 !important;
        padding: 6px 7px;
    }
    .empty {
        text-align: center;
        padding: 18px;
        color: #94a3b8;
        font-style: italic;
    }
</style>
</head>
<body>

{{-- Encabezado --}}
<table class="header">
    <tr>
        <td style="width: 60%;">
            <div class="inst-title">Centro de Atención Permanente — CAP Chicamán</div>
            <div class="inst-sub">{{ $titulo }}</div>
        </td>
        <td style="width: 40%;" class="header-meta">
            <div><strong>Informe:</strong> {{ $titulo }}</div>
            <div><strong>Criterio:</strong> {{ $subtitulo }}</div>
            <div><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</div>
        </td>
    </tr>
</table>

{{-- Tabla de Datos --}}
<table class="data-table">
    <thead>
        <tr>
            @foreach($columnas as $col)
                <th>{{ $col }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($filas as $fila)
            <tr>
                @foreach($fila as $celda)
                    <td>{{ $celda }}</td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($columnas) }}" class="empty">
                    No se encontraron registros en el período seleccionado.
                </td>
            </tr>
        @endforelse

        @if($filas->count() > 0)
        <tr class="total-row">
            <td colspan="{{ count($columnas) }}">
                Total registros: {{ $filas->count() }}
            </td>
        </tr>
        @endif
    </tbody>
</table>

{{-- Pie de Página --}}
<div class="footer">
    <table>
        <tr>
            <td style="text-align: left; width: 60%;">SGP Chicamán &middot; Centro de Atención Permanente</td>
            <td style="text-align: right; width: 40%;">Uso interno &middot; {{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>
</div>

</body>
</html>
