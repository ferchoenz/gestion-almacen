<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryExit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'terminal_id',
        'user_id',
        'consumable_id',
        'quantity',
        'exit_date',
        'department',
        'recipient_name',
        'purpose',
        'notes',
        'reference_document',
    ];

    protected $casts = [
        'exit_date' => 'date',
        'quantity' => 'decimal:2',
    ];

    /**
     * Relación: Una salida pertenece a un Usuario (quien registró).
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

    /**
     * Relación: Una salida pertenece a un Consumible.
     */
    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class);
    }
}
