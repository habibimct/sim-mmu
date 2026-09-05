<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user.
     */
    public function index(): View
    {
        $users = User::with([
            'roles',
            'organizations',
        ])
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan form tambah user.
     */
    public function create(): View
    {
        $roles = Role::where('is_active', true)
            ->orderBy('name')
            ->get();

        $organizations = Organization::where('is_active', true)
            ->orderByRaw("CASE WHEN type = 'INDUK' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact(
            'roles',
            'organizations'
        ));
    }

    /**
     * Menyimpan user baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],

            'organization_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'organization_ids.*' => [
                'integer',
                'exists:organizations,id',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Pastikan role yang dipilih masih aktif
    |--------------------------------------------------------------------------
    */

        $role = Role::where('id', $validated['role_id'])
            ->where('is_active', true)
            ->first();

        if (!$role) {
            return back()
                ->withInput()
                ->withErrors([
                    'role_id' => 'Role yang dipilih tidak aktif.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Pastikan organisasi yang dipilih masih aktif
    |--------------------------------------------------------------------------
    */

        $organizationIds = Organization::whereIn(
            'id',
            $validated['organization_ids']
        )
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();


        if (count($organizationIds) !== count($validated['organization_ids'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'organization_ids' => 'Terdapat organisasi yang tidak aktif atau tidak valid.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Buat User
    |--------------------------------------------------------------------------
    */

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => $validated['is_active'],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Hubungkan Role
    |--------------------------------------------------------------------------
    */

        $user->roles()->sync([
            $validated['role_id'],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Hubungkan Organisasi
    |--------------------------------------------------------------------------
    */

        $user->organizations()->sync($organizationIds);


        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit user.
     */
    public function edit(User $user): View
    {
        $user->load([
            'roles',
            'organizations',
        ]);

        $roles = Role::where('is_active', true)
            ->orderBy('name')
            ->get();

        $organizations = Organization::where('is_active', true)
            ->orderByRaw("CASE WHEN type = 'INDUK' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('admin.users.edit', compact(
            'user',
            'roles',
            'organizations'
        ));
    }

    /**
     * Memperbarui user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],

            'organization_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'organization_ids.*' => [
                'integer',
                'exists:organizations,id',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Pastikan role aktif
    |--------------------------------------------------------------------------
    */

        $role = Role::where('id', $validated['role_id'])
            ->where('is_active', true)
            ->first();

        if (!$role) {
            return back()
                ->withInput()
                ->withErrors([
                    'role_id' => 'Role yang dipilih tidak aktif.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Pastikan organisasi aktif
    |--------------------------------------------------------------------------
    */

        $organizationIds = Organization::whereIn(
            'id',
            $validated['organization_ids']
        )
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();


        if (count($organizationIds) !== count($validated['organization_ids'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'organization_ids' =>
                    'Terdapat organisasi yang tidak aktif atau tidak valid.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Update User
    |--------------------------------------------------------------------------
    */

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $validated['is_active'],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Update Role
    |--------------------------------------------------------------------------
    */

        $user->roles()->sync([
            $validated['role_id'],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Update Organisasi
    |--------------------------------------------------------------------------
    */

        $user->organizations()->sync($organizationIds);


        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }
}
