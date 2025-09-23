<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Invoice extends Model
{
    use HasFactory;

    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'invoice_date',
        'total_amount',
        'request_id',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the request that owns the invoice.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'request_id', 'request_id');
    }

    /**
     * Get the donation that owns the invoice.
     */
    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class, 'donation_id', 'donation_id');
    }

}
