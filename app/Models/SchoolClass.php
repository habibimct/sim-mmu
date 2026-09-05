<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'school_classes';

    protected $fillable = [
        'organization_id',
        'academic_year_id',
        'level',
        'name',
        'is_alumni',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'is_alumni' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Unit pemilik kelas.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class
        );
    }

    /**
     * Tahun ajaran kelas.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(
            AcademicYear::class
        );
    }

    /**
     * Riwayat siswa yang berada di kelas ini.
     */
    public function studentAcademicYears(): HasMany
    {
        return $this->hasMany(
            StudentAcademicYear::class
        );
    }

    public function scopeRegular($query)
    {
        return $query->where(
            'is_alumni',
            false
        );
    }

    public function scopeAlumni($query)
    {
        return $query->where(
            'is_alumni',
            true
        );
    }

    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(
            TeachingAssignment::class
        );
    }
}
