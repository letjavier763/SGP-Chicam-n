<?php

namespace App\Http\Controllers;

use App\Models\TurnoPersonal;
use App\Models\Recepcionista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TurnoController extends Controller
{
    /**
     * Vista principal: calendario mensual de turnos.
     */
    public function index(Request $request)
    {
        $mes  = (int) $request->get('mes',  now()->month);
        $anio = (int) $request->get('anio', now()->year);

        $inicio = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
        $fin    = $inicio->copy()->endOfMonth();

        $turnos = TurnoPersonal::with(['recepcionista'])
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy(fn($t) => $t->fecha->toDateString());

        $recepcionistas = Recepcionista::where('activo', true)->orderBy('nombre')->get();

        return view('turnos.index', compact('turnos', 'recepcionistas', 'mes', 'anio', 'inicio'));
    }

    /**
     * Guardar nuevo turno.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'fecha'               => 'required|date',
            'hora_inicio'         => 'nullable',
            'hora_fin'            => 'nullable',
            'id_recepcionista'    => 'nullable|exists:recepcionistas,id_recepcionista',
            'nombre_nuevo_recep'  => 'nullable|string|max:150',
            'observaciones'       => 'nullable|string|max:500',
        ]);

        $idRecep   = $validated['id_recepcionista'] ?? null;
        $nombreRec = null;

        // Si ingresaron un nombre nuevo, creamos el recepcionista
        if (empty($idRecep) && !empty($request->nombre_nuevo_recep)) {
            $rec       = Recepcionista::create(['nombre' => trim($request->nombre_nuevo_recep), 'activo' => true]);
            $idRecep   = $rec->id_recepcionista;
            $nombreRec = $rec->nombre;
        } elseif ($idRecep) {
            $nombreRec = Recepcionista::find($idRecep)?->nombre;
        }

        TurnoPersonal::create([
            'id_usuario'           => Auth::id(),
            'id_recepcionista'     => $idRecep,
            'nombre_recepcionista' => $nombreRec,
            'fecha'                => $validated['fecha'],
            'tipo_turno'           => 'dia',
            'hora_inicio'          => $validated['hora_inicio'] ?? null,
            'hora_fin'             => $validated['hora_fin'] ?? null,
            'observaciones'        => $validated['observaciones'] ?? null,
        ]);

        return redirect()->route('turnos.index', ['mes' => date('n', strtotime($validated['fecha'])), 'anio' => date('Y', strtotime($validated['fecha']))])
            ->with('success', 'Turno creado correctamente.');
    }

    /**
     * Actualizar turno.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();
        $turno = TurnoPersonal::findOrFail($id);

        $validated = $request->validate([
            'fecha'               => 'required|date',
            'hora_inicio'         => 'nullable',
            'hora_fin'            => 'nullable',
            'id_recepcionista'    => 'nullable|exists:recepcionistas,id_recepcionista',
            'nombre_nuevo_recep'  => 'nullable|string|max:150',
            'observaciones'       => 'nullable|string|max:500',
        ]);

        $idRecep   = $validated['id_recepcionista'] ?? null;
        $nombreRec = null;

        if (empty($idRecep) && !empty($request->nombre_nuevo_recep)) {
            $rec       = Recepcionista::create(['nombre' => trim($request->nombre_nuevo_recep), 'activo' => true]);
            $idRecep   = $rec->id_recepcionista;
            $nombreRec = $rec->nombre;
        } elseif ($idRecep) {
            $nombreRec = Recepcionista::find($idRecep)?->nombre;
        }

        $turno->update([
            'id_recepcionista'     => $idRecep,
            'nombre_recepcionista' => $nombreRec,
            'fecha'                => $validated['fecha'],
            'tipo_turno'           => 'dia',
            'hora_inicio'          => $validated['hora_inicio'] ?? null,
            'hora_fin'             => $validated['hora_fin'] ?? null,
            'observaciones'        => $validated['observaciones'] ?? null,
        ]);

        return redirect()->route('turnos.index', ['mes' => date('n', strtotime($validated['fecha'])), 'anio' => date('Y', strtotime($validated['fecha']))])
            ->with('success', 'Turno actualizado correctamente.');
    }

    /**
     * Eliminar turno.
     */
    public function destroy($id)
    {
        $this->authorizeAdmin();
        $turno = TurnoPersonal::withCount('registrosLlegada')->findOrFail($id);

        if ($turno->registros_llegada_count > 0) {
            return back()->with('error', 'No se puede eliminar el turno porque tiene registros de llegada asociados.');
        }

        $turno->delete();
        return back()->with('success', 'Turno eliminado correctamente.');
    }

    // ── Recepcionistas ──────────────────────────────────────────────

    /**
     * Listar recepcionistas (AJAX JSON).
     */
    public function recepcionistas()
    {
        return response()->json(Recepcionista::where('activo', true)->orderBy('nombre')->get());
    }

    /**
     * Guardar nuevo recepcionista.
     */
    public function storeRecepcionista(Request $request)
    {
        $this->authorizeAdmin();
        $request->validate(['nombre' => 'required|string|max:150']);
        $rec = Recepcionista::create(['nombre' => trim($request->nombre), 'activo' => true]);
        return response()->json(['id_recepcionista' => $rec->id_recepcionista, 'nombre' => $rec->nombre]);
    }

    /**
     * Eliminar recepcionista.
     */
    public function destroyRecepcionista($id)
    {
        $this->authorizeAdmin();
        $rec = Recepcionista::findOrFail($id);
        $rec->update(['activo' => false]);
        return back()->with('success', 'Recepcionista desactivado.');
    }

    // ---------------------------------------------------------------
    private function authorizeAdmin(): void
    {
        if (!Auth::user()->esAdministrador()) {
            abort(403, 'Acceso no autorizado.');
        }
    }
}
