<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vale de Salida - {{ $output->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 250px;
            height: auto;
        }
        h1 {
            font-size: 24px;
            margin: 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .details-table th, 
        .details-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .details-table th {
            background-color: #f4f4f4;
            width: 30%;
        }
        .signatures {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            width: 48%;
            display: inline-block;
            text-align: center;
        }
        .signature-box img {
            width: 250px;
            height: 125px;
            border: 1px solid #eee;
            background-color: #f9f9f9;
        }
        .signature-box p {
            margin-top: 5px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @php
                // LOGICA DINÁMICA DE LOGOS
                $logoFile = 'mi-logo.png'; // Default
                if (isset($output->terminal->name)) {
                    if ($output->terminal->name == 'TRP') {
                        $logoFile = 'logo_trp.png';
                    } elseif ($output->terminal->name == 'TRVM') {
                        $logoFile = 'logo_trvm.png';
                    }
                }
                $logoPath = public_path('images/' . $logoFile);
            @endphp

            @if(file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Logo">
            @else
                <h2>{{ $output->terminal->name ?? 'TERMINAL' }}</h2>
            @endif
            
            <h1>Vale de Salida de Material</h1>
        </div>

        <p style="text-align: center;">
            <strong>Folio de Salida:</strong> {{ $output->id }} | 
            <strong>Terminal:</strong> {{ $output->terminal->name ?? 'N/A' }} | 
            <strong>Fecha de Registro:</strong> {{ $output->created_at->format('d/m/Y H:i') }}
        </p>

        <table class="details-table">
            <tr>
                <th>Fecha de Salida:</th>
                <td>{{ \Carbon\Carbon::parse($output->output_date)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Descripción del Material:</th>
                <td>{{ $output->description }}</td>
            </tr>
            <tr>
                <th>Tipo:</th>
                <td>{{ $output->material_type }}</td>
            </tr>
            <tr>
                <th>No. de Item:</th>
                <td>{{ $output->item_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Cantidad Retirada:</th>
                <td>{{ $output->quantity }} (unidades)</td>
            </tr>
            @if($output->consumable)
                <tr>
                    <th>Almacén Origen:</th>
                    <td>{{ $output->consumable->location?->name ?? 'Sin asignar' }}</td>
                </tr>
                @if($output->consumable->specific_location)
                    <tr>
                        <th>Ubicación Exacta:</th>
                        <td>📍 {{ $output->consumable->specific_location }}</td>
                    </tr>
                @endif
            @endif
            <tr>
                <th>Orden de Trabajo (OT):</th>
                <td>{{ $output->work_order ?? 'PENDIENTE' }}</td>
            </tr>
            <tr>
                <th>Confirmación SAP:</th>
                <td>{{ $output->sap_confirmation ?? 'PENDIENTE' }}</td>
            </tr>
            <tr>
                <th>Status Actual:</th>
                <td><strong>{{ $output->status }}</strong></td>
            </tr>
        </table>

        <div class="signatures">
            <div class="signature-box" style="float: left;">
                <img src="{{ $output->receiver_signature }}" alt="Firma Recibe">
                <p>Firma de Quien Recibe<br>
                   <strong>{{ $output->receiver_name }}</strong>
                </p>
            </div>

            <div class="signature-box" style="float: right;">
                <img src="{{ $output->deliverer_signature }}" alt="Firma Entrega">
                <p>Firma de Quien Entrega<br>
                   <strong>{{ $output->user->name ?? 'N/A' }}</strong>
                </p>
            </div>
        </div>

    </div>
</body>
</html>