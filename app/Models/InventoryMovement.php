<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumable_id',
        'terminal_id',
        'movement_type',
        'quantity',
        'previous_stock',
        'new_stock',
        'unit_cost',
        'reference_type',
        'reference_id',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'previous_stock' => 'decimal:2',
        'new_stock' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    /**
     * Relación: Pertenece a un Consumible
     */
    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class);
    }

    /**
     * Relación: Pertenece a una Terminal
     */
    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    /**
     * Relación: Registrado por un Usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación polimórfica al documento de referencia
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Obtiene el color del tipo de movimiento para UI
     */
    public function getMovementColorAttribute(): string
    {
        return match($this->movement_type) {
            'ENTRADA', 'AJUSTE_POSITIVO', 'TRANSFERENCIA_ENTRADA' => 'green',
            'SALIDA', 'AJUSTE_NEGATIVO', 'TRANSFERENCIA_SALIDA' => 'red',
            default => 'gray',
        };
    }

    /**
     * Obtiene el icono del tipo de movimiento
     */
    public function getMovementIconAttribute(): string
    {
        return match($this->movement_type) {
            'ENTRADA' => '📥',
            'SALIDA' => '📤',
            'AJUSTE_POSITIVO' => '➕',
            'AJUSTE_NEGATIVO' => '➖',
            'TRANSFERENCIA_SALIDA' => '🔄',
            'TRANSFERENCIA_ENTRADA' => '🔄',
            default => '📦',
        };
    }

    /**
     * Scope para filtrar por tipo de movimiento
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('movement_type', $type);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
