<?php

namespace App\Http\Controllers;

use App\Models\TurnoPersonal;
use App\Models\Recepcionista;
use App\Services\GeminiOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

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

    // ── Escaneo de Rol de Turnos ─────────────────────────────────────

    /**
     * Procesar imagen de rol de turnos.
     */
    public function escanearRol(Request $request, GeminiOcrService $ocrService)
    {
        $this->authorizeAdmin();

        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:15360',
        ], [
            'imagen.required' => 'Debes adjuntar o tomar una imagen del rol de turnos.',
            'imagen.image'    => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.max'      => 'La imagen no debe superar los 15 MB.',
        ]);

        try {
            $datos = $ocrService->escanearRolTurnos($request->file('imagen'));

            // Obtener recepcionistas activos de la base de datos
            $recepcionistas = Recepcionista::where('activo', true)->orderBy('nombre')->get();

            // Mapear turnos y emparejar con recepcionistas existentes
            $turnosMapeados = collect($datos['turnos'] ?? [])->map(function ($turno) use ($recepcionistas) {
                $nombreDetectado = trim($turno['nombre'] ?? '');
                $normDetectado   = $this->normalizarTexto($nombreDetectado);

                // Buscar coincidencia exacta o normalizada
                $coincidencia = $recepcionistas->first(function ($r) use ($normDetectado) {
                    return $this->normalizarTexto($r->nombre) === $normDetectado;
                });

                return [
                    'fecha'                => $turno['fecha'] ?? null,
                    'dia'                  => $turno['dia'] ?? '',
                    'nombre_detectado'     => $nombreDetectado,
                    'id_recepcionista'     => $coincidencia?->id_recepcionista ?? null,
                    'nombre_recepcionista' => $coincidencia?->nombre ?? $nombreDetectado,
                    'es_nuevo'             => $coincidencia === null,
                    'hora_inicio'          => '08:00',
                    'hora_fin'             => '16:00',
                    'observaciones'        => 'Turno asignado según rol de ventanilla',
                ];
            });

            return response()->json([
                'success'        => true,
                'titulo'         => $datos['titulo'] ?? 'Rol de Ventanilla',
                'mes'            => $datos['mes'] ?? (int) now()->month,
                'anio'           => $datos['anio'] ?? (int) now()->year,
                'turnos'         => $turnosMapeados,
                'recepcionistas' => $recepcionistas,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Importar turnos confirmados en lote.
     */
    public function importarLote(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'turnos'                      => 'required|array|min:1',
            'turnos.*.fecha'              => 'required|date',
            'turnos.*.id_recepcionista'   => 'nullable',
            'turnos.*.nombre_nuevo'       => 'nullable|string|max:150',
            'turnos.*.nombre_recep'       => 'nullable|string|max:150',
            'turnos.*.hora_inicio'        => 'nullable',
            'turnos.*.hora_fin'           => 'nullable',
            'turnos.*.observaciones'      => 'nullable|string|max:500',
            'sobrescribir'                => 'nullable|boolean',
        ]);

        $sobrescribir = (bool) ($validated['sobrescribir'] ?? true);
        $turnos       = $validated['turnos'];
        $guardados    = 0;
        $nuevosRecepCache = [];

        DB::transaction(function () use ($turnos, $sobrescribir, &$guardados, &$nuevosRecepCache) {
            foreach ($turnos as $t) {
                $idRecep   = $t['id_recepcionista'] ?? null;
                $nombreRec = $t['nombre_recep'] ?? null;

                // Crear recepcionista nuevo si fue indicado
                if ($idRecep === '__nuevo__' || (empty($idRecep) && !empty($t['nombre_nuevo']))) {
                    $nombreACrear = trim($t['nombre_nuevo'] ?? $nombreRec);
                    $normKey = $this->normalizarTexto($nombreACrear);

                    if (isset($nuevosRecepCache[$normKey])) {
                        $idRecep   = $nuevosRecepCache[$normKey]['id'];
                        $nombreRec = $nuevosRecepCache[$normKey]['nombre'];
                    } else {
                        $nuevo = Recepcionista::firstOrCreate(
                            ['nombre' => $nombreACrear],
                            ['activo' => true]
                        );
                        $idRecep   = $nuevo->id_recepcionista;
                        $nombreRec = $nuevo->nombre;
                        $nuevosRecepCache[$normKey] = ['id' => $idRecep, 'nombre' => $nombreRec];
                    }
                } elseif (!empty($idRecep)) {
                    $rec = Recepcionista::find($idRecep);
                    if ($rec) {
                        $nombreRec = $rec->nombre;
                    }
                }

                $datosTurno = [
                    'id_usuario'           => Auth::id(),
                    'id_recepcionista'     => $idRecep ?: null,
                    'nombre_recepcionista' => $nombreRec ?: null,
                    'fecha'                => $t['fecha'],
                    'tipo_turno'           => 'dia',
                    'hora_inicio'          => !empty($t['hora_inicio']) ? $t['hora_inicio'] : null,
                    'hora_fin'             => !empty($t['hora_fin']) ? $t['hora_fin'] : null,
                    'observaciones'        => $t['observaciones'] ?? null,
                ];

                if ($sobrescribir) {
                    $existente = TurnoPersonal::where('fecha', $t['fecha'])->first();
                    if ($existente) {
                        $existente->update($datosTurno);
                    } else {
                        TurnoPersonal::create($datosTurno);
                    }
                } else {
                    TurnoPersonal::create($datosTurno);
                }

                $guardados++;
            }
        });

        $primerFecha = $turnos[0]['fecha'] ?? now()->toDateString();
        $mesDestino  = (int) date('n', strtotime($primerFecha));
        $anioDestino = (int) date('Y', strtotime($primerFecha));

        return response()->json([
            'success' => true,
            'mensaje' => "¡Se importaron exitosamente {$guardados} turnos al calendario!",
            'mes'     => $mesDestino,
            'anio'    => $anioDestino,
            'url'     => route('turnos.index', ['mes' => $mesDestino, 'anio' => $anioDestino]),
        ]);
    }

    /**
     * Normalizar cadenas de texto para comparar nombres sin distinción de tildes o mayúsculas.
     */
    private function normalizarTexto(string $texto): string
    {
        $texto = mb_strtolower(trim($texto), 'UTF-8');
        $translit = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ü' => 'u', 'ñ' => 'n',
        ];
        return strtr($texto, $translit);
    }

    // ---------------------------------------------------------------
    private function authorizeAdmin(): void
    {
        if (!Auth::user()->esAdministrador()) {
            abort(403, 'Acceso no autorizado.');
        }
    }
}
