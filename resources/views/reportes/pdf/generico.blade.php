<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #1a1a2e; }

    .header { background: #1a3a5c; color: white; padding: 14px 20px; margin-bottom: 16px; }
    .header h1 { font-size: 14pt; font-weight: bold; margin-bottom: 2px; }
    .header .sub { font-size: 8.5pt; opacity: 0.85; }
    .header .meta { font-size: 7.5pt; opacity: 0.7; margin-top: 4px; }

    table { width: 100%; border-collapse: collapse; }
    thead th {
        background: #1a3a5c;
        color: white;
        padding: 6px 8px;
        text-align: left;
        font-size: 8pt;
        font-weight: bold;
        border: 1px solid #1a3a5c;
    }
    tbody tr:nth-child(even) { background: #f0f4f8; }
    tbody td {
        padding: 5px 8px;
        border: 1px solid #d1d9e0;
        font-size: 8pt;
        vertical-align: top;
    }
    .total-row { font-weight: bold; background: #e8f0fe !important; }

    .footer {
        position: fixed;
        bottom: 0;
        left: 0; right: 0;
        font-size: 7pt;
        color: #888;
        border-top: 1px solid #ddd;
        padding: 4px 20px;
        text-align: center;
    }
    .empty { text-align: center; padding: 30px; color: #888; font-style: italic; }
</style>
</head>
<body>

<div class="header">
    <h1>{{ $titulo }}</h1>
    <div class="sub">{{ $subtitulo }}</div>
    <div class="meta">CAP Chicamán &mdash; Generado el {{ now()->format('d/m/Y H:i') }}</div>
</div>

<table>
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
                    No hay datos para mostrar en el período seleccionado.
                </td>
            </tr>
        @endforelse

        @if($filas->count() > 0)
        <tr class="total-row">
            <td colspan="{{ count($columnas) }}">Total de registros: {{ $filas->count() }}</td>
        </tr>
        @endif
    </tbody>
</table>

<div class="footer">
    SGP Chicamán &mdash; Centro de Atención Permanente &mdash; {{ $titulo }} &mdash; {{ now()->format('d/m/Y') }}
</div>

</body>
</html>
