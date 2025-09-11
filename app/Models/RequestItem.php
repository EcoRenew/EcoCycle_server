<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestItem extends Model
{
    use HasFactory;

    protected $primaryKey = ['request_id', 'material_id'];
    public $incrementing = false;

    protected $fillable = [
        'request_id',
        'material_id',
        'quantity',
        'calculated_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'calculated_price' => 'decimal:2',
    ];

    /**
     * Get the request that owns the request item.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'request_id', 'request_id');
    }

    /**
     * Get the material for the request item.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }
}