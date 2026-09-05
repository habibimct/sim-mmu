<?php

namespace App\Policies;

use App\Models\BillType;
use App\Models\Organization;
use App\Models\User;

class BillTypePolicy
{
    /**
     * Melihat daftar jenis tagihan.
     *
     * Hanya user yang memiliki organisasi unit
     * (parent_id != null) yang boleh mengakses.
     */
    public function viewAny(User $user): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.view')) {
            return false;
        }

        return $user->organizations()
            ->where(
                'organizations.is_active',
                true
            )
            ->whereNotNull(
                'organizations.parent_id'
            )
            ->exists();
    }


    /**
     * Melihat jenis tagihan tertentu.
     */
    public function view(
        User $user,
        BillType $billType
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.view')) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | BillType harus milik organisasi unit
        |--------------------------------------------------------------------------
        */

        if (
            $billType->organization?->parent_id === null
        ) {
            return false;
        }

        return $this->canAccessOrganization(
            $user,
            $billType->organization_id
        );
    }


    /**
     * Membuat jenis tagihan.
     */
    public function create(User $user): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.manage')) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | User harus memiliki minimal satu organisasi unit
        |--------------------------------------------------------------------------
        */

        return $user->organizations()
            ->where(
                'organizations.is_active',
                true
            )
            ->whereNotNull(
                'organizations.parent_id'
            )
            ->exists();
    }


    /**
     * Mengubah jenis tagihan.
     */
    public function update(
        User $user,
        BillType $billType
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.manage')) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Jenis tagihan milik organisasi induk tidak boleh diubah
        |--------------------------------------------------------------------------
        */

        if (
            $billType->organization?->parent_id === null
        ) {
            return false;
        }

        return $this->canManageOrganization(
            $user,
            $billType->organization_id
        );
    }


    /**
     * Menghapus jenis tagihan.
     */
    public function delete(
        User $user,
        BillType $billType
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.manage')) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Jenis tagihan milik organisasi induk tidak boleh dihapus
        |--------------------------------------------------------------------------
        */

        if (
            $billType->organization?->parent_id === null
        ) {
            return false;
        }

        return $this->canManageOrganization(
            $user,
            $billType->organization_id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ORGANIZATION ACCESS
    |--------------------------------------------------------------------------
    */

    /**
     * Apakah user boleh melihat jenis tagihan
     * milik organisasi tertentu?
     *
     * Aturan:
     *
     * 1. Organisasi induk tidak memiliki BillType.
     * 2. Unit sendiri boleh.
     * 3. Parent boleh melihat child.
     * 4. Child tidak boleh melihat sibling.
     */
    private function canAccessOrganization(
        User $user,
        int $organizationId
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Pastikan organisasi target adalah unit
        |--------------------------------------------------------------------------
        */

        $targetOrganization = Organization::query()
            ->find($organizationId);

        if (! $targetOrganization) {
            return false;
        }

        if (
            $targetOrganization->parent_id === null
        ) {
            return false;
        }


        $userOrganizations = $user->organizations;


        /*
        |--------------------------------------------------------------------------
        | Organisasi sendiri
        |--------------------------------------------------------------------------
        */

        if (
            $userOrganizations->contains(
                'id',
                $organizationId
            )
        ) {
            return true;
        }


        /*
        |--------------------------------------------------------------------------
        | Parent boleh melihat child langsung
        |--------------------------------------------------------------------------
        */

        foreach ($userOrganizations as $organization) {

            if (
                $organization->parent_id !== null
            ) {
                continue;
            }

            $isChild = Organization::query()
                ->whereKey(
                    $organizationId
                )
                ->where(
                    'parent_id',
                    $organization->id
                )
                ->exists();

            if ($isChild) {
                return true;
            }
        }


        return false;
    }


    /**
     * Apakah user boleh mengelola
     * jenis tagihan organisasi tertentu?
     *
     * Berbeda dengan view:
     *
     * Parent tidak boleh mengelola BillType child
     * hanya karena parent boleh melihat child.
     *
     * Pengelolaan hanya boleh dilakukan
     * oleh user yang memang berada pada unit tersebut.
     */
    private function canManageOrganization(
        User $user,
        int $organizationId
    ): bool {

        return $user->organizations()
            ->where(
                'organizations.id',
                $organizationId
            )
            ->where(
                'organizations.is_active',
                true
            )
            ->whereNotNull(
                'organizations.parent_id'
            )
            ->exists();
    }
}
