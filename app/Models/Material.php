<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    protected $primaryKey = 'material_id';

    protected $fillable = [
        'material_name',
        'description',
        'price_per_unit',
        'default_unit',   // use this instead of `unit`
        'units',          // JSON -> cast to array
        'image_url',
        'category_id',
        'points_per_kg',
        'stock',
    ];

    protected $casts = [
        'units' => 'array',
        'price_per_unit' => 'decimal:2',
        'points_per_kg' => 'float',
        'stock' => 'float',    ];

    /**
     * Get the category that owns the material.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_materials', 'material_id', 'product_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Get the request items for this material.
     */
    public function requestItems(): HasMany
    {
        return $this->hasMany(RequestItem::class, 'material_id', 'material_id');
    }
}