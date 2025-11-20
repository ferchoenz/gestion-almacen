<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Etiqueta NOM-018 - {{ $product->product_name }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .ghs-label-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border: 4px solid black;
            padding: 15px;
            page-break-inside: avoid;
        }
        .product-title {
            font-size: 1.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
        }
        .section-title {
            font-weight: bold;
            font-size: 1rem;
            margin-top: 10px;
            margin-bottom: 5px;
            text-decoration: underline;
        }
        .signal-word {
            font-size: 1.8rem;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
            margin: 15px 0;
        }
        .text-danger-ghs { color: #d80000; }
        .text-warning-ghs { color: #ff6600; }

        .pictogram-img {
            width: 85px;
            height: 85px;
            object-fit: contain;
            margin: 5px;
        }

        .statement-list {
            font-size: 0.85rem;
            padding-left: 20px;
            margin-bottom: 5px;
            text-align: justify;
        }
        .statement-list li { margin-bottom: 3px; }

        .supplier-footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid black;
            font-size: 0.8rem;
        }
        
        @media print {
            .ghs-label-container {
                border: 4px solid black !important;
                max-width: 100%;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="ghs-label-container">
        
        <!-- Título -->
        <div class="product-title">
            {{ $product->product_name }}
            @if($product->chemical_name && $product->chemical_name !== $product->product_name)
                <br><small class="text-muted fw-normal" style="font-size: 1rem;">({{ $product->chemical_name }})</small>
            @endif
        </div>

        <div class="row g-3">
            <!-- Columna Izquierda -->
            <div class="col-md-5 text-center border-end-md border-dark pe-md-4">
                
                <div class="d-flex justify-content-center flex-wrap mb-3">
                    @if($product->pictograms)
                        @foreach($product->pictograms as $pictoKey)
                            <img src="{{ public_path('images/ghs/' . $pictoKey . '.png') }}" 
                                 alt="{{ $pictoKey }}" 
                                 class="pictogram-img">
                        @endforeach
                    @else
                       <p class="text-muted fst-italic small">Sin pictogramas</p>
                    @endif
                </div>

                @if($product->signal_word === 'PELIGRO')
                    <div class="signal-word text-danger-ghs">PELIGRO</div>
                @elseif($product->signal_word === 'ATENCION')
                    <div class="signal-word text-warning-ghs">ATENCIÓN</div>
                @else
                    <div class="signal-word text-muted" style="font-size: 1.2rem;">SIN PALABRA</div>
                @endif

                <div class="text-start mt-4">
                    <div class="section-title">Indicaciones de Peligro (H)</div>
                    @if($product->hazard_statements)
                        <ul class="statement-list">
                            @foreach(explode("\n", $product->hazard_statements) as $statement)
                                @if(trim($statement) !== '')
                                    <li>{{ trim(str_replace(['●', '- '], '', $statement)) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Columna Derecha -->
            <div class="col-md-7 ps-md-4">
                <div class="section-title mt-0">Consejos de Prudencia (P)</div>
                @if($product->precautionary_statements)
                    <ul class="statement-list">
                        @foreach(explode("\n", $product->precautionary_statements) as $statement)
                            @if(trim($statement) !== '')
                                <li>{{ trim(str_replace(['●', '- '], '', $statement)) }}</li>
                            @endif
                        @endforeach
                    </ul>
                @endif

                @if($product->cas_number)
                    <div class="mt-3 pt-2 border-top border-secondary small">
                        <strong>No. CAS:</strong> {{ $product->cas_number }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer Fabricante -->
        <div class="supplier-footer gx-2">
            <div class="row">
                <div class="col-md-6 mb-1">
                   <strong>Fabricante / Proveedor:</strong><br>
                   {{ $product->manufacturer ?? 'N/A' }}
                </div>
                <div class="col-md-6 mb-1 text-md-end">
                    <strong>Teléfono de Emergencia:</strong><br>
                    <span class="fw-bold text-danger-ghs">{{ $product->emergency_phone ?? 'N/A' }}</span>
                </div>
                @if($product->address)
                <div class="col-12">
                    <strong>Dirección:</strong> {{ $product->address }}
                </div>
                @endif
            </div>
        </div>

    </div>
</body>
</html>