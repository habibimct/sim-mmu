<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PaymentAllocation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'payment_id',
        'student_bill_id',
        'amount',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('payment_allocation');
    }

    /**
     * Pembayaran.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            Payment::class
        );
    }

    /**
     * Tagihan siswa.
     */
    public function studentBill(): BelongsTo
    {
        return $this->belongsTo(
            StudentBill::class
        );
    }
}
