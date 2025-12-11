<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HazmatRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'terminal_id',
        'approver_id',
        'status',
        'rejection_reason',
        'entry_date',
        'trade_name',
        'chemical_name',
        'usage_area',
        'intended_use',
        'storage_location',
        'max_storage_quantity',
        'min_storage_quantity',
        'monthly_consumption',
        'is_sample',
        'is_import',
        'moc_id',
        'hds_path',
        'can_be_substituted',
        'hds_compliant',
        'has_training',
        'has_ppe',
        'has_containment',
        'moc_managed',
        'final_storage_location',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'is_sample' => 'boolean',
        'is_import' => 'boolean',
        'can_be_substituted' => 'boolean',
        'hds_compliant' => 'boolean',
        'has_training' => 'boolean',
        'has_ppe' => 'boolean',
        'has_containment' => 'boolean',
        'moc_managed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }
    
    // Helper para status color
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'APPROVED' => 'green',
            'REJECTED' => 'red',
            default => 'yellow',
        };
    }
}
