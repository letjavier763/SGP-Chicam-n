<?php

namespace App\Http\Controllers;

use App\Models\TurnoPersonal;
use App\Models\Paciente;
use App\Models\RegistroLlegada;
use App\Models\Bitacora;
use App\Models\Departamento;
use App\Models\Recepcionista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VentanillaController extends Controller
{
    /**
     * Panel principal de la ventanilla.
     * Muestra el turno activo del día y los registros de llegada.
     */
    public function index(Request $request)
    {
        $hoy = today();

        // Obtener los turnos disponibles del día de hoy
        $turnosHoy = TurnoPersonal::with(['usuario', 'recepcionista'])
            ->whereDate('fecha', $hoy)
            ->orderBy('id_turno')
            ->get();

        $turnoId = $request->get('turno_id');
        $turnoActivo = null;

        if ($turnosHoy->count() === 1) {
            // Si hay solo un turno disponible en el día, se asigna automáticamente
            $turnoActivo = $turnosHoy->first();
        } elseif ($turnosHoy->count() > 1) {
            // Si hay más de un turno, permite elegir cuál está en el turno
            if ($turnoId) {
                $turnoActivo = $turnosHoy->firstWhere('id_turno', $turnoId);
            }
            if (!$turnoActivo) {
                // Por defecto, asignar el del usuario logueado o el primero
                $turnoActivo = $turnosHoy->firstWhere('id_usuario', Auth::id()) ?? $turnosHoy->first();
            }
        }

        // Llegadas del turno activo del día de hoy
        $llegadas = collect();
        if ($turnoActivo) {
            $llegadas = RegistroLlegada::with('paciente.familia')
                ->where('id_turno', $turnoActivo->id_turno)
                ->whereDate('fecha', $hoy)
                ->orderBy('hora_llegada', 'desc')
                ->get();
        }

        // Búsqueda de paciente para registrar llegada
        $pacientes = collect();
        $buscarPaciente = $request->get('buscar_paciente');
        if ($buscarPaciente) {
            $pacientes = Paciente::with('familia')
                ->where('activo', true)
                ->where(function ($q) use ($buscarPaciente) {
                    $q->where('nombres', 'ilike', "%{$buscarPaciente}%")
                      ->orWhere('apellidos', 'ilike', "%{$buscarPaciente}%")
                      ->orWhere('dpi', 'like', "%{$buscarPaciente}%")
                      ->orWhere('numero_expediente_fisico', 'like', "%{$buscarPaciente}%");
                })
                ->limit(10)
                ->get();
        }

        $departamentos  = Departamento::orderBy('nombre')->get();
        $recepcionistas = Recepcionista::where('activo', true)->orderBy('nombre')->get();

        return view('ventanilla.index', compact(
            'turnosHoy', 'turnoActivo', 'llegadas', 'pacientes', 'buscarPaciente', 'departamentos', 'recepcionistas'
        ));
    }

    /**
     * Registrar llegada de un paciente en el turno activo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_turno'      => 'required|exists:turnos_personal,id_turno',
            'id_paciente'   => 'required|exists:pacientes,id_paciente',
            'hora_llegada'  => 'required',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $turno = TurnoPersonal::findOrFail($validated['id_turno']);

        // Verificar que el paciente no esté ya registrado en este turno el día de hoy
        $yaRegistrado = RegistroLlegada::where('id_turno', $turno->id_turno)
            ->whereDate('fecha', today())
            ->where('id_paciente', $validated['id_paciente'])
            ->exists();

        if ($yaRegistrado) {
            return back()->with('error', 'Este paciente ya fue registrado hoy en el turno actual.');
        }

        // Determinar automáticamente si es primera visita (si no tiene registros previos de llegada)
        $tieneVisitasPrevias = RegistroLlegada::where('id_paciente', $validated['id_paciente'])->exists();
        $esNuevo = !$tieneVisitasPrevias;

        $registro = RegistroLlegada::create([
            'id_paciente'   => $validated['id_paciente'],
            'id_turno'      => $turno->id_turno,
            'fecha'         => today(),
            'hora_llegada'  => $validated['hora_llegada'],
            'es_nuevo'      => $esNuevo,
            'observaciones' => $validated['observaciones'] ?? null,
        ]);

        // Registrar en bitácora
        Bitacora::registrar(
            Auth::id(),
            'crear',
            'registros_llegada',
            $registro->id_registro,
            "Llegada registrada: Paciente #{$validated['id_paciente']} en turno #{$turno->id_turno}",
            $request->ip()
        );

        return redirect()->route('ventanilla.index', ['turno_id' => $turno->id_turno])
            ->with('success', 'Llegada registrada correctamente.');
    }

    /**
     * Anular / eliminar un registro de llegada.
     */
    public function destroy(Request $request, $id)
    {
        $registro = RegistroLlegada::findOrFail($id);
        $turnoId  = $registro->id_turno;

        $registro->delete();

        Bitacora::registrar(
            Auth::id(),
            'eliminar',
            'registros_llegada',
            (int) $id,
            "Registro de llegada #{$id} anulado.",
            $request->ip()
        );

        return redirect()->route('ventanilla.index', ['turno_id' => $turnoId])
            ->with('success', 'Registro de llegada anulado.');
    }

    /**
     * Búsqueda AJAX de pacientes para ventanilla.
     * Retorna JSON con la lista de pacientes que coinciden y si ya están
     * registrados en el turno activo, para permitir registro rápido de un solo clic.
     */
    public function buscar(Request $request)
    {
        $termino = $request->get('q', '');
        $turnoId = $request->get('turno_id');

        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        // Obtener IDs ya registrados en el turno el día de hoy
        $yaRegistrados = collect();
        if ($turnoId) {
            $yaRegistrados = RegistroLlegada::where('id_turno', $turnoId)
                ->whereDate('fecha', today())
                ->pluck('id_paciente');
        }

        // Buscar pacientes activos que coincidan
        $pacientes = Paciente::with('familia.comunidad')
            ->where('activo', true)
            ->where(function ($q) use ($termino) {
                $q->where('nombres', 'ilike', "%{$termino}%")
                  ->orWhere('apellidos', 'ilike', "%{$termino}%")
                  ->orWhere('dpi', 'like', "%{$termino}%")
                  ->orWhere('numero_expediente_fisico', 'like', "%{$termino}%")
                  ->orWhereHas('familia', fn($fq) =>
                      $fq->where('numero_familia', 'ilike', "%{$termino}%")
                         ->orWhere('apellido_cabeza', 'ilike', "%{$termino}%")
                  );
            })
            ->limit(12)
            ->get();

        $resultado = $pacientes->map(function ($p) use ($yaRegistrados) {
            return [
                'id_paciente'            => $p->id_paciente,
                'nombres'                => $p->nombres,
                'apellidos'              => $p->apellidos,
                'dpi'                    => $p->dpi,
                'sexo'                   => $p->sexo,
                'numero_expediente_fisico' => $p->numero_expediente_fisico,
                'edad'                   => optional($p->fecha_nacimiento)->age,
                'familia_numero'         => optional($p->familia)->numero_familia,
                'familia_cabeza'         => optional($p->familia)->apellido_cabeza,
                'comunidad'              => optional(optional($p->familia)->comunidad)->nombre,
                'ya_registrado'          => $yaRegistrados->contains($p->id_paciente),
            ];
        });

        return response()->json($resultado);
    }

    /**
     * Iniciar un turno rápidamente desde Ventanilla cuando no hay turnos activos o se agrega otro.
     */
    public function iniciarTurno(Request $request)
    {
        $validated = $request->validate([
            'id_recepcionista'   => 'nullable|string',
            'nombre_nuevo_recep' => 'nullable|string|max:150',
            'observaciones'      => 'nullable|string|max:500',
        ]);

        $idRecep   = $validated['id_recepcionista'] ?? null;
        $nombreRec = null;

        if ($idRecep === 'nuevo' || (empty($idRecep) && !empty($request->nombre_nuevo_recep))) {
            $nombreNuevo = trim($request->nombre_nuevo_recep ?? '');
            if (!empty($nombreNuevo)) {
                $rec = Recepcionista::firstOrCreate(
                    ['nombre' => $nombreNuevo],
                    ['activo' => true]
                );
                $idRecep   = $rec->id_recepcionista;
                $nombreRec = $rec->nombre;
            }
        } elseif (!empty($idRecep)) {
            $rec = Recepcionista::find($idRecep);
            if ($rec) {
                $nombreRec = $rec->nombre;
            }
        }

        if (empty($nombreRec)) {
            return back()->with('error', 'Por favor selecciona o ingresa el nombre de quien atenderá en ventanilla.');
        }

        $turno = TurnoPersonal::create([
            'id_usuario'           => Auth::id(),
            'id_recepcionista'     => $idRecep ?: null,
            'nombre_recepcionista' => $nombreRec,
            'fecha'                => today()->toDateString(),
            'tipo_turno'           => 'dia',
            'observaciones'        => $validated['observaciones'] ?? null,
        ]);

        Bitacora::registrar(
            Auth::id(),
            'crear',
            'turnos_personal',
            $turno->id_turno,
            "Turno del día iniciado desde ventanilla: {$nombreRec}",
            $request->ip()
        );

        return redirect()->route('ventanilla.index', ['turno_id' => $turno->id_turno])
            ->with('success', "Turno iniciado para {$nombreRec}. Puede registrar llegadas.");
    }
}
