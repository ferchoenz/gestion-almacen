<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected $apiKey;
    // Usamos el modelo más potente disponible actualmente
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

        // PROMPT MEJORADO: Validación de HDS y Extracción de EPP
        $prompt = "Actúa como un experto en seguridad industrial y la NOM-018-STPS-2015. 
        Analiza el documento adjunto.
        
        1. PRIMERO: Determina si el documento es realmente una Hoja de Datos de Seguridad (HDS/MSDS) válida.
        2. Si NO es una HDS (ej. es una factura, una carta, una foto irrelevante), devuelve 'is_valid_hds': false y 'error_msg': 'El documento cargado NO parece ser una Hoja de Datos de Seguridad válida.'.
        3. Si SÍ es una HDS, devuelve 'is_valid_hds': true y extrae los datos.

        Extrae la siguiente información y devuélvela EXCLUSIVAMENTE en formato JSON válido:
        
        {
            \"is_valid_hds\": true/false,
            \"error_msg\": \"Mensaje de error solo si no es valida\",
            \"product_name\": \"Nombre comercial del producto\",
            \"chemical_name\": \"Nombre químico o técnico principal\",
            \"cas_number\": \"Si hay varios números CAS, extrae todos separados por comas.\",
            \"manufacturer\": \"Nombre de la empresa fabricante o proveedor\",
            \"emergency_phone\": \"Teléfono de emergencia\",
            \"address\": \"Dirección del fabricante\",
            \"signal_word\": \"Solo una: 'PELIGRO', 'ATENCION' o 'SIN PALABRA'\",
            \"hazard_statements\": \"Lista de códigos H y sus frases\",
            \"precautionary_statements\": \"Lista de códigos P y sus consejos\",
            \"epp\": \"Equipo de Protección Personal (EPP) sugerido explícitamente en la sección 8 (ej: Guantes de nitrilo, Gafas de seguridad, Respirador con cartuchos)\",
            \"pictograms\": [\"Lista de claves de pictogramas aplicables: 'flame', 'flame_over_circle', 'exploding_bomb', 'corrosion', 'gas_cylinder', 'skull_and_crossbones', 'exclamation_mark', 'environment', 'health_hazard'\"]
        }
        
        Si no encuentras un dato específico, pon cadena vacía (\"\").";

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