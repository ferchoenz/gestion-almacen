<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Etiqueta NOM-018 - {{ $hazmat->product_name }}</title>
    
    <!-- Usamos Bootstrap 5 vía CDN para estilos rápidos de impresión -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Estilos específicos para la impresión y optimización de espacio */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        /* Contenedor principal de la etiqueta (borde grueso estilo GHS) */
        .ghs-label-container {
            max-width: 900px; /* Ancho máximo para controlar la impresión */
            margin: 0 auto;
            background: white;
            border: 4px solid black; /* Borde GHS obligatorio */
            padding: 15px;
            page-break-inside: avoid; /* Intenta evitar cortes de página */
        }

        /* Título del producto más compacto */
        .product-title {
            font-size: 1.5rem; /* Reducido de un h1 normal */
            font-weight: 800;
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
        }

        /* Títulos de sección (Indicaciones, Consejos) */
        .section-title {
            font-weight: bold;
            font-size: 1rem;
            margin-top: 10px;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        /* Palabra de advertencia (PELIGRO/ATENCIÓN) */
        .signal-word {
            font-size: 1.8rem;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
            margin: 15px 0;
        }
        .text-danger-ghs { color: #d80000; } /* Rojo estándar GHS */
        .text-warning-ghs { color: #ff6600; } /* Naranja para atención */

        /* Imágenes de pictogramas */
        .pictogram-img {
            width: 85px; /* Tamaño controlado */
            height: 85px;
            object-fit: contain;
            margin: 5px;
        }

        /* Listas de frases (H y P) - CLAVE PARA AHORRAR ESPACIO */
        .statement-list {
            font-size: 0.85rem; /* Fuente más pequeña para que quepan más */
            padding-left: 20px; /* Menos sangría */
            margin-bottom: 5px;
            text-align: justify;
        }
        .statement-list li {
            margin-bottom: 3px; /* Menos espacio entre líneas */
        }

        /* Sección del proveedor al pie */
        .supplier-footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid black;
            font-size: 0.8rem; /* Letra pequeña para datos de contacto */
        }

        /* Estilos exclusivos para cuando se imprime */
        @media print {
            body {
                padding: 0;
                background-color: white;
            }
            .ghs-label-container {
                border: 4px solid black !important; /* Forzar borde al imprimir */
                box-shadow: none;
                max-width: 100%; /* Usar todo el ancho del papel */
                margin: 0;
            }
            /* Ocultar botones o elementos web si los hubiera */
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="ghs-label-container">
        
        <!-- 1. Identificación del Producto (Título Reducido) -->
        <div class="product-title">
            {{ $hazmat->product_name }}
            @if($hazmat->chemical_name && $hazmat->chemical_name !== $hazmat->product_name)
                <br><small class="text-muted fw-normal" style="font-size: 1rem;">({{ $hazmat->chemical_name }})</small>
            @endif
        </div>

        <div class="row g-3">
            <!-- COLUMNA IZQUIERDA: Pictogramas, Palabra de Advertencia e Indicaciones de Peligro -->
            <div class="col-md-5 text-center border-end-md border-dark pe-md-4">
                
                <!-- 2. Pictogramas -->
                <div class="d-flex justify-content-center flex-wrap mb-3">
                    @if($hazmat->pictograms)
                        @foreach($hazmat->pictograms as $pictoKey)
                            <!-- Asumiendo que las imágenes están en public/images/pictograms/ -->
                            <img src="{{ asset('images/pictograms/' . $pictoKey . '.png') }}" 
                                 alt="{{ $pictoKey }}" 
                                 class="pictogram-img"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/85x85?text=Picto';"> <!-- Fallback si falta imagen -->
                        @endforeach
                    @else
                       <p class="text-muted fst-italic small">Sin pictogramas requeridos</p>
                    @endif
                </div>

                <!-- 3. Palabra de Advertencia -->
                @if($hazmat->signal_word === 'PELIGRO')
                    <div class="signal-word text-danger-ghs">PELIGRO</div>
                @elseif($hazmat->signal_word === 'ATENCION')
                    <div class="signal-word text-warning-ghs">ATENCIÓN</div>
                @else
                    <div class="signal-word text-muted" style="font-size: 1.2rem;">SIN PALABRA DE ADVERTENCIA</div>
                @endif

                <!-- 4. Indicaciones de Peligro (Frases H) -->
                <div class="text-start mt-4">
                    <div class="section-title">Indicaciones de Peligro (H)</div>
                    @if($hazmat->hazard_statements)
                        <ul class="statement-list">
                            @foreach(explode("\n", $hazmat->hazard_statements) as $statement)
                                @if(trim($statement) !== '')
                                    <li>{{ trim(str_replace(['●', '- '], '', $statement)) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <p class="small text-muted fst-italic">No especificadas.</p>
                    @endif
                </div>
            </div>

            <!-- COLUMNA DERECHA: Consejos de Prudencia (Optimizada para espacio) -->
            <div class="col-md-7 ps-md-4">
                
                <!-- 5. Consejos de Prudencia (Frases P) -->
                <div class="section-title mt-0">Consejos de Prudencia (P)</div>
                @if($hazmat->precautionary_statements)
                    <!-- 
                         Se usa 'column-count' de CSS para dividir la lista larga en 2 columnas automáticamente 
                         si es muy ancha, o se mantiene en una lista compacta con letra pequeña.
                         Aquí optamos por lista compacta y letra pequeña definida en CSS (.statement-list).
                    -->
                    <ul class="statement-list">
                        @foreach(explode("\n", $hazmat->precautionary_statements) as $statement)
                            @if(trim($statement) !== '')
                                <!-- Limpiamos viñetas que a veces trae la IA para usar las de HTML -->
                                <li>{{ trim(str_replace(['●', '- '], '', $statement)) }}</li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <p class="small text-muted fst-italic">No especificados. Consulte la HDS.</p>
                @endif

                <!-- Información Adicional Compacta -->
                @if($hazmat->cas_number)
                    <div class="mt-3 pt-2 border-top border-secondary small">
                        <strong>No. CAS:</strong> {{ $hazmat->cas_number }}
                    </div>
                @endif
            </div>
        </div>

        <!-- 6. Información del Proveedor/Fabricante (Footer Nuevo) -->
        <div class="supplier-footer gx-2">
            <div class="row">
                <div class="col-md-6 mb-1">
                   <strong>Fabricante / Proveedor:</strong><br>
                   {{ $hazmat->manufacturer ?? 'Información no disponible en HDS' }}
                </div>
                <div class="col-md-6 mb-1 text-md-end">
                    <strong>Teléfono de Emergencia 24h:</strong><br>
                    <span class="fw-bold text-danger-ghs">{{ $hazmat->emergency_phone ?? 'Consultar HDS/Protocolo Interno' }}</span>
                </div>
                @if($hazmat->address)
                <div class="col-12">
                    <strong>Dirección:</strong> {{ $hazmat->address }}
                </div>
                @endif
            </div>
        </div>

    </div> <!-- Fin Container -->

    <!-- Script opcional para auto-imprimir al cargar -->
    <!-- <script> window.onload = function() { window.print(); } </script> -->

</body>
</html>