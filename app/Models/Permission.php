<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Permission extends Model
{
    use LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'module',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('permission');
    }

    /**
     * Role yang memiliki permission ini.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
