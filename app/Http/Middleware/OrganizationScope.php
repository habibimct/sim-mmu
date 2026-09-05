<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizationScope
{
    /**
     * Membatasi akses berdasarkan organisasi user.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Belum login
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            abort(401);
        }

        /*
        |--------------------------------------------------------------------------
        | Admin Sistem boleh mengakses seluruh organisasi
        |--------------------------------------------------------------------------
        */

        if ($user->hasPermission('organizations.manage')) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | User harus mempunyai organisasi
        |--------------------------------------------------------------------------
        */

        if (!$user->organizations()->exists()) {
            abort(403, 'User belum memiliki organisasi.');
        }

        return $next($request);
    }
}
