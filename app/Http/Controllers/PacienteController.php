<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Familia;
use App\Models\Comunidad;
use App\Models\AlertaDuplicado;
use App\Models\Bitacora;
use App\Services\GeminiOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Paciente::with(['familia.comunidad']);

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $criterio = $request->get('criterio', 'todos');

            if ($criterio === 'nombre') {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombres', 'ilike', "%{$buscar}%")
                      ->orWhere('apellidos', 'ilike', "%{$buscar}%");
                });
            } elseif ($criterio === 'numero_registro') {
                $query->whereRaw("CAST(numero_registro AS TEXT) ILIKE ?", ["%{$buscar}%"]);
            } elseif ($criterio === 'familia') {
                $query->where(function ($q) use ($buscar) {
                    $q->where('numero_expediente_fisico', 'like', "%{$buscar}%")
                      ->orWhereHas('familia', function ($fq) use ($buscar) {
                          $fq->where('numero_familia', 'like', "%{$buscar}%")
                             ->orWhere('apellido_cabeza', 'ilike', "%{$buscar}%");
                      });
                });
            } elseif ($criterio === 'dpi') {
                $query->where('dpi', 'like', "%{$buscar}%");
            } else {
                // 'todos' los campos
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombres', 'ilike', "%{$buscar}%")
                      ->orWhere('apellidos', 'ilike', "%{$buscar}%")
                      ->orWhere('dpi', 'like', "%{$buscar}%")
                      ->orWhere('numero_expediente_fisico', 'like', "%{$buscar}%")
                      ->orWhereRaw("CAST(numero_registro AS TEXT) ILIKE ?", ["%{$buscar}%"])
                      ->orWhereHas('familia', function ($fq) use ($buscar) {
                          $fq->where('numero_familia', 'like', "%{$buscar}%")
                             ->orWhere('apellido_cabeza', 'ilike', "%{$buscar}%");
                      });
                });
            }
        }

        if ($request->filled('numero_registro')) {
            $numReg = trim($request->numero_registro);
            $query->whereRaw("CAST(numero_registro AS TEXT) ILIKE ?", ["%{$numReg}%"]);
        }

        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $pacientes = $query->orderBy('id_paciente', 'desc')->paginate(15)->withQueryString();
        $familias = Familia::where('activo', true)->orderBy('numero_familia')->get();

        return view('pacientes.index', compact('pacientes', 'familias'));
    }

    public function create(Request $request)
    {
        $familias = Familia::where('activo', true)->orderBy('numero_familia')->get();
        $selectedFamilyId = $request->get('id_family');

        return view('pacientes.create', compact('familias', 'selectedFamilyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres'                   => 'required|string|max:100',
            'apellidos'                 => 'required|string|max:100',
            'dpi'                       => 'nullable|string|digits:13',
            'numero_expediente_fisico'  => 'nullable|string|max:50',
            'numero_registro'           => 'nullable|integer',
            'descripcion_registro'      => 'nullable|string|max:150',
            'direccion'                 => 'nullable|string|max:255',
            'fecha_nacimiento'          => 'required|date|before_or_equal:today',
            'sexo'                      => 'required|in:M,F',
            'telefono'                  => 'nullable|string|digits:8',
            'id_family'                 => 'nullable|integer',
            'numero_familia'            => 'nullable|string|max:50',
            'id_comunidad'              => 'nullable',
        ]);

        // Resolver la comunidad si es nueva
        $comunidadId = $request->input('id_comunidad');
        if ($comunidadId === 'OTRO') {
            if (empty($request->input('nueva_comunidad'))) {
                return back()->withInput()->withErrors(['id_comunidad' => 'El nombre de la nueva comunidad es obligatorio.']);
            }
            if (empty($request->input('id_municipio'))) {
                return back()->withInput()->withErrors(['id_comunidad' => 'Debe seleccionar un municipio válido para la nueva comunidad.']);
            }
            $nombreCom = trim($request->input('nueva_comunidad'));
            $comunidad = \App\Models\Comunidad::where('nombre', 'ilike', $nombreCom)
                ->where('id_municipio', $request->input('id_municipio'))
                ->first();
            if (!$comunidad) {
                $comunidad = \App\Models\Comunidad::create([
                    'nombre' => $nombreCom,
                    'id_municipio' => $request->input('id_municipio'),
                    'activo' => true
                ]);
            }
            $comunidadId = $comunidad->id_comunidad;
        }

        // Resolver la familia: usar id_family directo, o buscar/crear por numero_familia
        $familia = null;
        if (!empty($validated['id_family'])) {
            $familia = Familia::findOrFail($validated['id_family']);
        } elseif (!empty($validated['numero_familia'])) {
            $familia = Familia::where('numero_familia', $validated['numero_familia'])->first();
            if (!$familia) {
                // Si la familia no existe, la comunidad es obligatoria
                if (empty($comunidadId)) {
                    return back()->withInput()->withErrors([
                        'id_comunidad' => 'La comunidad es obligatoria para registrar un nuevo núcleo familiar.'
                    ]);
                }

                // Crear familia nueva con el número proporcionado
                $familia = Familia::create([
                    'numero_familia'  => $validated['numero_familia'],
                    'apellido_cabeza' => $validated['apellidos'],
                    'id_comunidad'    => $comunidadId,
                    'activo'          => true,
                    'fecha_registro'  => now(),
                ]);
                Bitacora::registrar(
                    Auth::id(), 'crear', 'familias', $familia->id_family,
                    "Familia #{$familia->numero_familia} creada automáticamente al registrar paciente.",
                    $request->ip()
                );
            }
        }

        if (!$familia) {
            return back()->withInput()->withErrors(['numero_familia' => 'Debe indicar un número de familia válido.']);
        }

        $expInput = $request->input('numero_expediente_fisico');
        $expedienteNumero = !empty($expInput) ? $expInput : $familia->numero_familia;

        // Verificación de duplicidad de DPI si fue ingresado
        if (!empty($validated['dpi'])) {
            $dpiEnPaciente = Paciente::where('dpi', $validated['dpi'])->first();
            $dpiEnFamilia  = Familia::where('dpi', $validated['dpi'])->first();

            if ($dpiEnPaciente || $dpiEnFamilia) {
                AlertaDuplicado::create([
                    'id_usuario'      => Auth::id(),
                    'tipo_duplicado'  => 'dpi',
                    'valor_duplicado' => $validated['dpi'],
                    'accion_tomada'   => 'registro_bloqueado',
                ]);

                $nombreDpi = $dpiEnPaciente 
                    ? ($dpiEnPaciente->nombres . ' ' . $dpiEnPaciente->apellidos . ' (Exp: ' . $dpiEnPaciente->numero_expediente_fisico . ')')
                    : ($dpiEnFamilia->apellido_cabeza . ' (Cabeza de familia #' . $dpiEnFamilia->numero_familia . ')');

                return back()->withInput()->withErrors([
                    'dpi' => 'El DPI ' . $validated['dpi'] . ' ya existe registrado en el sistema (' . $nombreDpi . '). Se generó una alerta de duplicidad.'
                ]);
            }
        }

        // Verificación de duplicidad de Número de Registro si fue ingresado
        if (!empty($validated['numero_registro'])) {
            $regEnPaciente = Paciente::where('numero_registro', $validated['numero_registro'])->first();

            if ($regEnPaciente) {
                AlertaDuplicado::create([
                    'id_usuario'      => Auth::id(),
                    'tipo_duplicado'  => 'numero_registro',
                    'valor_duplicado' => (string) $validated['numero_registro'],
                    'accion_tomada'   => 'registro_bloqueado',
                ]);

                return back()->withInput()->withErrors([
                    'numero_registro' => 'El número de registro ' . $validated['numero_registro'] . ' ya está asignado al paciente ' . $regEnPaciente->nombres . ' ' . $regEnPaciente->apellidos . ' (Expediente: ' . $regEnPaciente->numero_expediente_fisico . ').'
                ]);
            }
        }

        $paciente = Paciente::create([
            'id_family'                => $familia->id_family,
            'nombres'                  => $validated['nombres'],
            'apellidos'                => $validated['apellidos'],
            'dpi'                      => $validated['dpi'] ?? null,
            'numero_expediente_fisico' => $expedienteNumero,
            'numero_registro'          => $validated['numero_registro'] ?? null,
            'descripcion_registro'     => $validated['descripcion_registro'] ?? null,
            'direccion'                => $validated['direccion'] ?? null,
            'fecha_nacimiento'         => $validated['fecha_nacimiento'],
            'sexo'                     => $validated['sexo'],
            'telefono'                 => $validated['telefono'] ?? null,
            'activo'                   => true,
        ]);

        Bitacora::registrar(Auth::id(), 'crear', 'pacientes', $paciente->id_paciente,
            "{$paciente->nombres} {$paciente->apellidos} registrado.", $request->ip());

        // Lógica especial si proviene del modal de Ventanilla
        if ($request->has('desde_ventanilla') && $request->filled('turno_id')) {
            $turnoId = $request->input('turno_id');
            
            $registro = \App\Models\RegistroLlegada::create([
                'id_paciente'   => $paciente->id_paciente,
                'id_turno'      => $turnoId,
                'fecha'         => today(),
                'hora_llegada'  => now()->format('H:i'),
                'es_nuevo'      => true,
                'observaciones' => 'Primer ingreso registrado automáticamente al crear paciente.',
            ]);

            Bitacora::registrar(
                Auth::id(),
                'crear',
                'registros_llegada',
                $registro->id_registro,
                "Llegada de primer ingreso registrada automáticamente: Paciente #{$paciente->id_paciente} en turno #{$turnoId}",
                $request->ip()
            );

            return redirect()->route('ventanilla.index', ['turno_id' => $turnoId])
                ->with('success', 'Paciente guardado y primera llegada registrada correctamente.');
        }

        return redirect()->route('pacientes.show', $paciente->id_paciente)
            ->with('success', 'Paciente registrado exitosamente en la familia #' . $familia->numero_familia);
    }

    public function show($id)
    {
        $paciente = Paciente::with([
            'familia.comunidad.municipio.departamento',
            'registrosLlegada.turno.recepcionista',
            'registrosLlegada.turno.usuario',
        ])->findOrFail($id);
        $familias = Familia::where('activo', true)->orderBy('numero_familia')->get();
        return view('pacientes.show', compact('paciente', 'familias'));
    }

    public function edit($id)
    {
        $paciente = Paciente::findOrFail($id);
        $familias = Familia::where('activo', true)->orderBy('numero_familia')->get();

        return view('pacientes.edit', compact('paciente', 'familias'));
    }

    public function update(Request $request, $id)
    {
        $paciente = Paciente::findOrFail($id);

        $validated = $request->validate([
            'id_family'                 => 'required|exists:familias,id_family',
            'nombres'                   => 'required|string|max:100',
            'apellidos'                 => 'required|string|max:100',
            'dpi'                       => 'nullable|string|digits:13',
            'numero_expediente_fisico'  => 'nullable|string|max:50',
            'numero_registro'           => 'nullable|integer',
            'descripcion_registro'      => 'nullable|string|max:150',
            'direccion'                 => 'nullable|string|max:255',
            'fecha_nacimiento'          => 'required|date|before_or_equal:today',
            'sexo'                      => 'required|in:M,F',
            'telefono'                  => 'nullable|string|digits:8',
        ]);

        $familia = Familia::findOrFail($validated['id_family']);
        $expedienteNumero = !empty($validated['numero_expediente_fisico']) 
            ? $validated['numero_expediente_fisico'] 
            : $familia->numero_familia;

        if (!empty($validated['dpi']) && $validated['dpi'] !== $paciente->dpi) {
            $dpiEnPaciente = Paciente::where('dpi', $validated['dpi'])->where('id_paciente', '!=', $id)->first();
            $dpiEnFamilia  = Familia::where('dpi', $validated['dpi'])->first();

            if ($dpiEnPaciente || $dpiEnFamilia) {
                AlertaDuplicado::create([
                    'id_usuario'      => Auth::id(),
                    'tipo_duplicado'  => 'dpi',
                    'valor_duplicado' => $validated['dpi'],
                    'accion_tomada'   => 'modificacion_bloqueada',
                ]);

                $nombreDpi = $dpiEnPaciente 
                    ? ($dpiEnPaciente->nombres . ' ' . $dpiEnPaciente->apellidos . ' (Exp: ' . $dpiEnPaciente->numero_expediente_fisico . ')')
                    : ($dpiEnFamilia->apellido_cabeza . ' (Cabeza de familia #' . $dpiEnFamilia->numero_familia . ')');

                return back()->withInput()->withErrors([
                    'dpi' => 'El DPI ' . $validated['dpi'] . ' ya pertenece a otro registro en el sistema (' . $nombreDpi . ').'
                ]);
            }
        }

        if (!empty($validated['numero_registro']) && $validated['numero_registro'] != $paciente->numero_registro) {
            $regEnPaciente = Paciente::where('numero_registro', $validated['numero_registro'])
                ->where('id_paciente', '!=', $id)
                ->first();

            if ($regEnPaciente) {
                AlertaDuplicado::create([
                    'id_usuario'      => Auth::id(),
                    'tipo_duplicado'  => 'numero_registro',
                    'valor_duplicado' => (string) $validated['numero_registro'],
                    'accion_tomada'   => 'modificacion_bloqueada',
                ]);

                return back()->withInput()->withErrors([
                    'numero_registro' => 'El número de registro ' . $validated['numero_registro'] . ' ya pertenece a otro paciente: ' . $regEnPaciente->nombres . ' ' . $regEnPaciente->apellidos . ' (Expediente: ' . $regEnPaciente->numero_expediente_fisico . ').'
                ]);
            }
        }

        $paciente->update([
            'id_family'                => $familia->id_family,
            'nombres'                  => $validated['nombres'],
            'apellidos'                => $validated['apellidos'],
            'dpi'                      => $validated['dpi'] ?? null,
            'numero_expediente_fisico' => $expedienteNumero,
            'numero_registro'          => $validated['numero_registro'] ?? null,
            'descripcion_registro'     => $validated['descripcion_registro'] ?? null,
            'direccion'                => $validated['direccion'] ?? null,
            'fecha_nacimiento'         => $validated['fecha_nacimiento'],
            'sexo'                     => $validated['sexo'],
            'telefono'                 => $validated['telefono'] ?? null,
        ]);

        Bitacora::registrar(Auth::id(), 'editar', 'pacientes', $paciente->id_paciente,
            "{$paciente->nombres} {$paciente->apellidos} actualizado.", $request->ip());

        return redirect()->route('pacientes.show', $paciente->id_paciente)
            ->with('success', 'Paciente actualizado exitosamente.');
    }

    public function toggleStatus($id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->activo = !$paciente->activo;
        $paciente->save();

        $estadoStr = $paciente->activo ? 'activado' : 'desactivado';
        Bitacora::registrar(Auth::id(), 'editar', 'pacientes', $paciente->id_paciente,
            "Paciente {$paciente->nombres} {$paciente->apellidos} {$estadoStr}.", request()->ip());

        return back()->with('success', "El paciente fue {$estadoStr} correctamente.");
    }

    public function checkDuplicate(Request $request): JsonResponse
    {
        $tipo = $request->query('tipo');
        $valor = trim($request->query('valor'));
        $ignoreId = $request->query('ignore_id');

        if (!$tipo || $valor === '') {
            return response()->json(['duplicate' => false]);
        }

        $isDuplicate = false;
        $message = '';

        if ($tipo === 'dpi') {
            $pacienteQ = Paciente::where('dpi', $valor);
            if ($ignoreId) {
                $pacienteQ->where('id_paciente', '!=', $ignoreId);
            }
            $pExistente = $pacienteQ->first();
            if ($pExistente) {
                $isDuplicate = true;
                $message = 'El DPI ya pertenece a ' . $pExistente->nombres . ' ' . $pExistente->apellidos . ' (Exp: ' . $pExistente->numero_expediente_fisico . ').';
            } else {
                $famQ = Familia::where('dpi', $valor);
                $fExistente = $famQ->first();
                if ($fExistente) {
                    $isDuplicate = true;
                    $message = 'El DPI pertenece al cabeza de familia ' . $fExistente->apellido_cabeza . ' (Fam. #' . $fExistente->numero_familia . ').';
                }
            }
        } elseif ($tipo === 'numero_familia') {
            $famQ = Familia::where('numero_familia', $valor);
            if ($ignoreId) {
                $famQ->where('id_family', '!=', $ignoreId);
            }
            if ($famQ->exists()) {
                $isDuplicate = true;
                $message = 'El número de familia ya está registrado.';
            }
        } elseif ($tipo === 'numero_registro') {
            $pacienteQ = Paciente::where('numero_registro', $valor);
            if ($ignoreId) {
                $pacienteQ->where('id_paciente', '!=', $ignoreId);
            }
            $pExistente = $pacienteQ->first();
            if ($pExistente) {
                $isDuplicate = true;
                $message = 'El No. de registro ya pertenece a ' . $pExistente->nombres . ' ' . $pExistente->apellidos . ' (Exp: ' . $pExistente->numero_expediente_fisico . ').';
            }
        }

        return response()->json([
            'duplicate' => $isDuplicate,
            'message'   => $message
        ]);
    }

    // ── Escaneo de Cuaderno de Pacientes ──────────────────────────

    /**
     * Procesar foto de página de cuaderno de pacientes.
     */
    public function escanearCuaderno(Request $request, GeminiOcrService $ocrService): JsonResponse
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:15360',
        ], [
            'imagen.required' => 'Debes adjuntar o tomar una foto de la página del cuaderno.',
            'imagen.image'    => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.max'      => 'La imagen no debe superar los 15 MB.',
        ]);

        try {
            $datos = $ocrService->escanearCuadernoPacientes($request->file('imagen'));

            $familias    = Familia::with('comunidad')->where('activo', true)->orderBy('numero_familia')->get();
            $comunidades = Comunidad::orderBy('nombre')->get();

            $pacientesMapeados = collect($datos['pacientes'] ?? [])->map(function ($p) use ($familias, $comunidades) {
                $nombres   = trim($p['nombres'] ?? '');
                $apellidos = trim($p['apellidos'] ?? '');
                $dpi       = !empty($p['dpi']) ? preg_replace('/\D/', '', $p['dpi']) : null;
                $direccion = trim($p['direccion'] ?? '');

                // Verificar si DPI ya existe en el sistema
                $dpiExistente = false;
                $duplicadoDpiMsg = null;
                if (!empty($dpi) && strlen($dpi) === 13) {
                    $pDpi = Paciente::where('dpi', $dpi)->first();
                    if ($pDpi) {
                        $dpiExistente = true;
                        $duplicadoDpiMsg = "DPI ya registrado en {$pDpi->nombres} {$pDpi->apellidos} (Exp: {$pDpi->numero_expediente_fisico})";
                    }
                }

                // Verificar si ya existe paciente con nombre y apellidos similares
                $posibleDuplicado = false;
                $duplicadoNombreMsg = null;
                if (!empty($nombres) && !empty($apellidos)) {
                    $pSim = Paciente::where('nombres', 'ilike', "%{$nombres}%")
                        ->where('apellidos', 'ilike', "%{$apellidos}%")
                        ->first();
                    if ($pSim) {
                        $posibleDuplicado = true;
                        $duplicadoNombreMsg = "Coincide con {$pSim->nombres} {$pSim->apellidos} (Exp: {$pSim->numero_expediente_fisico})";
                    }
                }

                // Sugerencia de comunidad según dirección
                $comunidadSugeridaId = null;
                if (!empty($direccion)) {
                    $normDir = mb_strtolower($direccion, 'UTF-8');
                    $cMatch = $comunidades->first(function ($c) use ($normDir) {
                        return str_contains($normDir, mb_strtolower($c->nombre, 'UTF-8'));
                    });
                    $comunidadSugeridaId = $cMatch?->id_comunidad;
                }

                // Sugerencia de familia según primer apellido
                $familiaSugeridaId = null;
                if (!empty($apellidos)) {
                    $primerAp = explode(' ', $apellidos)[0];
                    $fMatch = $familias->first(function ($f) use ($primerAp) {
                        return str_contains(mb_strtolower($f->apellido_cabeza, 'UTF-8'), mb_strtolower($primerAp, 'UTF-8'));
                    });
                    $familiaSugeridaId = $fMatch?->id_family;
                }

                return [
                    'nombres'                   => $nombres,
                    'apellidos'                 => $apellidos,
                    'edad_texto'                => $p['edad_texto'] ?? '',
                    'fecha_nacimiento'          => $p['fecha_nacimiento_estimada'] ?? '2000-01-01',
                    'sexo'                      => in_array(strtoupper($p['sexo'] ?? ''), ['M', 'F']) ? strtoupper($p['sexo']) : 'M',
                    'dpi'                       => $dpi,
                    'direccion'                 => $direccion,
                    'numero_expediente'         => $p['numero_expediente'] ?? null,
                    'dpi_existente'             => $dpiExistente,
                    'dpi_mensaje'               => $duplicadoDpiMsg,
                    'posible_duplicado'         => $posibleDuplicado,
                    'duplicado_mensaje'         => $duplicadoNombreMsg,
                    'id_comunidad_sugerida'     => $comunidadSugeridaId,
                    'id_family_sugerida'        => $familiaSugeridaId,
                ];
            });

            return response()->json([
                'success'     => true,
                'titulo'      => $datos['titulo'] ?? 'Pacientes del Cuaderno',
                'pacientes'   => $pacientesMapeados,
                'familias'    => $familias,
                'comunidades' => $comunidades,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Importar pacientes extraídos en lote.
     */
    public function importarLote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pacientes'                      => 'required|array|min:1',
            'pacientes.*.nombres'            => 'required|string|max:100',
            'pacientes.*.apellidos'          => 'required|string|max:100',
            'pacientes.*.fecha_nacimiento'   => 'required|date|before_or_equal:today',
            'pacientes.*.sexo'               => 'required|in:M,F',
            'pacientes.*.dpi'                => 'nullable|string|digits:13',
            'pacientes.*.direccion'          => 'nullable|string|max:255',
            'pacientes.*.id_family'          => 'nullable',
            'pacientes.*.id_comunidad'       => 'nullable',
            'pacientes.*.numero_expediente'  => 'nullable|string|max:50',
            'pacientes.*.parentesco'         => 'nullable|string|max:50',
        ]);

        $guardados = 0;
        $omitidos  = 0;

        DB::transaction(function () use ($validated, &$guardados, &$omitidos) {
            foreach ($validated['pacientes'] as $p) {
                $dpi = !empty($p['dpi']) ? trim($p['dpi']) : null;

                // Omitir si el DPI ya está registrado para no corromper la unicidad
                if ($dpi && Paciente::where('dpi', $dpi)->exists()) {
                    $omitidos++;
                    continue;
                }

                $idFamily = !empty($p['id_family']) && $p['id_family'] !== '__nuevo__' 
                    ? (int) $p['id_family'] 
                    : null;

                $comunidadId = !empty($p['id_comunidad']) 
                    ? (int) $p['id_comunidad'] 
                    : Comunidad::first()?->id_comunidad;

                // Si no tiene familia asignada, crear nueva familia automáticamente
                if (!$idFamily) {
                    $maxId = (Familia::max('id_family') ?? 0) + 1;
                    $numFam = 'F-' . str_pad($maxId, 4, '0', STR_PAD_LEFT);
                    
                    while (Familia::where('numero_familia', $numFam)->exists()) {
                        $maxId++;
                        $numFam = 'F-' . str_pad($maxId, 4, '0', STR_PAD_LEFT);
                    }

                    $familia = Familia::create([
                        'numero_familia'  => $numFam,
                        'apellido_cabeza' => trim($p['apellidos']),
                        'id_comunidad'    => $comunidadId,
                        'activo'          => true,
                        'fecha_registro'  => now(),
                    ]);
                    $idFamily = $familia->id_family;
                    $expediente = !empty($p['numero_expediente']) ? trim($p['numero_expediente']) : $numFam;
                } else {
                    $fam = Familia::find($idFamily);
                    $expediente = !empty($p['numero_expediente']) 
                        ? trim($p['numero_expediente']) 
                        : ($fam?->numero_familia ?? 'EXP-' . rand(1000, 9999));
                }

                $paciente = Paciente::create([
                    'id_family'                => $idFamily,
                    'nombres'                  => trim($p['nombres']),
                    'apellidos'                => trim($p['apellidos']),
                    'dpi'                      => $dpi,
                    'numero_expediente_fisico' => $expediente,
                    'direccion'                => !empty($p['direccion']) ? trim($p['direccion']) : null,
                    'fecha_nacimiento'         => $p['fecha_nacimiento'],
                    'sexo'                     => $p['sexo'],
                    'parentesco_familia'       => $p['parentesco'] ?? 'Miembro',
                    'activo'                   => true,
                    'fecha_registro'           => now(),
                ]);

                Bitacora::registrar(
                    Auth::id(),
                    'crear',
                    'pacientes',
                    $paciente->id_paciente,
                    "Paciente {$paciente->nombres} {$paciente->apellidos} importado desde escaneo de cuaderno.",
                    request()->ip()
                );

                $guardados++;
            }
        });

        $mensaje = "¡Se registraron exitosamente {$guardados} pacientes!";
        if ($omitidos > 0) {
            $mensaje .= " ({$omitidos} registros omitidos porque su DPI ya existía).";
        }

        return response()->json([
            'success'   => true,
            'mensaje'   => $mensaje,
            'guardados' => $guardados,
            'omitidos'  => $omitidos,
            'url'       => route('pacientes.index'),
        ]);
    }
}
