<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeachingAssignment extends Model
{
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
