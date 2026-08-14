<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Sesion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Procesa la autenticación del usuario.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Añadimos validación para que el usuario esté activo
        $credentials['activo'] = true;

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password'], 'activo' => true])) {
            $request->session()->regenerate();

            $user = Auth::user();
            $ip = $request->ip();

            // 1. Actualizar último acceso del usuario
            $user->ultimo_acceso = now();
            $user->save();

            // 2. Registrar sesión en la tabla sesiones
            $sesion = Sesion::create([
                'id_usuario'  => $user->id_usuario,
                'hora_inicio' => now(),
                'ip_equipo'   => $ip,
                'estado'      => 'activa',
            ]);

            // Guardamos el ID de la sesión en la sesión de Laravel para el cierre
            $request->session()->put('id_sesion_sgp', $sesion->id_sesion);

            // 3. Registrar en bitácora
            Bitacora::registrar(
                $user->id_usuario,
                'inicio_sesion',
                'usuarios',
                $user->id_usuario,
                "Inicio de sesión exitoso. Usuario: {$user->username}",
                $ip
            );

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => 'Las credenciales proporcionadas no coinciden con nuestros registros o la cuenta está inactiva.',
        ])->onlyInput('username');
    }

    /**
     * Cierra la sesión activa.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $ip = $request->ip();

        if ($user) {
            // 1. Cerrar sesión en la tabla sesiones
            $idSesion = $request->session()->get('id_sesion_sgp');
            if ($idSesion) {
                Sesion::where('id_sesion', $idSesion)->update([
                    'hora_cierre' => now(),
                    'estado'      => 'cerrada',
                ]);
            }

            // 2. Registrar en bitácora
            Bitacora::registrar(
                $user->id_usuario,
                'cierre_sesion',
                'usuarios',
                $user->id_usuario,
                "Cierre de sesión. Usuario: {$user->username}",
                $ip
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
