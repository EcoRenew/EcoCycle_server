<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'parent_category_id',
    ];

    /**
     * Get the parent category.
     */
    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_category_id', 'category_id');
    }

    /**
     * Get the child categories.
     */
    public function childCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_category_id', 'category_id');
    }

    /**
     * Get the materials for this category.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'category_id', 'category_id');
    }
}
