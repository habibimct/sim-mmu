<?php

namespace App\Models;

use App\BelongsToOrganization;
use App\Models\StudentBill;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BillType extends Model
{
    use HasFactory, BelongsToOrganization, LogsActivity;

    protected $fillable = [
        'organization_id',
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->useLogName('bill_type');
    }

    /**
     * Organisasi / unit pemilik jenis tagihan.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class
        );
    }

    /**
     * Tagihan siswa yang menggunakan jenis tagihan ini.
     */
    public function studentBills(): HasMany
    {
        return $this->hasMany(
            StudentBill::class,
            'bill_type_id'
        );
    }
}
