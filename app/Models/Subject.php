<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Subject extends Model
{
    use LogsActivity;

    protected $fillable = [
        'organization_id',
        'code',
        'name',
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
            ->dontSubmitEmptyLogs()
            ->useLogName('subject');
    }

    /*
    |--------------------------------------------------------------------------
    | Organisasi
    |--------------------------------------------------------------------------
    */

    public function organization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class
        );
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(
            Subject::class
        );
    }

    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(
            TeachingAssignment::class
        );
    }
}
