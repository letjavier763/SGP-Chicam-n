<?php

namespace App\Http\Controllers;

use App\Models\Familia;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Comunidad;
use App\Models\Paciente;
use App\Models\AlertaDuplicado;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FamiliaController extends Controller
{
    public function index(Request $request)
    {
        $query = Familia::with(['comunidad.municipio.departamento', 'pacientes']);

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_familia', 'like', "%{$buscar}%")
                  ->orWhere('apellido_cabeza', 'ilike', "%{$buscar}%")
                  ->orWhere('dpi', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('id_comunidad')) {
            $query->where('id_comunidad', $request->id_comunidad);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $familias = $query->orderBy('id_family', 'desc')->paginate(15)->withQueryString();
        $comunidades = Comunidad::orderBy('nombre')->get();
        $departamentos = Departamento::orderBy('nombre')->get();

        return view('familias.index', compact('familias', 'comunidades', 'departamentos'));
    }

    public function create()
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        return view('familias.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_comunidad'     => 'required|exists:comunidades,id_comunidad',
            'numero_familia'   => 'required|string|max:20',
            'apellido_cabeza'  => 'required|string|max:100',
            'dpi'              => 'nullable|string|digits:13',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        // Verificación de duplicado numero_familia
        $famExistente = Familia::where('numero_familia', $validated['numero_familia'])->first();
        if ($famExistente) {
            AlertaDuplicado::create([
                'id_usuario'      => Auth::id(),
                'tipo_duplicado'  => 'numero_familia',
                'valor_duplicado' => $validated['numero_familia'],
                'accion_tomada'   => 'registro_bloqueado',
            ]);

            return back()->withInput()->withErrors([
                'numero_familia' => 'El número de familia ya está registrado en el sistema. Se ha generado una alerta de duplicidad.'
            ]);
        }

        // Verificación de duplicado DPI en familias o pacientes (si fue ingresado)
        if (!empty($validated['dpi'])) {
            $dpiEnFamilia = Familia::where('dpi', $validated['dpi'])->exists();
            $dpiEnPaciente = Paciente::where('dpi', $validated['dpi'])->exists();

            if ($dpiEnFamilia || $dpiEnPaciente) {
                AlertaDuplicado::create([
                    'id_usuario'      => Auth::id(),
                    'tipo_duplicado'  => 'dpi',
                    'valor_duplicado' => $validated['dpi'],
                    'accion_tomada'   => 'registro_bloqueado',
                ]);

                return back()->withInput()->withErrors([
                    'dpi' => 'El DPI ya existe registrado en el sistema (en familia o paciente). Se ha registrado la alerta de duplicidad.'
                ]);
            }
        }

        $familia = Familia::create([
            'id_comunidad'     => $validated['id_comunidad'],
            'numero_familia'   => $validated['numero_familia'],
            'apellido_cabeza'  => $validated['apellido_cabeza'],
            'dpi'              => $validated['dpi'] ?? null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
            'activo'           => true,
        ]);

        Bitacora::registrar(Auth::id(), 'crear', 'familias', $familia->id_family,
            "Familia #{$familia->numero_familia} ({$familia->apellido_cabeza}) creada.", $request->ip());

        return redirect()->route('familias.show', $familia->id_family)
            ->with('success', 'Núcleo familiar registrado exitosamente.');
    }

    public function show($id)
    {
        $familia = Familia::with(['comunidad.municipio.departamento', 'pacientes'])->findOrFail($id);
        return view('familias.show', compact('familia'));
    }

    public function edit($id)
    {
        $familia = Familia::with(['comunidad.municipio.departamento'])->findOrFail($id);
        $departamentos = Departamento::orderBy('nombre')->get();
        
        $selectedComunidad = $familia->comunidad;
        $selectedMunicipio = $selectedComunidad ? $selectedComunidad->municipio : null;
        $selectedDepartamento = $selectedMunicipio ? $selectedMunicipio->departamento : null;

        $municipios = $selectedDepartamento ? Municipio::where('id_departamento', $selectedDepartamento->id_departamento)->get() : collect();
        $comunidades = $selectedMunicipio ? Comunidad::where('id_municipio', $selectedMunicipio->id_municipio)->get() : collect();

        return view('familias.edit', compact(
            'familia', 'departamentos', 'municipios', 'comunidades',
            'selectedDepartamento', 'selectedMunicipio', 'selectedComunidad'
        ));
    }

    public function update(Request $request, $id)
    {
        $familia = Familia::findOrFail($id);

        $validated = $request->validate([
            'id_comunidad'     => 'required|exists:comunidades,id_comunidad',
            'numero_familia'   => 'required|string|max:20|unique:familias,numero_familia,' . $id . ',id_family',
            'apellido_cabeza'  => 'required|string|max:100',
            'dpi'              => 'nullable|string|digits:13',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        if (!empty($validated['dpi']) && $validated['dpi'] !== $familia->dpi) {
            $dpiEnFamilia = Familia::where('dpi', $validated['dpi'])->where('id_family', '!=', $id)->exists();
            $dpiEnPaciente = Paciente::where('dpi', $validated['dpi'])->exists();

            if ($dpiEnFamilia || $dpiEnPaciente) {
                AlertaDuplicado::create([
                    'id_usuario'      => Auth::id(),
                    'tipo_duplicado'  => 'dpi',
                    'valor_duplicado' => $validated['dpi'],
                    'accion_tomada'   => 'modificacion_bloqueada',
                ]);

                return back()->withInput()->withErrors([
                    'dpi' => 'El DPI ingresado ya pertenece a otro registro en el sistema.'
                ]);
            }
        }

        $familia->update([
            'id_comunidad'     => $validated['id_comunidad'],
            'numero_familia'   => $validated['numero_familia'],
            'apellido_cabeza'  => $validated['apellido_cabeza'],
            'dpi'              => $validated['dpi'] ?? null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
        ]);

        Bitacora::registrar(Auth::id(), 'editar', 'familias', $familia->id_family,
            "Familia #{$familia->numero_familia} actualizada.", $request->ip());

        return redirect()->route('familias.show', $familia->id_family)
            ->with('success', 'Familia actualizada exitosamente.');
    }

    public function toggleStatus($id)
    {
        $familia = Familia::findOrFail($id);
        $familia->activo = !$familia->activo;
        $familia->save();

        $estadoStr = $familia->activo ? 'activada' : 'desactivada';
        Bitacora::registrar(Auth::id(), 'editar', 'familias', $familia->id_family,
            "Familia #{$familia->numero_familia} {$estadoStr}.", request()->ip());

        return back()->with('success', "La familia fue {$estadoStr} correctamente.");
    }

    /**
     * Búsqueda AJAX de familias por número (para autocompletado en modales).
     * Devuelve JSON con coincidencias y si el número existe o no.
     */
    public function buscarAjax(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 1) {
            return response()->json(['existe' => false, 'coincidencias' => []]);
        }

        $familias = Familia::with('comunidad')
            ->where('numero_familia', 'ilike', "%{$q}%")
            ->orWhere('apellido_cabeza', 'ilike', "%{$q}%")
            ->limit(8)
            ->get();

        // Verificar si hay una coincidencia exacta
        $exacta = Familia::where('numero_familia', $q)->first();

        return response()->json([
            'existe'        => (bool) $exacta,
            'id_family'     => $exacta?->id_family,
            'coincidencias' => $familias->map(fn($f) => [
                'id_family'      => $f->id_family,
                'numero_familia' => $f->numero_familia,
                'apellido_cabeza'=> $f->apellido_cabeza,
                'comunidad'      => optional($f->comunidad)->nombre,
                'exacta'         => $f->numero_familia === $q,
            ]),
        ]);
    }
}
