<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StudentAcademicYear extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'organization_id',
        'school_class_id',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('student_academic_year');
    }

    /**
     * Siswa.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(
            Student::class
        );
    }

    /**
     * Tahun ajaran.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(
            AcademicYear::class
        );
    }

    /**
     * Organisasi / unit siswa pada periode ini.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class
        );
    }

    /**
     * Kelas siswa pada periode ini.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(
            SchoolClass::class,
            'school_class_id'
        );
    }

    /**
     * Tagihan siswa pada tahun akademik ini.
     */
    public function studentBills(): HasMany
    {
        return $this->hasMany(
            StudentBill::class
        );
    }

    public function attendanceDetails(): HasMany
    {
        return $this->hasMany(
            AttendanceDetail::class
        );
    }
}
