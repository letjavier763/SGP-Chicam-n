<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\AlertaDuplicadoController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\VentanillaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\PersonalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redireccionar raíz al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rutas de Autenticación (Públicas)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('detect.sqli')->name('login.post');
});

// Rutas Protegidas por Autenticación
Route::middleware(['auth', 'nocache'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Panel de Control General (Dashboard)
    Route::get('/dashboard', function () {
        $totalPacientes   = \App\Models\Paciente::count();
        $llegadasHoy      = \App\Models\RegistroLlegada::whereDate('fecha', today())->count();
        $alertasDuplicado = \App\Models\AlertaDuplicado::count();
        $turnoHoy         = \App\Models\TurnoPersonal::where('id_usuario', auth()->id())
                                ->whereDate('fecha', today())->first();
        $usuarios         = \App\Models\Usuario::where('activo', true)->orderBy('nombre_completo')->get();
        $familias         = \App\Models\Familia::where('activo', true)->orderBy('numero_familia')->get();
        
        return view('dashboard', compact('totalPacientes', 'llegadasHoy', 'alertasDuplicado', 'turnoHoy', 'usuarios', 'familias'));
    })->name('dashboard');

    // Rutas para cascada de ubicaciones (AJAX)
    Route::get('/api/ubicaciones/municipios/{departamentoId}', [LocationController::class, 'getMunicipios'])->name('api.ubicaciones.municipios');
    Route::get('/api/ubicaciones/comunidades/{municipioId}', [LocationController::class, 'getComunidades'])->name('api.ubicaciones.comunidades');
    Route::get('/api/familias/buscar', [FamiliaController::class, 'buscarAjax'])->name('api.familias.buscar');

    // Módulo de Núcleos Familiares
    Route::resource('familias', FamiliaController::class);
    Route::patch('/familias/{id}/toggle-status', [FamiliaController::class, 'toggleStatus'])->name('familias.toggle-status');

    // Módulo de Pacientes
    Route::get('/pacientes/verificar-duplicado', [PacienteController::class, 'checkDuplicate'])->name('pacientes.check-duplicate');
    Route::resource('pacientes', PacienteController::class);
    Route::patch('/pacientes/{id}/toggle-status', [PacienteController::class, 'toggleStatus'])->name('pacientes.toggle-status');

    // Módulo de Alertas de Duplicidad
    Route::get('/alertas-duplicado', [AlertaDuplicadoController::class, 'index'])->name('alertas.index');
    Route::patch('/alertas-duplicado/{id}/resolver', [AlertaDuplicadoController::class, 'resolver'])->name('alertas.resolver');

    // ---------------------------------------------------------------
    // Fase 3 — Módulo de Ventanilla y Turnos
    // ---------------------------------------------------------------

    // Turnos del Personal
    Route::resource('turnos', TurnoController::class)->except(['show']);

    // Ventanilla (registro de llegadas del día)
    Route::get('/ventanilla', [VentanillaController::class, 'index'])->name('ventanilla.index');
    Route::get('/ventanilla/buscar', [VentanillaController::class, 'buscar'])->name('ventanilla.buscar');
    Route::post('/ventanilla', [VentanillaController::class, 'store'])->name('ventanilla.store');
    Route::delete('/ventanilla/{id}', [VentanillaController::class, 'destroy'])->name('ventanilla.destroy');

    // ---------------------------------------------------------------
    // Fase 3 — 4ª Iteración: Reportería Estadística y PDF
    // ---------------------------------------------------------------

    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/turno/{turnoId}', [ReporteController::class, 'diario'])->name('diario');
        Route::get('/turno/{turnoId}/pdf', [ReporteController::class, 'exportarPdf'])->name('pdf');
        Route::get('/estadisticas/pdf', [ReporteController::class, 'exportarEstadisticasPdf'])->name('estadisticas.pdf');
    });

    // ---------------------------------------------------------------
    // Fase 4 — Bitácora (solo Administrador)
    // ---------------------------------------------------------------

    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');

    // Gestión de Personal
    Route::middleware(['role:Administrador'])->group(function () {
        Route::resource('personal', PersonalController::class)->except(['show']);
        Route::patch('/personal/{id}/toggle-status', [PersonalController::class, 'toggleStatus'])->name('personal.toggle-status');
    });
});
