<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\StudentBill;
use App\Models\User;

class StudentBillPolicy
{
    /**
     * Melihat daftar tagihan siswa.
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
     * Melihat tagihan tertentu.
     */
    public function view(
        User $user,
        StudentBill $studentBill
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.view')) {
            return false;
        }

        return $this->canAccessBill(
            $user,
            $studentBill
        );
    }


    /**
     * Membuat tagihan siswa.
     */
    public function create(User $user): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.manage')) {
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
     * Mengubah tagihan siswa.
     */
    public function update(
        User $user,
        StudentBill $studentBill
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.manage')) {
            return false;
        }

        return $this->canAccessBill(
            $user,
            $studentBill,
            true
        );
    }


    /**
     * Menghapus tagihan siswa.
     */
    public function delete(
        User $user,
        StudentBill $studentBill
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->can('finance.manage')) {
            return false;
        }

        return $this->canAccessBill(
            $user,
            $studentBill,
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ORGANIZATION ACCESS
    |--------------------------------------------------------------------------
    */

    /**
     * Memastikan user memiliki akses
     * terhadap organisasi pemilik tagihan.
     */
    private function canAccessBill(
        User $user,
        StudentBill $studentBill,
        bool $manage = false
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Ambil organisasi dari BillType
        |--------------------------------------------------------------------------
        */

        $studentBill->loadMissing([
            'billType.organization',
        ]);

        $organization =
            $studentBill->billType?->organization;


        if (! $organization) {
            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | BillType harus milik organisasi unit
        |--------------------------------------------------------------------------
        */

        if (
            $organization->parent_id === null
        ) {
            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | Untuk pengelolaan:
        | user harus berada langsung pada unit tersebut.
        |--------------------------------------------------------------------------
        */

        if ($manage) {

            return $user->organizations()
                ->where(
                    'organizations.id',
                    $organization->id
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


        /*
        |--------------------------------------------------------------------------
        | Untuk melihat:
        | gunakan akses organisasi yang sama.
        |--------------------------------------------------------------------------
        */

        return $this->canViewOrganization(
            $user,
            $organization->id
        );
    }


    /**
     * Apakah user boleh melihat organisasi tertentu?
     */
    private function canViewOrganization(
        User $user,
        int $organizationId
    ): bool {

        $organization = Organization::query()
            ->find($organizationId);

        if (! $organization) {
            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | Organisasi induk tidak memiliki tagihan
        |--------------------------------------------------------------------------
        */

        if (
            $organization->parent_id === null
        ) {
            return false;
        }


        $userOrganizations =
            $user->organizations;


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
        | Parent boleh melihat child langsung.
        |--------------------------------------------------------------------------
        */

        foreach ($userOrganizations as $userOrganization) {

            if (
                $userOrganization->parent_id !== null
            ) {
                continue;
            }

            $isChild = Organization::query()
                ->whereKey(
                    $organizationId
                )
                ->where(
                    'parent_id',
                    $userOrganization->id
                )
                ->exists();

            if ($isChild) {
                return true;
            }
        }


        return false;
    }


    public function cancel(
        User $user,
        StudentBill $studentBill
    ): bool {

        return $user->can('bills.manage')
            && $studentBill->status === 'unpaid';
    }
}
