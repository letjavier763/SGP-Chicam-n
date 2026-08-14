<?php

namespace App\Http\Controllers;

use App\Models\TurnoPersonal;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TurnoController extends Controller
{
    /**
     * Listar todos los turnos (Admin ve todos, Recepcionista ve solo los suyos).
     */
    public function index(Request $request)
    {
        $query = TurnoPersonal::with('usuario')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc');

        if (!Auth::user()->esAdministrador()) {
            $query->where('id_usuario', Auth::id());
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        if ($request->filled('tipo_turno')) {
            $query->where('tipo_turno', $request->tipo_turno);
        }

        $turnos = $query->paginate(20)->withQueryString();

        return view('turnos.index', compact('turnos'));
    }

    /**
     * Formulario de creación (solo Administrador).
     */
    public function create()
    {
        $this->authorizeAdmin();
        $usuarios = Usuario::where('activo', true)->orderBy('nombre_completo')->get();
        return view('turnos.create', compact('usuarios'));
    }

    /**
     * Guardar nuevo turno.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'id_usuario'    => 'required|exists:usuarios,id_usuario',
            'fecha'         => 'required|date',
            'tipo_turno'    => 'required|in:matutino,vespertino,nocturno',
            'hora_inicio'   => 'required',
            'hora_fin'      => 'required|after:hora_inicio',
            'observaciones' => 'nullable|string|max:500',
        ]);

        TurnoPersonal::create($validated);

        return redirect()->route('turnos.index')
            ->with('success', 'Turno creado correctamente.');
    }

    /**
     * Formulario de edición (solo Administrador).
     */
    public function edit($id)
    {
        $this->authorizeAdmin();
        $turno    = TurnoPersonal::findOrFail($id);
        $usuarios = Usuario::where('activo', true)->orderBy('nombre_completo')->get();
        return view('turnos.edit', compact('turno', 'usuarios'));
    }

    /**
     * Actualizar turno.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();
        $turno = TurnoPersonal::findOrFail($id);

        $validated = $request->validate([
            'id_usuario'    => 'required|exists:usuarios,id_usuario',
            'fecha'         => 'required|date',
            'tipo_turno'    => 'required|in:matutino,vespertino,nocturno',
            'hora_inicio'   => 'required',
            'hora_fin'      => 'required|after:hora_inicio',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $turno->update($validated);

        return redirect()->route('turnos.index')
            ->with('success', 'Turno actualizado correctamente.');
    }

    /**
     * Eliminar turno (solo si no tiene llegadas registradas).
     */
    public function destroy($id)
    {
        $this->authorizeAdmin();
        $turno = TurnoPersonal::withCount('registrosLlegada')->findOrFail($id);

        if ($turno->registros_llegada_count > 0) {
            return back()->with('error', 'No se puede eliminar el turno porque tiene registros de llegada asociados.');
        }

        $turno->delete();
        return redirect()->route('turnos.index')
            ->with('success', 'Turno eliminado correctamente.');
    }

    // ---------------------------------------------------------------
    private function authorizeAdmin(): void
    {
        if (!Auth::user()->esAdministrador()) {
            abort(403, 'Acceso no autorizado.');
        }
    }
}
