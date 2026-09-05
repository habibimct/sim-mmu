<?php

namespace App\Policies;

use App\Models\FinanceTransaction;
use App\Models\Organization;
use App\Models\User;

class FinanceTransactionPolicy
{
    /**
     * Melihat daftar transaksi.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active
            && $user->can('finance.view');
    }

    /**
     * Melihat transaksi tertentu.
     */
    public function view(
        User $user,
        FinanceTransaction $transaction
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.view')) {
            return false;
        }

        return $this->canViewOrganization(
            $user,
            $transaction->organization_id
        );
    }

    /**
     * Membuat transaksi baru.
     */
    public function create(User $user): bool
    {
        return $user->is_active
            && $user->can('finance.manage');
    }

    /**
     * Mengubah transaksi.
     */
    public function update(
        User $user,
        FinanceTransaction $transaction
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.manage')) {
            return false;
        }

        return $this->canManageOrganization(
            $user,
            $transaction->organization_id
        );
    }

    /**
     * Membatalkan transaksi.
     *
     * Syarat:
     *
     * 1. User aktif.
     * 2. Memiliki finance.manage.
     * 3. Berhak mengelola organisasi transaksi.
     * 4. Status transaksi masih confirmed.
     * 5. Belum lewat 1 x 24 jam sejak created_at.
     */
    public function cancel(
        User $user,
        FinanceTransaction $transaction
    ): bool {

        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.manage')) {
            return false;
        }

        if (
            ! $this->canManageOrganization(
                $user,
                $transaction->organization_id
            )
        ) {
            return false;
        }

        if ($transaction->source_type === 'deposit') {
            return false;
        }

        if (
            $transaction->status !== 'confirmed'
        ) {
            return false;
        }

        if (
            $transaction->created_at
            ->addDay()
            ->isPast()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Menghapus transaksi.
     */
    public function delete(
        User $user,
        FinanceTransaction $transaction
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.manage')) {
            return false;
        }

        return $this->canManageOrganization(
            $user,
            $transaction->organization_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ORGANIZATION ACCESS
    |--------------------------------------------------------------------------
    */

    /**
     * Apakah user boleh melihat transaksi
     * milik organisasi tertentu?
     *
     * Aturan:
     *
     * 1. Organisasi sendiri       -> boleh.
     * 2. User berada di parent    -> boleh melihat child.
     * 3. User berada di child     -> tidak boleh melihat sibling.
     */
    private function canViewOrganization(
        User $user,
        int $organizationId
    ): bool {
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
        | Jika user berada di organisasi parent,
        | maka boleh melihat child langsungnya.
        |--------------------------------------------------------------------------
        */

        foreach ($userOrganizations as $organization) {

            if ($organization->parent_id !== null) {
                continue;
            }

            $isChild = Organization::query()
                ->whereKey($organizationId)
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
     * Apakah user boleh mengelola transaksi
     * milik organisasi tertentu?
     *
     * Berbeda dengan view:
     *
     * Parent hanya boleh mengelola transaksi
     * milik parent itu sendiri.
     *
     * Parent TIDAK boleh mengelola transaksi child
     * hanya karena boleh melihatnya.
     */
    private function canManageOrganization(
        User $user,
        int $organizationId
    ): bool {
        return $user->organizations()
            ->whereKey($organizationId)
            ->exists();
    }
}
