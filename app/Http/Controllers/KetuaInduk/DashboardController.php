<?php

namespace App\Http\Controllers\KetuaInduk;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard Ketua INDUK.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless(
            $user->is_active
                && $user->can('dashboard.view')
                && $user->roles()
                ->whereIn('code', [
                    'ketua_induk',
                    'pengurus_induk',
                ])
                ->exists(),
            403
        );

        $induk = $user
            ->organizations()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->first();

        abort_unless(
            $induk,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Unit di bawah INDUK
        |--------------------------------------------------------------------------
        */

        $units = Organization::query()
            ->where(
                'parent_id',
                $induk->id
            )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Notifikasi terbaru
        |--------------------------------------------------------------------------
        */

        $notifications = $user
            ->notifications()
            ->latest()
            ->take(5)
            ->get();

        return view(
            'ketua-induk.dashboard',
            compact(
                'induk',
                'units',
                'notifications'
            )
        );
    }
}
