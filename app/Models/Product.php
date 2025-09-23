<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'img', 'hover_img'];
    public function materials()
    {
        return $this->belongsToMany(Material::class, 'product_materials', 'product_id', 'material_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function cartProducts()
    {
        return $this->hasMany(CartProduct::class);
    }

}
