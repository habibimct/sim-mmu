<?php

namespace App\Models;

use App\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Student extends Model
{
    use HasFactory, BelongsToOrganization, LogsActivity;

    protected $fillable = [
        'organization_id',
        'nis',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'class_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->useLogName('student');
    }

    /**
     * Riwayat siswa berdasarkan tahun ajaran.
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(
            StudentAcademicYear::class
        );
    }

    /**
     * Riwayat siswa berdasarkan tahun ajaran.
     */
    public function studentAcademicYears(): HasMany
    {
        return $this->hasMany(
            StudentAcademicYear::class
        );
    }
}
