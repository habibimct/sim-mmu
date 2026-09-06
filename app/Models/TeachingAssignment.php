<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TeachingAssignment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'organization_id',
        'teacher_id',
        'school_class_id',
        'subject_id',
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
            ->useLogName('teaching_assignment');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class
        );
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(
            Teacher::class
        );
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(
            SchoolClass::class
        );
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(
            Subject::class
        );
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(
            Attendance::class
        );
    }
}
