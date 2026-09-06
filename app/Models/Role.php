<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends Model
{
    use LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('role');
    }

    /**
     * User yang memiliki role ini.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Permission yang dimiliki role ini.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
