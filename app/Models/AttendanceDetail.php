<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'student_academic_year_id',
        'status',
        'notes',
    ];

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
