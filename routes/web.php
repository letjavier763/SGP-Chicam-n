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
        $turnoHoy         = \App\Models\TurnoPersonal::with(['usuario', 'recepcionista'])
                                ->whereDate('fecha', today())
                                ->orderByRaw('id_usuario = ? DESC', [auth()->id()])
                                ->orderBy('id_turno')
                                ->first();
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
    Route::get('/turnos', [TurnoController::class, 'index'])->name('turnos.index');
    Route::post('/turnos', [TurnoController::class, 'store'])->name('turnos.store');
    Route::put('/turnos/{id}', [TurnoController::class, 'update'])->name('turnos.update');
    Route::delete('/turnos/{id}', [TurnoController::class, 'destroy'])->name('turnos.destroy');

    // Recepcionistas
    Route::get('/recepcionistas', [TurnoController::class, 'recepcionistas'])->name('recepcionistas.index');
    Route::post('/recepcionistas', [TurnoController::class, 'storeRecepcionista'])->name('recepcionistas.store');
    Route::delete('/recepcionistas/{id}', [TurnoController::class, 'destroyRecepcionista'])->name('recepcionistas.destroy');

    // Ventanilla (registro de llegadas del día)
    Route::get('/ventanilla', [VentanillaController::class, 'index'])->name('ventanilla.index');
    Route::get('/ventanilla/buscar', [VentanillaController::class, 'buscar'])->name('ventanilla.buscar');
    Route::post('/ventanilla', [VentanillaController::class, 'store'])->name('ventanilla.store');
    Route::post('/ventanilla/iniciar-turno', [VentanillaController::class, 'iniciarTurno'])->name('ventanilla.iniciar-turno');
    Route::delete('/ventanilla/{id}', [VentanillaController::class, 'destroy'])->name('ventanilla.destroy');

    // ---------------------------------------------------------------
    // Fase 3 — 4ª Iteración: Reportería Estadística y PDF
    // ---------------------------------------------------------------

    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/turno/{turnoId}', [ReporteController::class, 'diario'])->name('diario');
        Route::get('/turno/{turnoId}/pdf', [ReporteController::class, 'exportarPdf'])->name('pdf');
        Route::get('/estadisticas/pdf', [ReporteController::class, 'exportarEstadisticasPdf'])->name('estadisticas.pdf');

        // ── 10 tipos de reportes exportables ────────────────────────────
        Route::get('/exportar/llegadas-rango',        [ReporteController::class, 'exportLlegadasRango'])->name('exportar.llegadas-rango');
        Route::get('/exportar/pacientes-nuevos',      [ReporteController::class, 'exportPacientesNuevos'])->name('exportar.pacientes-nuevos');
        Route::get('/exportar/pacientes-recurrentes', [ReporteController::class, 'exportPacientesRecurrentes'])->name('exportar.pacientes-recurrentes');
        Route::get('/exportar/por-sexo',              [ReporteController::class, 'exportPorSexo'])->name('exportar.por-sexo');
        Route::get('/exportar/por-turno',             [ReporteController::class, 'exportPorTurno'])->name('exportar.por-turno');
        Route::get('/exportar/padron-pacientes',      [ReporteController::class, 'exportPadronPacientes'])->name('exportar.padron-pacientes');
        Route::get('/exportar/padron-familias',       [ReporteController::class, 'exportPadronFamilias'])->name('exportar.padron-familias');
        Route::get('/exportar/alertas-duplicidad',    [ReporteController::class, 'exportAlertasDuplicidad'])->name('exportar.alertas-duplicidad');
        Route::get('/exportar/actividad-personal',    [ReporteController::class, 'exportActividadPersonal'])->name('exportar.actividad-personal');
        Route::get('/exportar/resumen-mensual',       [ReporteController::class, 'exportResumenMensual'])->name('exportar.resumen-mensual');
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
