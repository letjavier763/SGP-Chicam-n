<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Comunidad;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Obtener los municipios de un departamento dado.
     */
    public function getMunicipios(int $departamentoId): JsonResponse
    {
        $municipios = Municipio::where('id_departamento', $departamentoId)
            ->orderBy('nombre')
            ->get(['id_municipio', 'nombre']);

        return response()->json($municipios);
    }

    /**
     * Obtener las comunidades de un municipio dado.
     */
    public function getComunidades(int $municipioId): JsonResponse
    {
        $comunidades = Comunidad::where('id_municipio', $municipioId)
            ->orderBy('nombre')
            ->get(['id_comunidad', 'nombre', 'zona']);

        return response()->json($comunidades);
    }
}
