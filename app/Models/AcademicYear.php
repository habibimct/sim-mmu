<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Kelas yang berada pada tahun ajaran ini.
     */
    public function schoolClasses(): HasMany
    {
        return $this->hasMany(
            SchoolClass::class
        );
    }

    /**
     * Riwayat siswa pada tahun ajaran ini.
     */
    public function studentAcademicYears(): HasMany
    {
        return $this->hasMany(
            StudentAcademicYear::class
        );
    }
}
