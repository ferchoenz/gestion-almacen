<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Etiqueta NOM-018 - {{ $product->product_name }}</title>
    
    <style>
        /* --- CONFIGURACIÓN DE PÁGINA --- */
        @page {
            margin: 0.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 0;
            margin: 0;
        }

        /* Contenedor Principal (Borde Rojo NOM-018) */
        .ghs-label-container {
            border: 5px solid #d32f2f;
            padding: 12px;
            width: 98%;
            height: 680px;
            position: relative;
            box-sizing: border-box;
            background: white;
        }

        /* --- ENCABEZADO --- */
        .product-title {
            font-size: 24px;
            font-weight: 900;
            text-align: center;
            margin-bottom: 3px;
            text-transform: uppercase;
            line-height: 1.2;
            color: #1a1a1a;
            padding: 5px;
            background: linear-gradient(to bottom, #f5f5f5 0%, #ffffff 100%);
            border-bottom: 3px solid #d32f2f;
        }

        .chemical-name {
            font-size: 13px;
            color: #555;
            text-align: center;
            margin-top: 2px;
            margin-bottom: 8px;
            font-weight: 600;
            font-style: italic;
        }

        /* --- ESTRUCTURA DE COLUMNAS --- */
        .layout-table {
            width: 100%;
            margin-top: 8px;
            border-collapse: collapse;
        }
        
        .col-left {
            width: 38%;
            vertical-align: top;
            text-align: center;
            padding-right: 12px;
            border-right: 2px solid #d32f2f;
        }
        
        .col-right {
            width: 62%;
            vertical-align: top;
            padding-left: 12px;
        }

        /* --- PICTOGRAMAS --- */
        .pictograms-wrapper {
            text-align: center;
            margin-bottom: 12px;
            background: #fafafa;
            padding: 8px;
            border-radius: 4px;
        }
        
        .pictogram-img {
            width: 75px;
            height: 75px;
            margin: 3px;
            display: inline-block;
            border: 1px solid #ddd;
        }

        /* --- PALABRA DE ADVERTENCIA --- */
        .signal-word {
            font-size: 32px;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
            margin: 12px 0;
            padding: 8px;
            border: 2px solid;
            border-radius: 4px;
        }
        
        .text-danger-ghs {
            color: #d32f2f;
            background: #ffebee;
            border-color: #d32f2f !important;
        }
        
        .text-warning-ghs {
            color: #f57c00;
            background: #fff3e0;
            border-color: #f57c00 !important;
        }

        /* --- SECCIÓN EPP --- */
        .epp-section {
            margin-top: 12px;
            padding: 8px;
            background: #e3f2fd;
            border: 2px solid #1976d2;
            border-radius: 4px;
            text-align: center;
        }
        
        .epp-title {
            font-size: 10px;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        
        .epp-content {
            font-size: 9px;
            line-height: 1.3;
            color: #333;
            text-align: left;
            padding: 0 5px;
        }

        /* --- CAS NUMBER --- */
        .cas-box {
            margin-top: 12px;
            padding: 6px;
            background: #fff9c4;
            border: 2px solid #f9a825;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            color: #333;
        }

        /* --- LISTAS DE FRASES H y P --- */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            background: #f5f5f5;
            padding: 4px 6px;
            margin-top: 6px;
            margin-bottom: 3px;
            border-left: 4px solid #d32f2f;
            color: #d32f2f;
        }
        
        .statement-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
            font-size: 9.5px;
            text-align: justify;
            line-height: 1.25;
        }
        
        .statement-list li {
            margin-bottom: 2px;
            padding-left: 12px;
            text-indent: -12px;
            color: #333;
        }
        
        .statement-list li:before {
            content: "• ";
            font-weight: bold;
            color: #d32f2f;
        }

        /* --- FOOTER (Fabricante) --- */
        .footer-section {
            position: absolute;
            bottom: 12px;
            width: 96%;
            border-top: 3px solid #d32f2f;
            padding-top: 6px;
            background: #fafafa;
            padding: 8px;
        }
        
        .footer-table {
            width: 100%;
            font-size: 9px;
        }
        
        .footer-left {
            width: 65%;
            text-align: left;
            vertical-align: top;
        }
        
        .footer-right {
            width: 35%;
            text-align: right;
            vertical-align: top;
        }
        
        .emergency-phone {
            font-size: 13px;
            font-weight: 900;
            color: #d32f2f;
            background: #ffebee;
            padding: 4px 8px;
            border: 2px solid #d32f2f;
            border-radius: 4px;
            display: inline-block;
            margin-top: 2px;
        }

        /* --- UTILIDADES --- */
        strong {
            color: #1a1a1a;
        }

    </style>
</head>
<body>

    <div class="ghs-label-container">
        
        <!-- TÍTULO -->
        <div class="product-title">{{ $product->product_name }}</div>
        
        @if($product->chemical_name && $product->chemical_name !== $product->product_name)
            <div class="chemical-name">{{ $product->chemical_name }}</div>
        @endif

        <!-- CONTENIDO PRINCIPAL -->
        <table class="layout-table">
            <tr>
                <!-- COLUMNA IZQUIERDA (Visual) -->
                <td class="col-left">
                    <!-- Pictogramas GHS -->
                    <div class="pictograms-wrapper">
                        @if($product->pictograms && count($product->pictograms) > 0)
                            @foreach($product->pictograms as $pictoKey)
                                <img src="{{ public_path('images/ghs/' . $pictoKey . '.png') }}" 
                                     class="pictogram-img" 
                                     alt="{{ $pictoKey }}">
                            @endforeach
                        @else
                             <div style="padding: 20px; color: #999;">
                                 <em>(Sin pictogramas)</em>
                             </div>
                        @endif
                    </div>

                    <!-- Palabra de Advertencia -->
                    @if($product->signal_word && $product->signal_word !== 'SIN PALABRA')
                        <div class="signal-word {{ $product->signal_word === 'PELIGRO' ? 'text-danger-ghs' : 'text-warning-ghs' }}">
                            {{ $product->signal_word }}
                        </div>
                    @endif

                    <!-- Número CAS -->
                    @if($product->cas_number)
                        <div class="cas-box">
                            <strong>CAS:</strong> {{ $product->cas_number }}
                        </div>
                    @endif

                    <!-- EPP RECOMENDADO -->
                    @if($product->epp)
                        <div class="epp-section">
                            <div class="epp-title">EPP RECOMENDADO</div>
                            <div class="epp-content">
                                {{ $product->epp }}
                            </div>
                        </div>
                    @endif
                </td>

                <!-- COLUMNA DERECHA (Texto) -->
                <td class="col-right">
                    <!-- Indicaciones de Peligro -->
                    @if($product->hazard_statements)
                        <div class="section-title">INDICACIONES DE PELIGRO (H)</div>
                        <ul class="statement-list">
                            @foreach(explode("\n", $product->hazard_statements) as $statement)
                                @if(trim($statement) !== '')
                                    <li>{{ trim(str_replace(['●', '- ', '•'], '', $statement)) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif

                    <!-- Consejos de Prudencia -->
                    @if($product->precautionary_statements)
                        <div class="section-title" style="margin-top: 10px;">CONSEJOS DE PRUDENCIA (P)</div>
                        <ul class="statement-list">
                            @foreach(explode("\n", $product->precautionary_statements) as $statement)
                                @if(trim($statement) !== '')
                                    <li>{{ trim(str_replace(['●', '- ', '•'], '', $statement)) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif

                    <!-- Información Adicional -->
                    @if($product->location || $product->department)
                        <div style="margin-top: 12px; padding: 6px; background: #f5f5f5; border-left: 4px solid #666; font-size: 9px;">
                            @if($product->location)
                                <strong>Ubicación:</strong> {{ $product->location }}<br>
                            @endif
                            @if($product->department)
                                <strong>Departamento:</strong> {{ $product->department }}
                            @endif
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- FOOTER (Fabricante e Información de Emergencia) -->
        <div class="footer-section">
            <table class="footer-table">
                <tr>
                    <td class="footer-left">
                        <strong>Fabricante / Proveedor:</strong> {{ $product->manufacturer ?? 'No especificado' }}<br>
                        @if($product->address)
                            <strong>Dirección:</strong> {{ $product->address }}
                        @endif
                    </td>
                    <td class="footer-right">
                        <strong>TELÉFONO DE EMERGENCIA 24H:</strong><br>
                        <span class="emergency-phone">
                            {{ $product->emergency_phone ?? '911' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>
</html>