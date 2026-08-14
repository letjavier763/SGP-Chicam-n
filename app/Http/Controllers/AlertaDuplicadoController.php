<?php

namespace App\Http\Controllers;

use App\Models\AlertaDuplicado;
use Illuminate\Http\Request;

class AlertaDuplicadoController extends Controller
{
    public function index(Request $request)
    {
        $query = AlertaDuplicado::with('usuario');

        if ($request->filled('tipo')) {
            $query->where('tipo_duplicado', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where('valor_duplicado', 'like', "%{$buscar}%");
        }

        $alertas = $query->orderBy('fecha_deteccion', 'desc')->paginate(20)->withQueryString();

        return view('alertas.index', compact('alertas'));
    }

    public function resolver(Request $request, $id)
    {
        $alerta = AlertaDuplicado::findOrFail($id);

        $validated = $request->validate([
            'accion_tomada' => 'required|string|max:50',
        ]);

        $alerta->update([
            'accion_tomada' => $validated['accion_tomada']
        ]);

        return back()->with('success', 'La alerta de duplicidad fue actualizada.');
    }
}
