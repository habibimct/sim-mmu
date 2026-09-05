<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\BelongsToOrganization;

class FinanceTransaction extends Model
{
    use HasFactory, BelongsToOrganization, LogsActivity;

    protected $fillable = [
        'organization_id',
        'transaction_date',
        'type',
        'amount',
        'payment_method',
        'category',
        'source_type',
        'created_by',
        'description',

        'status',

        'cancellation_reason',
        'rejection_reason',
        'cancelled_by',
        'cancelled_at',
        'payment_id',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Organisasi/unit pemilik transaksi.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class
        );
    }

    /**
     * User yang membuat transaksi.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * User yang membatalkan transaksi.
     */
    public function canceller(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'cancelled_by'
        );
    }

    /**
     * Konfigurasi log aktivitas Spatie.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'organization_id',
                'transaction_date',
                'type',
                'amount',
                'payment_method',
                'category',
                'description',

                'status',
                'cancellation_reason',
                'rejection_reason',
                'cancelled_by',
                'cancelled_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            Payment::class
        );
    }
}
