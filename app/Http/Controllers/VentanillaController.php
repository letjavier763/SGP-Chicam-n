<?php

namespace App\Http\Controllers;

use App\Models\TurnoPersonal;
use App\Models\Paciente;
use App\Models\RegistroLlegada;
use App\Models\Bitacora;
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
        $turnosHoy = TurnoPersonal::with('usuario')
            ->whereDate('fecha', $hoy)
            ->orderBy('hora_inicio')
            ->get();

        // Seleccionar turno: por parámetro, o el turno del usuario logueado de hoy
        $turnoId = $request->get('turno_id');
        $turnoActivo = null;

        if ($turnoId) {
            $turnoActivo = TurnoPersonal::find($turnoId);
        } else {
            // Buscar turno del usuario actual hoy
            $turnoActivo = TurnoPersonal::where('id_usuario', Auth::id())
                ->whereDate('fecha', $hoy)
                ->orderBy('hora_inicio')
                ->first();

            // Si es admin sin turno propio, tomar el primer turno del día
            if (!$turnoActivo && Auth::user()->esAdministrador() && $turnosHoy->isNotEmpty()) {
                $turnoActivo = $turnosHoy->first();
            }
        }

        // Llegadas del turno activo
        $llegadas = collect();
        if ($turnoActivo) {
            $llegadas = RegistroLlegada::with('paciente.familia')
                ->where('id_turno', $turnoActivo->id_turno)
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

        return view('ventanilla.index', compact(
            'turnosHoy', 'turnoActivo', 'llegadas', 'pacientes', 'buscarPaciente'
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
            'es_nuevo'      => 'boolean',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $turno = TurnoPersonal::findOrFail($validated['id_turno']);

        // Verificar que el paciente no esté ya registrado en este turno
        $yaRegistrado = RegistroLlegada::where('id_turno', $turno->id_turno)
            ->where('id_paciente', $validated['id_paciente'])
            ->exists();

        if ($yaRegistrado) {
            return back()->with('error', 'Este paciente ya fue registrado en el turno actual.');
        }

        $registro = RegistroLlegada::create([
            'id_paciente'   => $validated['id_paciente'],
            'id_turno'      => $turno->id_turno,
            'fecha'         => $turno->fecha,
            'hora_llegada'  => $validated['hora_llegada'],
            'es_nuevo'      => $request->boolean('es_nuevo'),
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
}
