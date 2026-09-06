<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AttendanceDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'attendance_id',
        'student_academic_year_id',
        'status',
        'notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->useLogName('attendance_detail');
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(
            Attendance::class
        );
    }

    public function studentAcademicYear(): BelongsTo
    {
        return $this->belongsTo(
            StudentAcademicYear::class
        );
    }
}
