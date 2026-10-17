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

FORMATO TÍPICO DE CADA LÍNEA:
Cada fila generalmente sigue este patrón (aunque puede variar según el cuaderno):
  [Número de expediente]  [Apellido(s) Nombre(s)]  —  [Dirección / Comunidad]  [FN: DD/MM/AA | F/N: DD-MM-AAAA | fecha completa]

Ejemplos reales que puedes encontrar en este tipo de cuadernos guatemaltecos:
  "11,485  Guillema Casilda  — Chicamán  22-06-94"
  "11,486  Angelina Caal  — Chiquimula  FN: 10-03-1985"
  "11,487  Margarita Lux  — Pueblo Nuevo  F/N: 05-09-03"
  "13,493  Odilia López Cec —  Annas  FN 13.09.87"
  "13,494  María Cristina Xna Caal — Beleján 16  FN: 06-08-85"
  "17,535  Tostfina Maldonado Roa — Las Flores  FN: 15-04-1975"
  "17,536  Juana De León — Chacrual  FN: 13-07-1954"
  "13,541  Sara Jiménez — Monte Saó  FN:07-01-03"
  "11,449  Faustina Arreaga — Mixqui  F/N: 04-02-1963"
  "17,449  Tami Maquín — Con Sebastián Beirjú  fam. Chic"
  "14,540  Ambrosio Caal — La Conquista  FN: 12-05-1968"
  "11,503  Fanny Tzoc — El Soch  FN: 03-11-1989"

INSTRUCCIONES ESTRICTAS:
1. Extrae CADA fila que corresponda a una persona o paciente. Extrae TODAS las filas visibles, sin omitir ninguna.
2. Para cada registro, interpreta y extrae los siguientes campos:
   - "nombres": Nombre(s) de pila en MAYÚSCULAS (ej: "CASILDA", "JUAN CARLOS", "MARÍA ELENA").
     Generalmente van después de los apellidos en el cuaderno. Si no puedes separar con certeza, pon el nombre completo en "nombres" y deja "apellidos" vacío.
   - "apellidos": Apellido(s) en MAYÚSCULAS (ej: "GUILLEMA", "MALDONADO ROA", "MAQUÍN CAJ").
     Generalmente van primero en la línea, justo después del número de expediente.
   - "edad_texto": Copia TEXTUALMENTE lo que ves sobre la edad o fecha de nacimiento
     (ej: "FN: 22-06-94", "F/N: 10-03-1985", "13/03/1990", "28 años", "6 meses", "FN: 07-01-03").
   - "fecha_nacimiento_estimada": Fecha de nacimiento en formato 'YYYY-MM-DD'.
     * Si viene "FN: DD/MM/AA" o "F/N: DD-MM-AA" (dos dígitos de año):
       - Si AA >= 26: año completo = 1900 + AA  (ej: 94 → 1994, 87 → 1987, 63 → 1963)
       - Si AA < 26:  año completo = 2000 + AA  (ej: 03 → 2003, 05 → 2005, 24 → 2024)
     * Si viene fecha completa "DD-MM-AAAA" o "DD/MM/AAAA": conviértela directamente a YYYY-MM-DD.
     * Si sólo indica edad en años (ej: "28 años"): resta al año 2026 → "1998-01-01".
     * Si indica meses (ej: "6 meses"): calcula los meses hacia atrás desde 2026-10.
     * Si no hay fecha ni edad recognoscible: devuelve "2000-01-01".
   - "sexo": "M" para masculino o "F" para femenino. Infiérelo del nombre si no está explícito.
     Nombres típicamente femeninos guatemaltecos: Casilda, Angelina, Margarita, Odilia, María, Faustina, Tami, Tostfina, Juana, Sara, Fanny, Candelaria, Evelyn, etc.
   - "dpi": Cadena numérica de 13 dígitos del DPI/CUI si está claramente anotado. Si no hay o no se puede leer, devuelve null.
   - "direccion": La comunidad, caserío, aldea, municipio o dirección anotada después del guión largo "—" o guión "-".
     No incluyas la fecha en la dirección. Ejemplos: "Chicamán", "Pueblo Nuevo", "Chixoy", "Beleján 16", "Con Sebastián Beirjú", "El Soch", "La Conquista".
     Si no hay dirección identificable, devuelve null.
   - "numero_expediente": El número de expediente o ficha que aparece al INICIO de la fila.
     Elimina comas, puntos y espacios del número (ej: "11,485" → "11485", "13,493" → "13493", "17,535" → "17535").
     Si no hay número visible, devuelve null.
   - "datos_familia": Cualquier referencia a familia o núcleo familiar (ej: "fam. Chic", "Fam 23", "núcleo 5", "Fam. García"). Si no hay, devuelve null.
   - "tachado": true si la fila está cruzada con una línea horizontal (tachón). Si no está tachada, devuelve false.

3. REGLAS CLAVE DE INTERPRETACIÓN:
   - El número al inicio de cada línea (ej: "11,485", "13,493") = NÚMERO DE EXPEDIENTE.
   - El texto entre el expediente y el guión "—" = NOMBRE COMPLETO (primero apellidos, luego nombres).
   - El texto después del guión "—" = DIRECCIÓN + FECHA DE NACIMIENTO.
   - "FN:", "F/N:", "FN." antes de una fecha = prefijo de Fecha de Nacimiento.
   - Una secuencia "DD-MM-AA" o "DD/MM/AA" o "DD.MM.AA" al final de la línea es casi siempre la fecha de nacimiento.
   - Ignora encabezados de página, fechas de la hoja del cuaderno, tachones de texto que no sean pacientes y notas de margen.
   - ORIENTACIÓN: La imagen puede estar girada (90° a la derecha, 90° a la izquierda o de cabeza). Detecta y lee el texto correctamente sin importar la orientación en la que fue tomada la foto.

4. Devuelve la respuesta en formato JSON estricto con la siguiente estructura (sin texto adicional):
{
  "titulo": "Registro de Cuaderno de Pacientes",
  "pacientes": [
    {
      "nombres": "CASILDA",
      "apellidos": "GUILLEMA",
      "edad_texto": "22-06-94",
      "fecha_nacimiento_estimada": "1994-06-22",
      "sexo": "F",
      "dpi": null,
      "direccion": "Chicamán",
      "numero_expediente": "11485",
      "datos_familia": null,
      "tachado": false
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
            'gemini-3.5-flash',
            'gemini-flash-latest',
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
                    ])->timeout(65)->post($endpoint, $payload);
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
