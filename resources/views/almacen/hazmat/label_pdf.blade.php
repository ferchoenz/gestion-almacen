<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Etiqueta NOM-018 - {{ $product->product_name }}</title>
    
    <style>
        /* --- CONFIGURACIÓN DE PÁGINA --- */
        @page {
            margin: 0.5cm; /* Márgenes mínimos en la hoja física */
        }

        body {
            font-family: Arial, sans-serif;
            padding: 0;
            margin: 0;
        }

        /* Contenedor Principal (Borde Rojo) */
        .ghs-label-container {
            border: 4px solid #d32f2f; /* Rojo seguridad */
            padding: 10px;
            width: 98%; /* Usar todo el ancho disponible */
            height: 680px; /* Altura fija aproximada para carta horizontal */
            position: relative;
            box-sizing: border-box;
        }

        /* --- ENCABEZADO --- */
        .product-title {
            /* Quitamos la línea negra y reducimos tamaño */
            font-size: 22px; 
            font-weight: 900;
            text-align: center;
            margin-bottom: 5px;
            text-transform: uppercase;
            line-height: 1.1;
            /* border-bottom: 2px solid black;  <-- ELIMINADA */
        }

        .chemical-name {
            font-size: 14px;
            color: #444;
            text-align: center;
            margin-top: 0;
            font-weight: normal;
        }

        /* --- ESTRUCTURA DE COLUMNAS (Usamos Tabla para estabilidad en PDF) --- */
        .layout-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .col-left {
            width: 35%;
            vertical-align: top;
            text-align: center; /* Centra todo lo de la izq */
            padding-right: 10px;
            border-right: 1px solid #ccc;
        }
        .col-right {
            width: 65%;
            vertical-align: top;
            padding-left: 10px;
        }

        /* --- PICTOGRAMAS --- */
        .pictograms-wrapper {
            text-align: center; /* Asegura centrado */
            margin-bottom: 15px;
        }
        .pictogram-img {
            width: 80px;
            height: 80px;
            margin: 2px;
            display: inline-block;
        }

        /* --- PALABRA DE ADVERTENCIA --- */
        .signal-word {
            font-size: 28px;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
            margin-top: 10px;
        }
        .text-danger-ghs { color: #d32f2f; }
        .text-warning-ghs { color: #fbc02d; }

        /* --- LISTAS DE FRASES H y P --- */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 5px;
            margin-bottom: 2px;
        }
        
        /* Estilo compacto para que quepa todo */
        .statement-list {
            list-style-type: none; /* Quitamos viñetas default para ganar espacio */
            padding: 0;
            margin: 0;
            font-size: 10px; /* Letra pequeña legible */
            text-align: justify;
            line-height: 1.1; /* Líneas pegaditas */
        }
        .statement-list li {
            margin-bottom: 2px;
            padding-left: 10px;
            text-indent: -10px; /* Sangría francesa para listas limpias */
        }
        .statement-list li:before {
            content: "- ";
            font-weight: bold;
        }

        /* --- FOOTER (Fabricante) --- */
        .footer-table {
            width: 100%;
            margin-top: 15px;
            border-top: 2px solid #333;
            padding-top: 5px;
            font-size: 9px;
        }
        .footer-left { width: 60%; text-align: left; }
        .footer-right { width: 40%; text-align: right; }

    </style>
</head>
<body>

    <div class="ghs-label-container">
        
        <!-- TÍTULO -->
        <div class="product-title">{{ $product->product_name }}</div>
        
        @if($product->chemical_name && $product->chemical_name !== $product->product_name)
            <div class="chemical-name">({{ $product->chemical_name }})</div>
        @endif

        <!-- CONTENIDO PRINCIPAL -->
        <table class="layout-table">
            <tr>
                <!-- COLUMNA IZQUIERDA (Visual) -->
                <td class="col-left">
                    <div class="pictograms-wrapper">
                        @if($product->pictograms)
                            @foreach($product->pictograms as $pictoKey)
                                <img src="{{ public_path('images/ghs/' . $pictoKey . '.png') }}" 
                                     class="pictogram-img" 
                                     alt="Picto">
                            @endforeach
                        @else
                             <br><em>(Sin pictogramas)</em>
                        @endif
                    </div>

                    <div class="signal-word">
                        @if($product->signal_word === 'PELIGRO')
                            <span class="text-danger-ghs">PELIGRO</span>
                        @elseif($product->signal_word === 'ATENCION')
                            <span class="text-warning-ghs">ATENCIÓN</span>
                        @endif
                    </div>

                    @if($product->cas_number)
                        <div style="margin-top: 20px; font-size: 10px; font-weight: bold;">
                            CAS: {{ $product->cas_number }}
                        </div>
                    @endif
                </td>

                <!-- COLUMNA DERECHA (Texto) -->
                <td class="col-right">
                    <!-- Indicaciones de Peligro -->
                    @if($product->hazard_statements)
                        <div class="section-title">INDICACIONES DE PELIGRO (H):</div>
                        <ul class="statement-list">
                            @foreach(explode("\n", $product->hazard_statements) as $statement)
                                @if(trim($statement) !== '')
                                    <li>{{ trim(str_replace(['●', '- '], '', $statement)) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif

                    <!-- Consejos de Prudencia -->
                    @if($product->precautionary_statements)
                        <div class="section-title" style="margin-top: 8px;">CONSEJOS DE PRUDENCIA (P):</div>
                        <ul class="statement-list">
                            @foreach(explode("\n", $product->precautionary_statements) as $statement)
                                @if(trim($statement) !== '')
                                    <li>{{ trim(str_replace(['●', '- '], '', $statement)) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </td>
            </tr>
        </table>

        <!-- FOOTER (Siempre abajo) -->
        <div style="position: absolute; bottom: 10px; width: 97%;">
            <table class="footer-table">
                <tr>
                    <td class="footer-left">
                        <strong>Fabricante / Proveedor:</strong> {{ $product->manufacturer ?? 'No especificado' }}<br>
                        <strong>Dirección:</strong> {{ $product->address ?? '---' }}
                    </td>
                    <td class="footer-right">
                        <strong>TEL. EMERGENCIA 24H:</strong><br>
                        <span style="font-size: 11px; font-weight: bold; color: #d32f2f;">
                            {{ $product->emergency_phone ?? '911' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>
</html>