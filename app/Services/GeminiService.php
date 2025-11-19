<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected $apiKey;
    
    // ACTUALIZACIÓN: Cambiamos al modelo 2.5 Flash Preview, que es más reciente y potente
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function analyzeHdsPdf($pdfBase64)
    {
        if (!$this->apiKey) {
            throw new Exception("No se encontró la API KEY de Gemini en el archivo .env");
        }

        $prompt = "Actúa como un experto en seguridad industrial y la NOM-018-STPS-2015. 
        Analiza el documento adjunto (Hoja de Datos de Seguridad).
        Extrae la siguiente información y devuélvela EXCLUSIVAMENTE en formato JSON válido, sin texto adicional ni bloques de código markdown (```json ... ```):
        
        {
            \"product_name\": \"Nombre comercial del producto\",
            \"chemical_name\": \"Nombre químico o técnico principal\",
            \"cas_number\": \"Número CAS principal (si aplica)\",
            \"signal_word\": \"Solo una de estas tres opciones exactas: 'PELIGRO', 'ATENCION' o 'SIN PALABRA'\",
            \"hazard_statements\": \"Lista de códigos H y sus frases (ej: H225: Líquido muy inflamable)\",
            \"precautionary_statements\": \"Lista de códigos P y sus consejos principales (resumidos)\",
            \"pictograms\": [\"Lista de pictogramas que aplican. Usa SOLO estos nombres clave: 'flame', 'flame_over_circle', 'exploding_bomb', 'corrosion', 'gas_cylinder', 'skull_and_crossbones', 'exclamation_mark', 'environment', 'health_hazard'\"]
        }
        
        Si no encuentras un dato, pon null o cadena vacía.";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '?key=' . $this->apiKey, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => 'application/pdf',
                                'data' => $pdfBase64
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json'
            ]
        ]);

        if ($response->failed()) {
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();
            Log::error('Gemini API Error: ' . $errorMessage);
            
            // Mensaje más amigable si el error es por modelo no encontrado
            if (str_contains($errorMessage, 'not found for API version')) {
                throw new Exception("Error de configuración: El modelo de IA seleccionado no está disponible actualmente. Verifica la versión del modelo en GeminiService.php.");
            }
            
            throw new Exception("Error de Google API: " . $errorMessage);
        }

        $jsonString = $response->json()['candidates'][0]['content']['parts'][0]['text'];
        
        // Limpieza extra por si la IA manda bloques de código markdown
        $jsonString = str_replace('```json', '', $jsonString);
        $jsonString = str_replace('```', '', $jsonString);

        return json_decode($jsonString, true);
    }
}