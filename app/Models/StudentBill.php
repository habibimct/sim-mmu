<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StudentBill extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'student_academic_year_id',
        'bill_type_id',
        'period',
        'amount',
        'due_date',
        'status',
        'description',

        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'cancelled_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('student_bill');
    }

    /**
     * Tahun akademik siswa.
     */
    public function studentAcademicYear(): BelongsTo
    {
        return $this->belongsTo(
            StudentAcademicYear::class
        );
    }

    /**
     * Jenis tagihan.
     */
    public function billType(): BelongsTo
    {
        return $this->belongsTo(
            BillType::class
        );
    }

    /**
     * User yang membatalkan tagihan.
     */
    public function canceller(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'cancelled_by'
        );
    }

    /**
     * Alokasi pembayaran untuk tagihan ini.
     */
    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(
            PaymentAllocation::class
        );
    }

    /**
     * Total pembayaran yang sudah dikonfirmasi.
     */
    public function getPaidAmountAttribute(): float
    {
        return (float) $this
            ->paymentAllocations()
            ->whereHas(
                'payment',
                function ($query) {
                    $query->where(
                        'status',
                        'confirmed'
                    );
                }
            )
            ->sum('amount');
    }

    /**
     * Sisa tagihan yang masih harus dibayar.
     */
    public function getRemainingAmountAttribute(): float
    {
        $remaining =
            (float) $this->amount
            -
            $this->paid_amount;

        return max(
            0,
            $remaining
        );
    }
}
