<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'payment_number',
        'payment_date',
        'amount',
        'payment_method',
        'payment_provider',
        'status',

        'provider_transaction_id',
        'provider_order_id',
        'provider_status',

        'description',

        'created_by',
        'confirmed_by',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    /**
     * Unit / organisasi penerima pembayaran.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class
        );
    }

    /**
     * Alokasi pembayaran ke tagihan.
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(
            PaymentAllocation::class
        );
    }

    /**
     * User yang mencatat pembayaran.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * User yang mengonfirmasi pembayaran.
     */
    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'confirmed_by'
        );
    }


    public function financeTransaction(): HasOne
    {
        return $this->hasOne(
            FinanceTransaction::class
        );
    }
}
