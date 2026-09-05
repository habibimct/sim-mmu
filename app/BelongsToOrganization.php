<?php

namespace App;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    /**
     * Organisasi yang memiliki data ini.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * ID organisasi yang sedang digunakan user.
     */
    public static function organizationIdsForUser()
    {
        $user = auth()->user();

        if (!$user) {
            return collect();
        }

        /*
        |--------------------------------------------------------------------------
        | User dengan permission organizations.manage
        | dapat mengakses seluruh organisasi.
        |--------------------------------------------------------------------------
        */

        if ($user->hasPermission('organizations.manage')) {
            return Organization::query()
                ->where('is_active', true)
                ->pluck('id');
        }

        /*
        |--------------------------------------------------------------------------
        | User biasa hanya mendapatkan organisasi miliknya.
        |--------------------------------------------------------------------------
        */

        return $user->organizations()
            ->where('organizations.is_active', true)
            ->pluck('organizations.id');
    }

    /**
     * Query data berdasarkan organisasi user yang sedang login.
     */
    public function scopeForCurrentUser($query)
    {
        $ids = static::organizationIdsForUser();

        return $query->whereIn('organization_id', $ids);
    }
}
