<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected $apiKey;
    // Usamos el modelo más reciente disponible
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

        // PROMPT MEJORADO: Pide datos del fabricante y maneja múltiples CAS
        $prompt = "Actúa como un experto en seguridad industrial y la NOM-018-STPS-2015. 
        Analiza el documento adjunto (Hoja de Datos de Seguridad).
        Extrae la siguiente información y devuélvela EXCLUSIVAMENTE en formato JSON válido, sin texto adicional ni bloques de código markdown (```json ... ```):
        
        {
            \"product_name\": \"Nombre comercial del producto\",
            \"chemical_name\": \"Nombre químico o técnico principal\",
            \"cas_number\": \"Si hay varios números CAS, extrae todos y sepáralos por comas (ej: '7664-93-9, 7732-18-5'). Si solo hay uno, pon ese.\",
            \"manufacturer\": \"Nombre de la empresa fabricante o proveedor\",
            \"emergency_phone\": \"Teléfono de emergencia (ej: SETIQ, CHEMTREC o del fabricante)\",
            \"address\": \"Dirección del fabricante o proveedor\",
            \"signal_word\": \"Solo una de estas tres opciones exactas: 'PELIGRO', 'ATENCION' o 'SIN PALABRA'\",
            \"hazard_statements\": \"Lista de códigos H y sus frases completas (ej: H225: Líquido muy inflamable)\",
            \"precautionary_statements\": \"Lista de códigos P y sus consejos principales (resumidos)\",
            \"pictograms\": [\"Lista de pictogramas que aplican. Usa SOLO estos nombres clave: 'flame', 'flame_over_circle', 'exploding_bomb', 'corrosion', 'gas_cylinder', 'skull_and_crossbones', 'exclamation_mark', 'environment', 'health_hazard'\"]
        }
        
        Si no encuentras un dato, pon cadena vacía (\"\").";

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
            
            if (str_contains($errorMessage, 'not found for API version')) {
                throw new Exception("Error de configuración: El modelo de IA seleccionado no está disponible.");
            }
            
            throw new Exception("Error de Google API: " . $errorMessage);
        }

        $jsonString = $response->json()['candidates'][0]['content']['parts'][0]['text'];
        $jsonString = str_replace('```json', '', $jsonString);
        $jsonString = str_replace('```', '', $jsonString);

        return json_decode($jsonString, true);
    }
}