<?php

namespace App\Http\Controllers;

use App\Models\TurnoPersonal;
use App\Models\RegistroLlegada;
use App\Models\ReporteDiario;
use App\Models\Paciente;
use App\Models\Familia;
use App\Models\AlertaDuplicado;
use App\Models\Usuario;
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

        $reporte = ReporteDiario::firstOrCreate(
            ['id_turno' => $turno->id_turno],
            [
                'fecha'           => $turno->fecha,
                'total_pacientes' => $totalPacientes,
                'total_nuevos'    => $totalNuevos,
                'total_recurrentes' => $totalRecurrentes,
            ]
        );

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

    // ══════════════════════════════════════════════════════════
    // REPORTE 1: Llegadas en un rango de fechas (listado)
    // ══════════════════════════════════════════════════════════
    public function exportLlegadasRango(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());

        $datos = RegistroLlegada::with(['paciente', 'turno.usuario'])
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->orderBy('fecha')->orderBy('hora_llegada')
            ->get();

        $titulo = 'Registro de Llegadas';
        $subtitulo = "Del $fechaDesde al $fechaHasta";
        $columnas = ['Fecha', 'Hora', 'Paciente', 'Expediente', 'Es Nuevo', 'Personal de Turno'];
        $filas = $datos->map(fn($r) => [
            $r->fecha,
            $r->hora_llegada ?? '—',
            optional($r->paciente)->nombres . ' ' . optional($r->paciente)->apellidos,
            optional($r->paciente)->numero_expediente_fisico ?? '—',
            $r->es_nuevo ? 'Sí' : 'No',
            optional(optional($r->turno)->usuario)->nombre_completo ?? '—',
        ]);

        return $this->generarPdfGenerico($titulo, $subtitulo, $columnas, $filas, 'llegadas-rango');
    }

    // ══════════════════════════════════════════════════════════
    // REPORTE 2: Pacientes nuevos registrados
    // ══════════════════════════════════════════════════════════
    public function exportPacientesNuevos(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());

        $datos = RegistroLlegada::with('paciente')
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->where('es_nuevo', true)
            ->orderBy('fecha')
            ->get();

        $titulo = 'Pacientes Nuevos';
        $subtitulo = "Del $fechaDesde al $fechaHasta";
        $columnas = ['Fecha', 'Nombres', 'Apellidos', 'DPI', 'Expediente', 'Sexo'];
        $filas = $datos->map(fn($r) => [
            $r->fecha,
            optional($r->paciente)->nombres,
            optional($r->paciente)->apellidos,
            optional($r->paciente)->dpi ?? '—',
            optional($r->paciente)->numero_expediente_fisico ?? '—',
            optional($r->paciente)->sexo === 'F' ? 'Femenino' : 'Masculino',
        ]);

        return $this->generarPdfGenerico($titulo, $subtitulo, $columnas, $filas, 'pacientes-nuevos');
    }

    // ══════════════════════════════════════════════════════════
    // REPORTE 3: Pacientes recurrentes
    // ══════════════════════════════════════════════════════════
    public function exportPacientesRecurrentes(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());

        $datos = RegistroLlegada::with('paciente')
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->where('es_nuevo', false)
            ->orderBy('fecha')
            ->get();

        $titulo = 'Pacientes Recurrentes';
        $subtitulo = "Del $fechaDesde al $fechaHasta";
        $columnas = ['Fecha', 'Nombres', 'Apellidos', 'DPI', 'Expediente'];
        $filas = $datos->map(fn($r) => [
            $r->fecha,
            optional($r->paciente)->nombres,
            optional($r->paciente)->apellidos,
            optional($r->paciente)->dpi ?? '—',
            optional($r->paciente)->numero_expediente_fisico ?? '—',
        ]);

        return $this->generarPdfGenerico($titulo, $subtitulo, $columnas, $filas, 'pacientes-recurrentes');
    }

    // ══════════════════════════════════════════════════════════
    // REPORTE 4: Distribución de llegadas por sexo
    // ══════════════════════════════════════════════════════════
    public function exportPorSexo(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());

        $datos = RegistroLlegada::join('pacientes', 'registros_llegada.id_paciente', '=', 'pacientes.id_paciente')
            ->selectRaw('pacientes.sexo, DATE(registros_llegada.fecha) as fecha, COUNT(*) as total')
            ->whereBetween('registros_llegada.fecha', [$fechaDesde, $fechaHasta])
            ->groupBy('pacientes.sexo', DB::raw('DATE(registros_llegada.fecha)'))
            ->orderBy('fecha')
            ->get();

        $titulo = 'Llegadas por Sexo';
        $subtitulo = "Del $fechaDesde al $fechaHasta";
        $columnas = ['Fecha', 'Sexo', 'Total'];
        $filas = $datos->map(fn($r) => [
            $r->fecha,
            $r->sexo === 'F' ? 'Femenino' : 'Masculino',
            $r->total,
        ]);

        return $this->generarPdfGenerico($titulo, $subtitulo, $columnas, $filas, 'por-sexo');
    }

    // ══════════════════════════════════════════════════════════
    // REPORTE 5: Resumen por turno
    // ══════════════════════════════════════════════════════════
    public function exportPorTurno(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());

        $datos = TurnoPersonal::with('usuario')
            ->withCount(['registrosLlegada', 'registrosLlegada as nuevos_count' => fn($q) => $q->where('es_nuevo', true)])
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->orderBy('fecha')
            ->get();

        $titulo = 'Resumen por Turno';
        $subtitulo = "Del $fechaDesde al $fechaHasta";
        $columnas = ['Fecha', 'Tipo de Turno', 'Personal', 'Total Llegadas', 'Nuevos', 'Recurrentes'];
        $filas = $datos->map(fn($t) => [
            $t->fecha->format('d/m/Y'),
            ucfirst($t->tipo_turno),
            optional($t->usuario)->nombre_completo ?? '—',
            $t->registros_llegada_count,
            $t->nuevos_count,
            $t->registros_llegada_count - $t->nuevos_count,
        ]);

        return $this->generarPdfGenerico($titulo, $subtitulo, $columnas, $filas, 'por-turno');
    }

    // ══════════════════════════════════════════════════════════
    // REPORTE 6: Padrón general de pacientes
    // ══════════════════════════════════════════════════════════
    public function exportPadronPacientes(Request $request)
    {
        $datos = Paciente::orderBy('apellidos')->orderBy('nombres')->get();

        $titulo = 'Padrón General de Pacientes';
        $subtitulo = 'Generado el ' . now()->format('d/m/Y H:i');
        $columnas = ['Expediente', 'Nombres', 'Apellidos', 'DPI', 'Sexo', 'F. Nacimiento', 'Estado'];
        $filas = $datos->map(fn($p) => [
            $p->numero_expediente_fisico ?? '—',
            $p->nombres,
            $p->apellidos,
            $p->dpi ?? '—',
            $p->sexo === 'F' ? 'Femenino' : 'Masculino',
            optional($p->fecha_nacimiento)->format('d/m/Y') ?? '—',
            $p->activo ? 'Activo' : 'Inactivo',
        ]);

        return $this->generarPdfGenerico($titulo, $subtitulo, $columnas, $filas, 'padron-pacientes', 'landscape');
    }

    // ══════════════════════════════════════════════════════════
    // REPORTE 7: Padrón de familias
    // ══════════════════════════════════════════════════════════
    public function exportPadronFamilias(Request $request)
    {
        $datos = Familia::withCount('pacientes')->orderBy('numero_familia')->get();

        $titulo = 'Padrón de Familias Registradas';
        $subtitulo = 'Generado el ' . now()->format('d/m/Y H:i');
        $columnas = ['No. Familia', 'Nombre Familia', 'Municipio', 'Comunidad', 'Miembros'];
        $filas = $datos->map(fn($f) => [
            $f->numero_familia ?? '—',
            $f->nombre_familia ?? '—',
            $f->municipio ?? '—',
            $f->comunidad ?? '—',
            $f->pacientes_count,
        ]);

        return $this->generarPdfGenerico($titulo, $subtitulo, $columnas, $filas, 'padron-familias');
    }

    // ══════════════════════════════════════════════════════════
    // REPORTE 8: Historial de alertas de duplicidad
    // ══════════════════════════════════════════════════════════
    public function exportAlertasDuplicidad(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());

        $datos = AlertaDuplicado::with('usuario')
            ->whereDate('fecha_deteccion', '>=', $fechaDesde)
            ->whereDate('fecha_deteccion', '<=', $fechaHasta)
            ->orderBy('fecha_deteccion', 'desc')
            ->get();

        $titulo = 'Alertas de Duplicidad';
        $subtitulo = "Del $fechaDesde al $fechaHasta";
        $columnas = ['Fecha/Hora', 'Usuario', 'Tipo', 'Valor Duplicado', 'Estado'];
        $filas = $datos->map(fn($a) => [
            optional($a->fecha_deteccion)->format('d/m/Y H:i'),
            optional($a->usuario)->nombre_completo ?? 'Sistema',
            strtoupper($a->tipo_duplicado),
            $a->valor_duplicado,
            ucwords(str_replace('_', ' ', $a->accion_tomada ?? 'pendiente')),
        ]);

        return $this->generarPdfGenerico($titulo, $subtitulo, $columnas, $filas, 'alertas-duplicidad');
    }

    // ══════════════════════════════════════════════════════════
    // REPORTE 9: Actividad por personal / recepcionista
    // ══════════════════════════════════════════════════════════
    public function exportActividadPersonal(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->get('fecha_hasta', now()->toDateString());

        $datos = TurnoPersonal::with('usuario')
            ->selectRaw('id_usuario, COUNT(*) as turnos_trabajados, SUM(registros_count) as total_llegadas')
            ->withCount(['registrosLlegada as registros_count'])
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->groupBy('id_usuario')
            ->with('usuario')
            ->get();

        // Usar query directa más simple
        $datos = DB::table('turnos_personal')
            ->join('usuarios', 'turnos_personal.id_usuario', '=', 'usuarios.id_usuario')
            ->selectRaw('usuarios.nombre_completo, COUNT(turnos_personal.id_turno) as turnos, COALESCE(SUM(rc.cnt),0) as llegadas')
            ->leftJoinSub(
                DB::table('registros_llegada')->selectRaw('id_turno, COUNT(*) as cnt')->groupBy('id_turno'),
                'rc', 'rc.id_turno', '=', 'turnos_personal.id_turno'
            )
            ->whereBetween('turnos_personal.fecha', [$fechaDesde, $fechaHasta])
            ->groupBy('usuarios.nombre_completo')
            ->orderByDesc('llegadas')
            ->get();

        $titulo = 'Actividad por Personal';
        $subtitulo = "Del $fechaDesde al $fechaHasta";
        $columnas = ['Personal', 'Turnos Trabajados', 'Total Llegadas Atendidas'];
        $filas = $datos->map(fn($r) => [
            $r->nombre_completo,
            $r->turnos,
            $r->llegadas,
        ]);

        return $this->generarPdfGenerico($titulo, $subtitulo, $columnas, $filas, 'actividad-personal');
    }

    // ══════════════════════════════════════════════════════════
    // REPORTE 10: Resumen mensual consolidado
    // ══════════════════════════════════════════════════════════
    public function exportResumenMensual(Request $request)
    {
        $mes  = $request->get('mes', now()->format('Y-m'));
        $desde = $mes . '-01';
        $hasta = date('Y-m-t', strtotime($desde));

        $datos = RegistroLlegada::selectRaw('DATE(fecha) as dia, COUNT(*) as total, SUM(CASE WHEN es_nuevo THEN 1 ELSE 0 END) as nuevos')
            ->whereBetween('fecha', [$desde, $hasta])
            ->groupBy(DB::raw('DATE(fecha)'))
            ->orderBy('dia')
            ->get();

        $titulo = 'Resumen Mensual Consolidado';
        $subtitulo = 'Mes: ' . \Carbon\Carbon::parse($desde)->locale('es')->isoFormat('MMMM YYYY');
        $columnas = ['Día', 'Total Llegadas', 'Nuevos', 'Recurrentes'];
        $filas = $datos->map(fn($r) => [
            \Carbon\Carbon::parse($r->dia)->format('d/m/Y'),
            $r->total,
            $r->nuevos,
            $r->total - $r->nuevos,
        ]);

        return $this->generarPdfGenerico($titulo, $subtitulo, $columnas, $filas, 'resumen-mensual-' . $mes);
    }

    // ══════════════════════════════════════════════════════════
    // Helper: Genera PDF genérico con tabla
    // stream() para previsualizar, download() para exportar
    // ══════════════════════════════════════════════════════════
    private function generarPdfGenerico(string $titulo, string $subtitulo, array $columnas, $filas, string $nombreArchivo, string $orientacion = 'portrait')
    {
        $pdf = Pdf::loadView('reportes.pdf.generico', compact('titulo', 'subtitulo', 'columnas', 'filas'))
            ->setPaper('letter', $orientacion);

        $filename = "reporte-{$nombreArchivo}-" . now()->format('Y-m-d') . '.pdf';

        if (request()->boolean('preview')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
