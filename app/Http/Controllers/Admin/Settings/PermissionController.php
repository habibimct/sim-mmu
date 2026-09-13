<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Menampilkan halaman pengaturan permission berdasarkan role.
     */
    public function index(Request $request)
    {
        $roles = Role::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedRole = null;
        $selectedPermissionIds = [];

        if ($roles->isNotEmpty()) {
            $roleId = $request->integer('role_id');

            $selectedRole = $roles->firstWhere('id', $roleId)
                ?? $roles->first();

            $selectedPermissionIds = $selectedRole
                ->permissions()
                ->pluck('permissions.id')
                ->toArray();
        }

        $permissions = Permission::query()
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        return view('admin.settings.permissions.index', [
            'roles' => $roles,
            'selectedRole' => $selectedRole,
            'permissions' => $permissions,
            'selectedPermissionIds' => $selectedPermissionIds,
        ]);
    }

    /**
     * Menyimpan perubahan permission untuk role yang dipilih.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
            'permissions' => [
                'nullable',
                'array',
            ],
            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        $role = Role::query()
            ->where('is_active', true)
            ->findOrFail($validated['role_id']);

        $permissionIds = $validated['permissions'] ?? [];

        /*
         * Jangan sampai Admin Sistem mencabut permission
         * settings.manage dari role yang sedang dipakainya sendiri.
         */
        if ($role->users()->where('users.id', Auth::id())->exists()) {
            $settingsPermissionId = Permission::where(
                'code',
                'settings.manage'
            )->value('id');

            if (
                $settingsPermissionId &&
                ! in_array($settingsPermissionId, $permissionIds)
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'permissions' =>
                            'Permission "Mengelola Pengaturan" tidak dapat dicabut dari role yang sedang Anda gunakan.',
                    ]);
            }
        }

        DB::transaction(function () use ($role, $permissionIds) {
            $role->permissions()->sync($permissionIds);
        });

        return redirect()
            ->route('admin.settings.permissions.index', [
                'role_id' => $role->id,
            ])
            ->with('success', 'Permission berhasil diperbarui.');
    }
}
