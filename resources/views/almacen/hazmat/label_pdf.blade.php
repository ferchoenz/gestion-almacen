<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Etiqueta NOM-018 - {{ $product->product_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Contenedor principal de la etiqueta (Borde rojo grueso típico de seguridad) */
        .label-container {
            border: 5px solid #d32f2f;
            /* Rojo seguridad */
            padding: 20px;
            margin: 20px;
            width: 900px;
            /* Ancho fijo para asegurar distribución */
            height: 600px;
            position: relative;
        }

        .header {
            text-align: center;
            /* border-bottom: 2px solid #333;  <-- Coloca una linea debajo del encabezado */
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .product-name {
            font-size: 36px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .chemical-name {
            font-size: 18px;
            color: #555;
            margin: 5px 0 0 0;
        }

        .content-grid {
            display: table;
            width: 100%;
        }

        .column-left {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            padding-right: 20px;
        }

        .column-right {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        /* PICTOGRAMAS */
        .pictograms-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .pictogram-img {
            width: 120px;
            height: 120px;
            margin: 5px;
            display: inline-block;
        }

        /* PALABRA DE ADVERTENCIA */
        .signal-word {
            font-size: 40px;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .signal-danger {
            color: #d32f2f;
        }

        /* Rojo Peligro */
        .signal-warning {
            color: #fbc02d;
        }

        /* Amarillo Atención */

        /* FRASES H y P */
        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
            font-size: 14px;
        }

        .statement-text {
            font-size: 12px;
            margin-bottom: 10px;
            line-height: 1.4;
            text-align: justify;
        }

        /* PIE DE ETIQUETA */
        .footer {
            position: absolute;
            bottom: 10px;
            left: 20px;
            right: 20px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="label-container">

        <!-- ENCABEZADO: Identificación del Producto -->
        <div class="header">
            <h1 class="product-name">{{ $product->product_name }}</h1>
            @if ($product->chemical_name != $product->product_name)
                <h2 class="chemical-name">{{ $product->chemical_name }}</h2>
            @endif
            <p style="margin: 5px 0 0 0;">CAS: {{ $product->cas_number ?? 'N/A' }}</p>
        </div>

        <div class="content-grid">

            <!-- COLUMNA IZQUIERDA: Visual (Pictogramas y Palabra) -->
            <div class="column-left">
                <div class="pictograms-container">
                    @if ($product->pictograms)
                        @foreach ($product->pictograms as $pic)
                            <!-- Busca la imagen en public/images/ghs/nombre.png -->
                            <img src="{{ public_path('images/ghs/' . $pic . '.png') }}" class="pictogram-img"
                                alt="{{ $pic }}">
                        @endforeach
                    @endif
                </div>

                <div class="signal-word {{ $product->signal_word == 'PELIGRO' ? 'signal-danger' : 'signal-warning' }}">
                    {{ $product->signal_word }}
                </div>
            </div>

            <!-- COLUMNA DERECHA: Texto (Frases H y P) -->
            <div class="column-right">
                @if ($product->hazard_statements)
                    <div class="section-title">INDICACIONES DE PELIGRO (H):</div>
                    <div class="statement-text">
                        {!! nl2br(e($product->hazard_statements)) !!}
                    </div>
                @endif

                @if ($product->precautionary_statements)
                    <div class="section-title">CONSEJOS DE PRUDENCIA (P):</div>
                    <div class="statement-text">
                        {!! nl2br(e($product->precautionary_statements)) !!}
                    </div>
                @endif
            </div>
        </div>

        <!-- PIE DE PÁGINA: Datos de la empresa -->
        <div class="footer">
            <strong>SISTEMA DE GESTIÓN DE MATERIALES</strong> | Generado el: {{ date('d/m/Y') }} <br>
            En caso de emergencia llamar al número de seguridad de la planta. Consultar HDS completa para más
            información.
        </div>
    </div>
</body>

</html>
