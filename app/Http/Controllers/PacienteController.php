<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Familia;
use App\Models\AlertaDuplicado;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Paciente::with(['familia.comunidad']);

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function ($q) use ($buscar) {
                $q->where('nombres', 'ilike', "%{$buscar}%")
                  ->orWhere('apellidos', 'ilike', "%{$buscar}%")
                  ->orWhere('dpi', 'like', "%{$buscar}%")
                  ->orWhere('numero_expediente_fisico', 'like', "%{$buscar}%")
                  ->orWhereHas('familia', function ($fq) use ($buscar) {
                      $fq->where('numero_familia', 'like', "%{$buscar}%")
                         ->orWhere('apellido_cabeza', 'ilike', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $pacientes = $query->orderBy('id_paciente', 'desc')->paginate(15)->withQueryString();

        return view('pacientes.index', compact('pacientes'));
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
            'id_family'                 => 'required|exists:familias,id_family',
            'nombres'                   => 'required|string|max:100',
            'apellidos'                 => 'required|string|max:100',
            'dpi'                       => 'nullable|string|digits:13',
            'numero_expediente_fisico'  => 'nullable|string|max:50',
            'fecha_nacimiento'          => 'required|date|before_or_equal:today',
            'sexo'                      => 'required|in:M,F',
            'telefono'                  => 'nullable|string|digits:8',
        ]);

        $familia = Familia::findOrFail($validated['id_family']);
        $expInput = $request->input('numero_expediente_fisico');
        $expedienteNumero = !empty($expInput) ? $expInput : $familia->numero_familia;

        // Verificación de duplicidad de DPI si fue ingresado
        if (!empty($validated['dpi'])) {
            $dpiEnPaciente = Paciente::where('dpi', $validated['dpi'])->exists();
            $dpiEnFamilia  = Familia::where('dpi', $validated['dpi'])->exists();

            if ($dpiEnPaciente || $dpiEnFamilia) {
                AlertaDuplicado::create([
                    'id_usuario'      => Auth::id(),
                    'tipo_duplicado'  => 'dpi',
                    'valor_duplicado' => $validated['dpi'],
                    'accion_tomada'   => 'registro_bloqueado',
                ]);

                return back()->withInput()->withErrors([
                    'dpi' => 'El DPI ' . $validated['dpi'] . ' ya existe registrado en el sistema. Se generó una alerta de duplicidad.'
                ]);
            }
        }

        $paciente = Paciente::create([
            'id_family'                => $familia->id_family,
            'nombres'                  => $validated['nombres'],
            'apellidos'                => $validated['apellidos'],
            'dpi'                      => $validated['dpi'] ?? null,
            'numero_expediente_fisico' => $expedienteNumero,
            'fecha_nacimiento'         => $validated['fecha_nacimiento'],
            'sexo'                     => $validated['sexo'],
            'telefono'                 => $validated['telefono'] ?? null,
            'activo'                   => true,
        ]);

        Bitacora::registrar(Auth::id(), 'crear', 'pacientes', $paciente->id_paciente,
            "{$paciente->nombres} {$paciente->apellidos} registrado.", $request->ip());

        return redirect()->route('pacientes.show', $paciente->id_paciente)
            ->with('success', 'Paciente registrado exitosamente en la familia #' . $familia->numero_familia);
    }

    public function show($id)
    {
        $paciente = Paciente::with(['familia.comunidad.municipio.departamento', 'registrosLlegada'])->findOrFail($id);
        return view('pacientes.show', compact('paciente'));
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
            'fecha_nacimiento'          => 'required|date|before_or_equal:today',
            'sexo'                      => 'required|in:M,F',
            'telefono'                  => 'nullable|string|digits:8',
        ]);

        $familia = Familia::findOrFail($validated['id_family']);
        $expedienteNumero = !empty($validated['numero_expediente_fisico']) 
            ? $validated['numero_expediente_fisico'] 
            : $familia->numero_familia;

        if (!empty($validated['dpi']) && $validated['dpi'] !== $paciente->dpi) {
            $dpiEnPaciente = Paciente::where('dpi', $validated['dpi'])->where('id_paciente', '!=', $id)->exists();
            $dpiEnFamilia  = Familia::where('dpi', $validated['dpi'])->exists();

            if ($dpiEnPaciente || $dpiEnFamilia) {
                AlertaDuplicado::create([
                    'id_usuario'      => Auth::id(),
                    'tipo_duplicado'  => 'dpi',
                    'valor_duplicado' => $validated['dpi'],
                    'accion_tomada'   => 'modificacion_bloqueada',
                ]);

                return back()->withInput()->withErrors([
                    'dpi' => 'El DPI ' . $validated['dpi'] . ' ya pertenece a otro registro en el sistema.'
                ]);
            }
        }

        $paciente->update([
            'id_family'                => $familia->id_family,
            'nombres'                  => $validated['nombres'],
            'apellidos'                => $validated['apellidos'],
            'dpi'                      => $validated['dpi'] ?? null,
            'numero_expediente_fisico' => $expedienteNumero,
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

        if (!$tipo || !$valor) {
            return response()->json(['duplicate' => false]);
        }

        $isDuplicate = false;
        $message = '';

        if ($tipo === 'dpi') {
            $pacienteQ = Paciente::where('dpi', $valor);
            if ($ignoreId) {
                $pacienteQ->where('id_paciente', '!=', $ignoreId);
            }
            if ($pacienteQ->exists()) {
                $isDuplicate = true;
                $message = 'El DPI ya pertenece a un paciente registrado.';
            } else {
                $famQ = Familia::where('dpi', $valor);
                if ($famQ->exists()) {
                    $isDuplicate = true;
                    $message = 'El DPI ya pertenece al cabeza de un núcleo familiar registrado.';
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
        }

        return response()->json([
            'duplicate' => $isDuplicate,
            'message'   => $message
        ]);
    }
}
