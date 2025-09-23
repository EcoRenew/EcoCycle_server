<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Donation extends Model
{
     protected $primaryKey = 'donation_id';

    protected $fillable = [
        'user_id',
        'pickup_address_id',
        'item_category',
        'condition',
        'description',
        'pickup_date',
        'additional_notes',
        'photos',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'photos' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function pickupAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'pickup_address_id', 'address_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'donation_id', 'donation_id');
    }

}
