<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Consumable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'terminal_id',
        'sku',
        'name',
        'description',
        'category',
        'unit_of_measure',
        'current_stock',
        'min_stock',
        'max_stock',
        'unit_cost',
        'location_id',
        'image_path',
        'barcode',
        'is_active',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'max_stock' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate barcode on creation if not provided
        static::creating(function ($consumable) {
            if (empty($consumable->barcode)) {
                $consumable->barcode = 'BAR-' . strtoupper(Str::random(10));
            }
        });
    }

    /**
     * Relación: Pertenece a una Terminal
     */
    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    /**
     * Relación: Pertenece a una Ubicación
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
    }

    /**
     * Relación: Tiene muchos Movimientos
     */
    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Agregar stock al inventario
     */
    public function addStock(float $quantity, string $referenceType = null, int $referenceId = null, string $notes = null): InventoryMovement
    {
        $previousStock = $this->current_stock;
        $this->current_stock += $quantity;
        $this->save();

        return $this->movements()->create([
            'terminal_id' => $this->terminal_id,
            'movement_type' => 'ENTRADA',
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $this->current_stock,
            'unit_cost' => $this->unit_cost,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Remover stock del inventario
     */
    public function removeStock(float $quantity, string $referenceType = null, int $referenceId = null, string $notes = null): InventoryMovement
    {
        if ($this->current_stock < $quantity) {
            throw new \Exception("Stock insuficiente. Disponible: {$this->current_stock}, Solicitado: {$quantity}");
        }

        $previousStock = $this->current_stock;
        $this->current_stock -= $quantity;
        $this->save();

        return $this->movements()->create([
            'terminal_id' => $this->terminal_id,
            'movement_type' => 'SALIDA',
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $this->current_stock,
            'unit_cost' => $this->unit_cost,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Ajustar stock manualmente (inventario físico)
     */
    public function adjustStock(float $newStock, string $notes = null): InventoryMovement
    {
        $previousStock = $this->current_stock;
        $difference = $newStock - $previousStock;
        $this->current_stock = $newStock;
        $this->save();

        return $this->movements()->create([
            'terminal_id' => $this->terminal_id,
            'movement_type' => $difference >= 0 ? 'AJUSTE_POSITIVO' : 'AJUSTE_NEGATIVO',
            'quantity' => abs($difference),
            'previous_stock' => $previousStock,
            'new_stock' => $this->current_stock,
            'unit_cost' => $this->unit_cost,
            'notes' => $notes,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Verifica si el stock está por debajo del mínimo
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }

    /**
     * Verifica si el stock está por encima del máximo
     */
    public function isOverStock(): bool
    {
        return $this->max_stock && $this->current_stock >= $this->max_stock;
    }

    /**
     * Obtiene el porcentaje de stock actual vs máximo
     */
    public function getStockPercentageAttribute(): float
    {
        if (!$this->max_stock || $this->max_stock == 0) {
            return 0;
        }
        return ($this->current_stock / $this->max_stock) * 100;
    }

    /**
     * Scope para productos con stock bajo
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock');
    }

    /**
     * Scope para productos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
