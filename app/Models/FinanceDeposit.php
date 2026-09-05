<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceDeposit extends Model
{
    protected $fillable = [
        'organization_id',
        'target_organization_id',
        'deposit_date',
        'amount',
        'payment_method',
        'description',
        'proof_path',
        'proof_original_name',
        'proof_size',
        'status',
        'created_by',
        'confirmed_by',
        'confirmed_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'deposit_date' => 'date',
            'amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class
        );
    }

    public function targetOrganization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class,
            'target_organization_id'
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
}
