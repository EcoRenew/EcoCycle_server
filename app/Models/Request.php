<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'request_type',
        'status',
        'pickup_date',
        'pickup_address_id',
        'customer_id',
        'collector_id',
    ];

    protected $casts = [
        'pickup_date' => 'datetime',
    ];

    /**
     * Get the pickup address for the request.
     */
    public function pickupAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'pickup_address_id', 'address_id');
    }

    /**
     * Get the customer that made the request.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'user_id');
    }

    /**
     * Get the collector assigned to the request.
     */
    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collector_id', 'user_id');
    }

    /**
     * Get the request items for the request.
     */
    public function requestItems(): HasMany
    {
        return $this->hasMany(RequestItem::class, 'request_id', 'request_id');
    }

    /**
     * Get the invoice for the request.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'request_id', 'request_id');
    }
}