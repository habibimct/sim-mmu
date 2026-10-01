<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Guardian extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'organization_id',
        'name',
        'nik',
        'phone',
        'initial_password',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Akun user milik wali.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Siswa yang menjadi tanggung jawab wali.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'student_guardians'
        )->withPivot([
            'relationship',
            'is_primary',
        ])->withTimestamps();
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'organization_id',
                'name',
                'nik',
                'phone',
                'is_active',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('guardian');
    }
}
