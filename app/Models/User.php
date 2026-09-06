<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Role yang dimiliki user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Organisasi/unit tempat user bertugas.
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class);
    }

    /**
     * Data guru yang terkait dengan akun user.
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(
            Teacher::class,
            'user_id',
            'id'
        );
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('code', $permission);
            })
            ->exists();
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()
            ->where('code', $role)
            ->exists();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'email',
                'status',
                'is_active',
            ])
            ->logOnlyDirty()
            ->useLogName('user');
    }
}
