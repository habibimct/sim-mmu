<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfflineSyncRequest extends Model
{
    protected $fillable = [
        'sync_id',
        'type',
        'status',
    ];
}
