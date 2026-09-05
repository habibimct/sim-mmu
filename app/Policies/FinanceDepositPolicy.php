<?php

namespace App\Policies;

use App\Models\FinanceDeposit;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class FinanceDepositPolicy
{
    /**
     * Melihat daftar setoran.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active
            && $user->can('finance.view');
    }

    /**
     * Melihat satu setoran.
     */
    public function view(
        User $user,
        FinanceDeposit $deposit
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.view')) {
            return false;
        }

        $organizationIds = $user
            ->organizations()
            ->where('is_active', true)
            ->pluck('organizations.id');

        /*
    |--------------------------------------------------------------------------
    | User berada pada organisasi pengirim
    |--------------------------------------------------------------------------
    */

        if (
            $organizationIds->contains(
                $deposit->organization_id
            )
        ) {
            return true;
        }

        /*
    |--------------------------------------------------------------------------
    | Ketua INDUK dapat melihat setoran
    | dari unit yang menyetor ke organisasinya.
    |--------------------------------------------------------------------------
    */

        return $user
            ->organizations()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->whereKey(
                $deposit->target_organization_id
            )
            ->exists();
    }

    /**
     * Membuat setoran.
     *
     * Hanya user yang memiliki finance.manage.
     */
    public function create(User $user): bool
    {
        return $user->is_active
            && $user->can('finance.manage');
    }

    /**
     * Mengubah setoran.
     *
     * Untuk keamanan, setelah dibuat setoran tidak kita edit
     * melalui policy umum.
     */
    public function update(
        User $user,
        FinanceDeposit $deposit
    ): bool {
        return false;
    }

    /**
     * Menghapus setoran.
     */
    public function delete(
        User $user,
        FinanceDeposit $deposit
    ): bool {
        return false;
    }

    /**
     * Mengonfirmasi setoran.
     *
     * Hanya parent yang boleh melakukan konfirmasi.
     */
    public function confirm(
        User $user,
        FinanceDeposit $deposit
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.manage')) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Hanya setoran yang masih pending
        |--------------------------------------------------------------------------
        */

        if ($deposit->status !== 'pending') {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | User harus berada pada organisasi parent
        |--------------------------------------------------------------------------
        */

        return $user
            ->organizations()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->whereKey(
                $deposit->target_organization_id
            )
            ->exists();
    }

    /**
     * Menolak setoran.
     */
    public function reject(
        User $user,
        FinanceDeposit $deposit
    ): bool {
        return $this->confirm(
            $user,
            $deposit
        );
    }
}
