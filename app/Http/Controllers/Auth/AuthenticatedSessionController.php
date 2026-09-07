<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $route = $this->dashboardRoute($request->user());

        return redirect()->route($route);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function redirectByRole($user): RedirectResponse
    {
        if (
            $user->hasRole('admin_sistem') ||
            $user->hasRole('keuangan_induk') ||
            $user->hasRole('bendahara_unit') ||
            $user->hasRole('admin_unit')
        ) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('ketua_induk')) {
            return redirect()->route('ketua-induk.dashboard');
        }

        if ($user->hasRole('kepala_unit')) {
            return redirect()->route('kepala-unit.dashboard');
        }

        abort(403, 'Anda belum memiliki dashboard yang sesuai.');
    }

    protected function dashboardRoute($user): string
    {
        /*
    |--------------------------------------------------------------------------
    | ADMIN AREA
    |--------------------------------------------------------------------------
    */

        if (
            $user->hasRole('admin_sistem') ||
            $user->hasRole('keuangan_induk') ||
            $user->hasRole('bendahara_unit') ||
            $user->hasRole('admin_unit')
        ) {
            return 'admin.dashboard';
        }


        /*
    |--------------------------------------------------------------------------
    | KETUA INDUK
    |--------------------------------------------------------------------------
    */

        if ($user->hasRole('ketua_induk')) {
            return 'ketua-induk.dashboard';
        }


        /*
    |--------------------------------------------------------------------------
    | KEPALA UNIT
    |--------------------------------------------------------------------------
    */

        if ($user->hasRole('kepala_unit')) {
            return 'kepala-unit.dashboard';
        }


        /*
    |--------------------------------------------------------------------------
    | GURU
    |--------------------------------------------------------------------------
    */

        if ($user->hasRole('guru')) {
            return 'guru.dashboard';
        }



        /*
        |--------------------------------------------------------------------------
        | BELUM MEMILIKI ROLE
        |--------------------------------------------------------------------------
        */

        if (! $user->roles()->exists()) {
            return 'waiting-approval';
        }

        /*
        |--------------------------------------------------------------------------
        | ROLE BELUM MEMILIKI DASHBOARD
        |--------------------------------------------------------------------------
        */

        abort(403, 'Anda belum memiliki dashboard yang sesuai.');
    }

    public function redirectToDashboard(): RedirectResponse
    {
        return redirect()->route(
            $this->dashboardRoute(request()->user())
        );
    }
}
