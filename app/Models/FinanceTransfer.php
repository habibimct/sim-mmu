<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FinanceTransfer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'from_organization_id',
        'to_organization_id',
        'transfer_date',
        'amount',
        'payment_method',
        'description',
        'status',
        'created_by',
        'confirmed_by',
        'confirmed_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'transfer_date' => 'date',
            'amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('finance_transfer');
    }

    public function fromOrganization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class,
            'from_organization_id'
        );
    }

    public function toOrganization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class,
            'to_organization_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'confirmed_by'
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(
            FinanceTransferAttachment::class
        );
    }
}
