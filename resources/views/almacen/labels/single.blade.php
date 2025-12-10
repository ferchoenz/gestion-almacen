<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta - {{ $consumable->sku }}</title>
    <style>
        @page {
            size: 50mm 25mm;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            width: 50mm;
            height: 25mm;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
        }
        
        .label {
            width: 48mm;
            height: 23mm;
            padding: 2mm;
            border: 0.5pt solid #ccc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
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
            max-width: 40mm;
            height: 8mm;
        }
        
        .barcode-text {
            font-size: 6pt;
            font-family: 'Courier New', monospace;
            margin-top: 0.5mm;
        }
        
        .sku {
            font-size: 8pt;
            font-weight: bold;
            color: #333;
        }
        
        .location {
            font-size: 5pt;
            color: #666;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
        }
        
        .controls {
            position: fixed;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        .controls button {
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .btn-print {
            background: #4F46E5;
            color: white;
        }
        
        .btn-print:hover {
            background: #4338CA;
        }
        
        .btn-back {
            background: #6B7280;
            color: white;
        }
        
        .btn-back:hover {
            background: #4B5563;
        }
    </style>
</head>
<body>
    <div class="controls no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimir</button>
        <button class="btn-back" onclick="history.back()">← Volver</button>
    </div>

    <div class="label">
        <div class="product-name">{{ Str::limit($consumable->name, 40) }}</div>
        
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

    <script>
        // Auto-print on load (optional)
        // window.onload = () => window.print();
    </script>
</body>
</html>
