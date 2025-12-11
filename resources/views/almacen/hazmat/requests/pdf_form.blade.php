<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>TRP-PO-SS-132-F01</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid black; padding: 4px; vertical-align: top; }
        .header-table td { text-align: center; font-weight: bold; }
        .section-header { background-color: #d1d5db; font-weight: bold; text-align: center; }
        .label { font-weight: bold; width: 40%; background-color: #f3f4f6; }
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 8pt; text-align: right; }
        .check { font-family: DejaVu Sans; }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <table class="header-table">
        <tr>
            <td rowspan="2" style="width: 25%;">
                <!-- Logo Simulado -->
                <div style="font-size: 14pt; color: #cc0000;">SEMPRA</div>
                <div style="font-size: 8pt;">Infraestructura</div>
            </td>
            <td style="width: 50%; font-size: 12pt;">TRP-PO-SS-132-F01</td>
            <td style="width: 25%;">NO. DE PÁGINA:<br>1 de 2</td>
        </tr>
        <tr>
            <td>SOLICITUD DE AUTORIZACIÓN DE<br>INGRESO DE MATERIALES PELIGROSOS NUEVOS</td>
            <td>REV. 4</td>
        </tr>
    </table>

    <!-- Datos Área Solicitante -->
    <table>
        <tr class="section-header"><td colspan="4">DATOS DEL ÁREA SOLICITANTE</td></tr>
        <tr>
            <td class="label" style="width: 15%;">Área:</td>
            <td style="width: 35%;">{{ $r->terminal->name ?? 'N/A' }}</td>
            <td class="label" style="width: 20%;">Fecha de Solicitud:</td>
            <td style="width: 30%;">{{ $r->created_at->format('d/m/Y') }}</td>
        </tr>
    </table>

    <!-- Datos Material Peligroso -->
    <table>
        <tr class="section-header"><td colspan="2">DATOS DEL MATERIAL PELIGROSO</td></tr>
        <tr><td colspan="2" style="font-style: italic; font-size: 8pt; text-align: center;">El Requisitor deberá completar los datos del material</td></tr>
        
        <tr><td class="label">Fecha de Ingreso:</td><td>{{ $r->entry_date ? $r->entry_date->format('d/m/Y') : 'N/A' }}</td></tr>
        <tr><td class="label">Nombre Comercial / Fabricante:</td><td>{{ $r->trade_name }}</td></tr>
        <tr><td class="label">Nombre Químico:</td><td>{{ $r->chemical_name }}</td></tr>
        <tr><td class="label">Área en la que se utiliza:</td><td>{{ $r->usage_area }}</td></tr>
        <tr><td class="label">Descripción del uso previsto:</td><td>{{ $r->intended_use }}</td></tr>
        <tr><td class="label">Lugar(es) de almacenamiento:</td><td>{{ $r->storage_location }}</td></tr>
        <tr><td class="label">Cantidad máxima por almacenar:</td><td>{{ $r->max_storage_quantity }}</td></tr>
        <tr><td class="label">Cantidad mínima por almacenar:</td><td>{{ $r->min_storage_quantity }}</td></tr>
        <tr><td class="label">Consumo mensual:</td><td>{{ $r->monthly_consumption }}</td></tr>
        <tr><td class="label">¿Es muestra?</td><td>{{ $r->is_sample ? 'SÍ' : 'NO' }}</td></tr>
        <tr><td class="label">¿Es material de importación?</td><td>{{ $r->is_import ? 'SÍ' : 'NO' }}</td></tr>
        <tr><td class="label">MOC:</td><td>{{ $r->moc_id }}</td></tr>
    </table>

    <!-- Validación -->
    <table>
        <tr class="section-header"><td colspan="2">Validación (Llenado por Champion / Seguridad y Salud)</td></tr>
        
        <tr><td class="label">¿Se puede sustituir por otra sustancia de menor riesgo?</td><td>{{ $r->can_be_substituted ? 'SÍ' : 'NO' }}</td></tr>
        <tr><td class="label">¿La HDS es vigente y cumple con la NOM-018-STPS-Vigente?</td><td>{{ $r->hds_compliant ? 'SÍ' : 'NO' }}</td></tr>
        <tr><td class="label">¿Se tiene capacitación para el manejo del material?</td><td>{{ $r->has_training ? 'SÍ' : 'NO' }}</td></tr>
        <tr><td class="label">¿Se tiene el EPP adecuado para el manejo del material?</td><td>{{ $r->has_ppe ? 'SÍ' : 'NO' }}</td></tr>
        <tr><td class="label">¿Se tiene el material adecuado de contención de derrames?</td><td>{{ $r->has_containment ? 'SÍ' : 'NO' }}</td></tr>
        <tr><td class="label">¿El uso fue gestionado mediante control de cambios (MOC)?</td><td>{{ $r->moc_managed ? 'SÍ' : 'NO' }}</td></tr>
        <tr><td class="label">¿En dónde se debe almacenar la sustancia química?</td><td>{{ $r->final_storage_location }}</td></tr>
    </table>

    <div style="margin-top: 10px; margin-bottom: 20px;">
        <strong>Resultado:</strong> &nbsp;&nbsp;
        <span class="check" style="font-family: sans-serif;">{{ $r->status == 'APPROVED' ? '[ X ]' : '[   ]' }}</span> Aprobado &nbsp;&nbsp;&nbsp;&nbsp;
        <span class="check" style="font-family: sans-serif;">{{ $r->status == 'REJECTED' ? '[ X ]' : '[   ]' }}</span> Rechazado
        <br><br>
        <strong>Motivo de Rechazo:</strong> {{ $r->rejection_reason }}
    </div>

    <!-- Firmas -->
    <table style="page-break-inside: avoid;">
        <tr class="section-header"><td colspan="2">FIRMAS DE AUTORIZACIÓN</td></tr>
        
        <tr>
            <td style="width: 30%; height: 40px;">Solicitante:</td>
            <td>
                {{ $r->user->name }}<br>
                <span style="font-size: 8pt; color: gray;">Firma Digital (Usuario Sistema)</span>
            </td>
        </tr>
        <tr>
            <td style="height: 40px;">Líder de Seguridad y Salud:</td>
            <td>
                {{ $r->approver->name ?? '' }}<br>
                <span style="font-size: 8pt; color: gray;">Firma Digital (Autorizador)</span>
            </td>
        </tr>
    </table>

</body>
</html>
