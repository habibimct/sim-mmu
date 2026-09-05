<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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
