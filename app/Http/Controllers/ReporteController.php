<?php

namespace App\Http\Controllers;

use App\Models\TurnoPersonal;
use App\Models\RegistroLlegada;
use App\Models\ReporteDiario;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /**
     * Panel principal de reportes con filtros.
     */
    public function index(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());

        // Estadísticas generales del rango de fechas
        $totalLlegadas = RegistroLlegada::whereBetween('fecha', [$fechaDesde, $fechaHasta])->count();
        $totalNuevos   = RegistroLlegada::whereBetween('fecha', [$fechaDesde, $fechaHasta])->where('es_nuevo', true)->count();
        $totalRecurrentes = $totalLlegadas - $totalNuevos;

        // Llegadas por día (para gráfica)
        $llegadasPorDia = RegistroLlegada::selectRaw('fecha, COUNT(*) as total')
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Distribución por sexo
        $porSexo = RegistroLlegada::join('pacientes', 'registros_llegada.id_paciente', '=', 'pacientes.id_paciente')
            ->selectRaw('pacientes.sexo, COUNT(*) as total')
            ->whereBetween('registros_llegada.fecha', [$fechaDesde, $fechaHasta])
            ->groupBy('pacientes.sexo')
            ->get();

        // Turnos del rango con sus reportes
        $turnos = TurnoPersonal::with(['usuario', 'reportesDiarios'])
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->withCount('registrosLlegada')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('reportes.index', compact(
            'fechaDesde', 'fechaHasta',
            'totalLlegadas', 'totalNuevos', 'totalRecurrentes',
            'llegadasPorDia', 'porSexo', 'turnos'
        ));
    }

    /**
     * Vista detallada del reporte de un turno específico.
     */
    public function diario($turnoId)
    {
        $turno = TurnoPersonal::with(['usuario', 'registrosLlegada.paciente.familia'])->findOrFail($turnoId);

        $llegadas         = $turno->registrosLlegada->sortBy('hora_llegada');
        $totalPacientes   = $llegadas->count();
        $totalNuevos      = $llegadas->where('es_nuevo', true)->count();
        $totalRecurrentes = $totalPacientes - $totalNuevos;

        // Obtener o generar el reporte diario
        $reporte = ReporteDiario::firstOrCreate(
            ['id_turno' => $turno->id_turno],
            [
                'fecha'           => $turno->fecha,
                'total_pacientes' => $totalPacientes,
                'total_nuevos'    => $totalNuevos,
                'total_recurrentes' => $totalRecurrentes,
            ]
        );

        // Actualizar con datos frescos
        $reporte->update([
            'total_pacientes'   => $totalPacientes,
            'total_nuevos'      => $totalNuevos,
            'total_recurrentes' => $totalRecurrentes,
            'generado_en'       => now(),
        ]);

        return view('reportes.diario', compact('turno', 'llegadas', 'reporte', 'totalPacientes', 'totalNuevos', 'totalRecurrentes'));
    }

    /**
     * Exportar el reporte de un turno como PDF.
     */
    public function exportarPdf($turnoId)
    {
        $turno = TurnoPersonal::with(['usuario', 'registrosLlegada.paciente.familia'])->findOrFail($turnoId);

        $llegadas         = $turno->registrosLlegada->sortBy('hora_llegada');
        $totalPacientes   = $llegadas->count();
        $totalNuevos      = $llegadas->where('es_nuevo', true)->count();
        $totalRecurrentes = $totalPacientes - $totalNuevos;

        $pdf = Pdf::loadView('reportes.pdf.diario', compact(
            'turno', 'llegadas', 'totalPacientes', 'totalNuevos', 'totalRecurrentes'
        ))->setPaper('letter', 'portrait');

        $filename = 'reporte-turno-' . $turno->id_turno . '-' . $turno->fecha->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Exportar estadísticas generales del rango en PDF.
     */
    public function exportarEstadisticasPdf(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());

        $totalLlegadas    = RegistroLlegada::whereBetween('fecha', [$fechaDesde, $fechaHasta])->count();
        $totalNuevos      = RegistroLlegada::whereBetween('fecha', [$fechaDesde, $fechaHasta])->where('es_nuevo', true)->count();
        $totalRecurrentes = $totalLlegadas - $totalNuevos;

        $llegadasPorDia = RegistroLlegada::selectRaw('fecha, COUNT(*) as total')
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $porSexo = RegistroLlegada::join('pacientes', 'registros_llegada.id_paciente', '=', 'pacientes.id_paciente')
            ->selectRaw('pacientes.sexo, COUNT(*) as total')
            ->whereBetween('registros_llegada.fecha', [$fechaDesde, $fechaHasta])
            ->groupBy('pacientes.sexo')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.estadisticas', compact(
            'fechaDesde', 'fechaHasta',
            'totalLlegadas', 'totalNuevos', 'totalRecurrentes',
            'llegadasPorDia', 'porSexo'
        ))->setPaper('letter', 'portrait');

        $filename = 'estadisticas-' . $fechaDesde . '-al-' . $fechaHasta . '.pdf';
        return $pdf->download($filename);
    }
}
