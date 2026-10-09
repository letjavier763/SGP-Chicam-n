<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiOcrService
{
    /**
     * Escanear una imagen de rol de turnos usando la API de Gemini.
     *
     * @param UploadedFile|string $imageFile
     * @return array
     * @throws Exception
     */
    public function escanearRolTurnos($imageFile): array
    {
        $prompt = <<<PROMPT
Eres un asistente experto en digitalización de documentos administrativos y de salud.
Analiza la imagen adjunta, que corresponde a un "ROL PARA VENTANILLA" o "ROL DE TURNOS".

Instrucciones estrictas:
1. Extrae el título, mes y año indicados en el encabezado del documento (ejemplo: "ROL PARA VENTANILLA ABRIL 2026" -> mes: 4, anio: 2026).
2. Extrae todas las filas de la tabla donde haya una persona asignada a una fecha.
3. Para cada turno detectado incluye:
   - "nombre": El nombre tal cual aparece en la tabla en mayúsculas (ej: DANY, MARLENY, SULY, IDALIA, YULBIN, DALY, YÁNELI, LUDWIN, YAZMI, BRAYAN). Presta suma atención a no confundir nombres similares como DANY y DALY.
   - "dia": El día de la semana (LUNES, MARTES, MIÉRCOLES, JUEVES, VIERNES, SÁBADO, DOMINGO).
   - "fecha": La fecha convertida a formato ISO 'YYYY-MM-DD' (ejemplo: si en la tabla dice 06/04/2026, devuelve "2026-04-06").
4. Ignora firmas manuscritas, sellos, sellos de dirección, chinchetas y cualquier elemento fuera de la tabla de turnos.
5. Devuelve la respuesta en formato JSON con la siguiente estructura exacta:
{
  "titulo": "ROL PARA VENTANILLA ABRIL 2026",
  "mes": 4,
  "anio": 2026,
  "turnos": [
    {
      "nombre": "DANY",
      "dia": "LUNES",
      "fecha": "2026-04-06"
    }
  ]
}
PROMPT;

        $parsed = $this->ejecutarConsultaVision($prompt, $imageFile);

        if (!is_array($parsed) || !isset($parsed['turnos']) || !is_array($parsed['turnos'])) {
            throw new Exception('No se pudo estructurar la tabla de turnos del documento. Asegúrate de que la imagen sea legible.');
        }

        return $parsed;
    }

    /**
     * Escanear una página de cuaderno manuscrita con registros de pacientes.
     *
     * @param UploadedFile|string $imageFile
     * @return array
     * @throws Exception
     */
    public function escanearCuadernoPacientes($imageFile): array
    {
        $prompt = <<<PROMPT
Eres un asistente médico experto en transcripción de cuadernos clínicos y censos de salud de Guatemala.
Analiza la imagen adjunta, que corresponde a una página manuscrita (escrita a mano) de un CUADERNO DE REGISTRO DE PACIENTES o LIBRO DE CONSULTAS.

Instrucciones estrictas:
1. Extrae cada una de las filas correspondientes a personas/pacientes anotadas en el cuaderno.
2. Descifra la letra manuscrita con atención y extrae:
   - "nombres": Nombres de pila del paciente en mayúsculas (ej: "JUAN CARLOS", "MARÍA ELENA"). Si están juntos con los apellidos, sepáralos lo más coherentemente posible.
   - "apellidos": Apellidos en mayúsculas (ej: "PÉREZ GÓMEZ", "LÓPEZ CASTRO").
   - "edad_texto": Texto original sobre su edad tal cual está escrito (ej: "28 a", "35 años", "8 meses", "12/03/1990").
   - "fecha_nacimiento_estimada": Fecha de nacimiento en formato 'YYYY-MM-DD'.
     * Si viene fecha exacta, conviértela a ISO 'YYYY-MM-DD'.
     * Si sólo indica edad en años (ej: 28 años), calcula el año restando al año actual 2026 (ej: 2026 - 28 = 1998 -> "1998-01-01").
     * Si indica meses (ej: 6 meses), calcula los meses hacia atrás respecto al año 2026.
     * Si no se indica edad o nacimiento, devuelve "2000-01-01".
   - "sexo": "M" para masculino o "F" para femenino. Infiérelo del contexto o del nombre si no está explícito.
   - "dpi": Cadena numérica de 13 dígitos del DPI/CUI si está anotado. Si no hay o es menor de edad, devuelve null.
   - "direccion": Comunidad, caserío, aldea o dirección anotada (ej: "Chicamán", "Pueblo Nuevo", "Chixoy"). Si no hay, null.
   - "numero_expediente": Número de expediente, folio o registro físico si está escrito. Si no hay, null.
3. Ignora encabezados de página, tachones descartados y notas de margen que no correspondan a pacientes.
4. Devuelve la respuesta en formato JSON estricto con la siguiente estructura:
{
  "titulo": "Registro de Cuaderno de Pacientes",
  "pacientes": [
    {
      "nombres": "JUAN CARLOS",
      "apellidos": "LÓPEZ PÉREZ",
      "edad_texto": "28 años",
      "fecha_nacimiento_estimada": "1998-01-01",
      "sexo": "M",
      "dpi": null,
      "direccion": "Chicamán",
      "numero_expediente": null
    }
  ]
}
PROMPT;

        $parsed = $this->ejecutarConsultaVision($prompt, $imageFile);

        if (!is_array($parsed) || !isset($parsed['pacientes']) || !is_array($parsed['pacientes'])) {
            throw new Exception('No se pudo estructurar la lista de pacientes del cuaderno. Asegúrate de que la foto esté bien iluminada y enfocada.');
        }

        return $parsed;
    }

    /**
     * Motor común de consulta a la API de Visión de Gemini con fallback automático.
     *
     * @param string $prompt
     * @param UploadedFile|string $imageFile
     * @return array
     * @throws Exception
     */
    private function ejecutarConsultaVision(string $prompt, $imageFile): array
    {
        $apiKey = config('services.gemini.api_key');
        if (empty($apiKey)) {
            throw new Exception('No se ha configurado GEMINI_API_KEY en las variables de entorno (.env). Puedes obtener una clave gratuita en https://aistudio.google.com/');
        }

        // Obtener bytes y mime type
        if ($imageFile instanceof UploadedFile) {
            $imageBytes = file_get_contents($imageFile->getRealPath());
            $mimeType   = $imageFile->getMimeType() ?: 'image/jpeg';
        } elseif (is_string($imageFile) && file_exists($imageFile)) {
            $imageBytes = file_get_contents($imageFile);
            $mimeType   = mime_content_type($imageFile) ?: 'image/jpeg';
        } else {
            throw new Exception('El archivo de imagen proporcionado no es válido.');
        }

        $primaryModel = config('services.gemini.model', 'gemini-3.8-flash');
        $candidateModels = array_unique([
            $primaryModel,
            'gemini-3.8-flash',
            'gemini-3.7-flash',
            'gemini-3.6-flash',
            'gemini-2.5-flash',
        ]);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data'      => base64_encode($imageBytes),
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
                'temperature'        => 0.1,
            ],
        ];

        $lastError = 'No se pudo procesar la solicitud.';
        $response  = null;

        foreach ($candidateModels as $model) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            // Intentar hasta 2 veces por modelo en caso de saturación momentánea
            for ($intento = 1; $intento <= 2; $intento++) {
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->timeout(45)->post($endpoint, $payload);
                } catch (Exception $e) {
                    Log::error("Error de conexión con la API de procesamiento [{$model}]: " . $e->getMessage());
                    $lastError = 'No se pudo conectar con el servicio de procesamiento. Verifica la conexión del servidor.';
                    break;
                }

                if ($response->successful()) {
                    break 2; // Éxito
                }

                $errorJson    = $response->json();
                $errorMessage = $errorJson['error']['message'] ?? ('HTTP ' . $response->status());
                $lastError    = $errorMessage;
                Log::warning("Gemini API error [{$model}] intento {$intento}: {$errorMessage}");

                if ($response->status() === 400 && str_contains($errorMessage, 'API key')) {
                    throw new Exception('La clave GEMINI_API_KEY configurada es inválida.');
                }

                if ($response->status() === 503 || $response->status() === 429 || str_contains($errorMessage, 'high demand')) {
                    sleep(1);
                    continue;
                }

                if (str_contains($errorMessage, 'not found') || str_contains($errorMessage, 'no longer available')) {
                    break;
                }
            }
        }

        if (!$response || $response->failed()) {
            throw new Exception("Error devuelto por el servicio de procesamiento: {$lastError}");
        }

        $resultJson = $response->json();
        $rawText = $resultJson['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Limpiar bloques markdown si vinieran
        $rawText = trim($rawText);
        $rawText = preg_replace('/^```(?:json)?\s*/i', '', $rawText);
        $rawText = preg_replace('/\s*```$/', '', $rawText);

        $parsed = json_decode($rawText, true);

        if (!is_array($parsed)) {
            Log::error('Respuesta JSON no válida de Gemini: ' . $rawText);
            throw new Exception('No se pudo interpretar el resultado de la imagen.');
        }

        return $parsed;
    }
}
