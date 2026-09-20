<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AttendanceDetail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Attendance extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'sync_id',
        'organization_id',
        'teaching_assignment_id',
        'date',
        'meeting_number',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'meeting_number' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->useLogName('attendance');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function teachingAssignment(): BelongsTo
    {
        return $this->belongsTo(
            TeachingAssignment::class
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            AttendanceDetail::class
        );
    }
}
