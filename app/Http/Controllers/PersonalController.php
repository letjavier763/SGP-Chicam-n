<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PersonalController extends Controller
{
    /**
     * Lista todos los usuarios con posibilidad de filtrado y búsqueda.
     */
    public function index(Request $request)
    {
        $query = Usuario::with('rol');

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_completo', 'ilike', "%{$buscar}%")
                  ->orWhere('username', 'ilike', "%{$buscar}%");
            });
        }

        if ($request->filled('id_rol')) {
            $query->where('id_rol', $request->id_rol);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $personal = $query->orderBy('nombre_completo', 'asc')->paginate(15)->withQueryString();
        $roles = Rol::orderBy('nombre_rol', 'asc')->get();

        return view('personal.index', compact('personal', 'roles'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        $roles = Rol::orderBy('nombre_rol', 'asc')->get();
        return view('personal.create', compact('roles'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:150',
            'username'        => 'required|string|max:50|unique:usuarios,username',
            'password'        => 'required|string|min:6|confirmed',
            'id_rol'          => 'required|exists:roles,id_rol',
        ]);

        $usuario = Usuario::create([
            'nombre_completo' => $validated['nombre_completo'],
            'username'        => $validated['username'],
            'password_hash'   => Hash::make($validated['password']),
            'id_rol'          => $validated['id_rol'],
            'activo'          => true,
        ]);

        // Registrar en bitácora
        Bitacora::registrar(
            Auth::id(),
            'creacion_usuario',
            'usuarios',
            $usuario->id_usuario,
            "Se creó el usuario '{$usuario->username}' con rol de ID {$usuario->id_rol}.",
            $request->ip()
        );

        return redirect()->route('personal.index')->with('success', 'Usuario registrado exitosamente.');
    }

    /**
     * Muestra el formulario de edición de un usuario.
     */
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        $roles = Rol::orderBy('nombre_rol', 'asc')->get();
        return view('personal.edit', compact('usuario', 'roles'));
    }

    /**
     * Actualiza los datos de un usuario.
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:150',
            'username'        => 'required|string|max:50|unique:usuarios,username,' . $id . ',id_usuario',
            'password'        => 'nullable|string|min:6|confirmed',
            'id_rol'          => 'required|exists:roles,id_rol',
        ]);

        $data = [
            'nombre_completo' => $validated['nombre_completo'],
            'username'        => $validated['username'],
            'id_rol'          => $validated['id_rol'],
        ];

        if (!empty($validated['password'])) {
            $data['password_hash'] = Hash::make($validated['password']);
        }

        $usuario->update($data);

        // Registrar en bitácora
        Bitacora::registrar(
            Auth::id(),
            'edicion_usuario',
            'usuarios',
            $usuario->id_usuario,
            "Se actualizaron los datos del usuario '{$usuario->username}'.",
            $request->ip()
        );

        return redirect()->route('personal.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Alterna el estado activo/inactivo del usuario.
     */
    public function toggleStatus(Request $request, $id)
    {
        if (Auth::id() == $id) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario = Usuario::findOrFail($id);
        $usuario->activo = !$usuario->activo;
        $usuario->save();

        $estadoStr = $usuario->activo ? 'activo' : 'inactivo';

        // Registrar en bitácora
        Bitacora::registrar(
            Auth::id(),
            'cambio_estado_usuario',
            'usuarios',
            $usuario->id_usuario,
            "Se cambió el estado del usuario '{$usuario->username}' a {$estadoStr}.",
            $request->ip()
        );

        return redirect()->route('personal.index')->with('success', "Estado del usuario cambiado a {$estadoStr} exitosamente.");
    }
}
