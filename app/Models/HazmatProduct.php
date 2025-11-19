<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Importante

class HazmatProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'terminal_id', // <-- IMPORTANTE: Agregado para que se pueda guardar
        'product_name',
        'chemical_name',
        'cas_number',
        'location',
        'physical_state',
        'max_quantity',
        'department',
        'signal_word',
        'hazard_statements',
        'precautionary_statements',
        'pictograms',
        'hds_path',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'pictograms' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relación: Un material peligroso pertenece a una Terminal.
     * ESTA ES LA FUNCIÓN QUE FALTABA Y CAUSABA EL ERROR.
     */
    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }
}