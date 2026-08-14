<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BitacoraController extends Controller
{
    /**
     * Listado de eventos de la bitácora (solo Administrador).
     */
    public function index(Request $request)
    {
        if (!Auth::user()->esAdministrador()) {
            abort(403, 'Acceso no autorizado.');
        }

        $query = Bitacora::with('usuario')->orderBy('fecha', 'desc');

        if ($request->filled('usuario_id')) {
            $query->where('id_usuario', $request->usuario_id);
        }

        if ($request->filled('accion')) {
            $query->where('accion', 'ilike', '%' . $request->accion . '%');
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $eventos = $query->paginate(30)->withQueryString();

        $usuarios = \App\Models\Usuario::orderBy('nombre_completo')->get();

        return view('bitacora.index', compact('eventos', 'usuarios'));
    }
}
