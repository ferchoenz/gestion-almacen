<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiquetas ({{ $quantity }}) - {{ $consumable->sku }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .labels-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5mm;
            background: white;
            padding: 10mm;
        }
        
        .label {
            width: 45mm;
            height: 25mm;
            padding: 2mm;
            border: 0.5pt solid #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            background: white;
            page-break-inside: avoid;
        }
        
        .product-name {
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
            line-height: 1.1;
            max-height: 5mm;
            overflow: hidden;
            width: 100%;
        }
        
        .barcode-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .barcode-container img {
            max-width: 38mm;
            height: 8mm;
        }
        
        .barcode-text {
            font-size: 5pt;
            font-family: 'Courier New', monospace;
            margin-top: 0.5mm;
        }
        
        .sku {
            font-size: 7pt;
            font-weight: bold;
            color: #333;
        }
        
        .location {
            font-size: 5pt;
            color: #666;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .labels-grid {
                padding: 5mm;
            }
        }
        
        .controls {
            position: fixed;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .controls button {
            padding: 12px 24px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.2s;
        }
        
        .btn-print {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            color: white;
        }
        
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }
        
        .btn-back {
            background: #6B7280;
            color: white;
        }
        
        .btn-back:hover {
            background: #4B5563;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="controls no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimir {{ $quantity }} etiquetas</button>
        <button class="btn-back" onclick="history.back()">← Volver</button>
    </div>

    <div class="header no-print">
        <h1>{{ $consumable->name }}</h1>
        <p>{{ $quantity }} etiquetas listas para imprimir</p>
    </div>

    <div class="labels-grid">
        @for($i = 0; $i < $quantity; $i++)
        <div class="label">
            <div class="product-name">{{ Str::limit($consumable->name, 35) }}</div>
            
            <div class="barcode-container">
                <img src="data:image/png;base64,{{ $barcode }}" alt="Código de Barras">
                <span class="barcode-text">{{ $consumable->barcode }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                <span class="sku">{{ $consumable->sku }}</span>
                @if($consumable->location)
                    <span class="location">📍 {{ $consumable->location->code }}</span>
                @endif
            </div>
        </div>
        @endfor
    </div>
</body>
</html>
