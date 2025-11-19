<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Añade esto al inicio
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialOutput extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los campos que se pueden llenar masivamente.
     */
    protected $fillable = [
        'terminal_id',
        'user_id',
        'material_type',
        'item_number',
        'description',
        'output_date',
        'quantity',
        'receiver_name',
        'receiver_signature',
        'deliverer_signature',
        'work_order',
        'sap_confirmation',
        'status',
        'pdf_vale_path',
        'cancellation_reason',
    ];

    /**
     * Relación: Una salida pertenece a un Usuario (el que entregó).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Una salida pertenece a una Terminal.
     */
    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }
}