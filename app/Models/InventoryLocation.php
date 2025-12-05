<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'terminal_id',
        'code',
        'name',
        'aisle',
        'rack',
        'level',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relación: Pertenece a una Terminal
     */
    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    /**
     * Relación: Tiene muchos Consumibles
     */
    public function consumables(): HasMany
    {
        return $this->hasMany(Consumable::class, 'location_id');
    }

    /**
     * Obtiene el nombre completo de la ubicación
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->aisle, $this->rack, $this->level]);
        return $this->name . (count($parts) > 0 ? ' (' . implode('-', $parts) . ')' : '');
    }
}
