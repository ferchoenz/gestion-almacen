<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialReception extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los campos que se pueden llenar masivamente.
     * (Todos los campos de tu formulario)
     */
    protected $fillable = [
        'terminal_id',
        'user_id',
        'material_type',
        'item_number',
        'description',
        'provider',
        'purchase_order',
        'reception_date',
        'quantity',
        'quality_certificate',
        'sap_confirmation',
        'storage_location',
        'status',
        'cancellation_reason',
        // Rutas de archivos adjuntos
        'invoice_path',
        'remission_path',
        'certificate_path',
    ];

    /**
     * Define los "casts" de tipos.
     * Le decimos a Laravel que 'quality_certificate' es un Booleano (true/false).
     */
    protected $casts = [
        'quality_certificate' => 'boolean',
        'reception_date' => 'date',
    ];


    /**
     * Relación: Una recepción pertenece a un Usuario (el que registró).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Una recepción pertenece a una Terminal.
     */
    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }
}