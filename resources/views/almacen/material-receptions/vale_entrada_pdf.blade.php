<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vale de Entrada - {{ $reception->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-width: 250px; height: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; width: 30%; }
    </style>
</head>
<body>
    <div class="header">
        @php
            $logoFile = 'mi-logo.png'; 
            if (isset($reception->terminal->name)) {
                if ($reception->terminal->name == 'TRP') {
                    $logoFile = 'logo_trp.png';
                } elseif ($reception->terminal->name == 'TRVM') {
                    $logoFile = 'logo_trvm.png';
                }
            }
            $logoPath = public_path('images/' . $logoFile);
        @endphp

        @if(file_exists($logoPath))
            <img src="{{ $logoPath }}" alt="Logo">
        @else
            <h2>{{ $reception->terminal->name ?? 'TERMINAL' }}</h2>
        @endif

        <h1>Vale de Entrada de Material</h1>
    </div>
    <p style="text-align: center;">
        <strong>Folio:</strong> {{ $reception->id }} | 
        <strong>Terminal:</strong> {{ $reception->terminal->name ?? 'N/A' }} |
        <strong>Usuario:</strong> {{ $reception->user->name ?? 'N/A' }}
    </p>

    <table>
        <tr><th>Fecha de Recepción:</th><td>{{ $reception->reception_date->format('d/m/Y') }}</td></tr>
        <tr><th>Proveedor:</th><td>{{ $reception->provider }}</td></tr>
        <tr><th>Orden de Compra (OC):</th><td>{{ $reception->purchase_order }}</td></tr>
        <tr><th>Descripción:</th><td>{{ $reception->description }}</td></tr>
        <tr><th>Tipo:</th><td>{{ $reception->material_type }}</td></tr>
        <tr><th>No. de Item:</th><td>{{ $reception->item_number ?? 'N/A' }}</td></tr>
        <tr><th>Cantidad Recibida:</th><td>{{ $reception->quantity }}</td></tr>
        <tr><th>Certificado de Calidad:</th><td>{{ $reception->quality_certificate ? 'SÍ' : 'NO' }}</td></tr>
        <tr><th>Confirmación SAP:</th><td>{{ $reception->sap_confirmation ?? 'N/A' }}</td></tr>
        @if($reception->consumable)
            <tr><th>Almacén Destino:</th><td>{{ $reception->consumable->location?->name ?? 'Sin asignar' }}</td></tr>
            @if($reception->consumable->specific_location)
                <tr><th>Ubicación Exacta:</th><td>📍 {{ $reception->consumable->specific_location }}</td></tr>
            @endif
        @else
            <tr><th>Ubicación:</th><td>{{ $reception->storage_location ?? 'Pendiente' }}</td></tr>
        @endif
    </table>
</body>
</html>