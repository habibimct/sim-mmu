<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Teacher extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'nik',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'phone',
        'email',
        'initial_password',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Organisasi/unit tempat guru bertugas.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(
            Organization::class,
            'organization_teacher'
        );
    }

    /**
     * ID organisasi yang boleh diakses user saat ini.
     */
    public static function organizationIdsForUser()
    {
        return Organization::accessibleIdsForUser();
    }

    /**
     * Hanya guru yang memiliki relasi
     * dengan organisasi yang dapat diakses user saat ini.
     */
    public function scopeForCurrentUser(Builder $query): Builder
    {
        return $query->whereHas(
            'organizations',
            function (Builder $organizationQuery) {
                $organizationQuery->whereIn(
                    'organizations.id',
                    static::organizationIdsForUser()
                );
            }
        );
    }

    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(
            TeachingAssignment::class
        );
    }

    public function attendances(): HasManyThrough
    {
        return $this->hasManyThrough(
            Attendance::class,
            TeachingAssignment::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->useLogName('teacher');
    }
}
