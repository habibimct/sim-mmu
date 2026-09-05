<?php

namespace App\Support;

use App\Models\FinanceTransaction;
use App\Models\FinanceDeposit;
use App\Models\User;
use Illuminate\Support\Collection;

class FinanceNotificationRecipients
{
    /**
     * Mendapatkan penerima notifikasi untuk suatu transaksi.
     */
    /**
     * Mendapatkan penerima notifikasi untuk suatu transaksi.
     */
    public static function forTransaction(
        FinanceTransaction $transaction
    ): Collection {

        $organization = $transaction->organization;

        /*
    |--------------------------------------------------------------------------
    | Transaksi normal pada unit
    |--------------------------------------------------------------------------
    |
    | Notifikasi hanya untuk Bendahara Unit dan Kepala Unit
    | dari unit yang memiliki transaksi.
    |
    */

        if (
            $organization->parent_id !== null
        ) {

            return User::query()
                ->where(
                    'is_active',
                    true
                )
                ->whereHas(
                    'organizations',
                    function ($query) use ($organization) {

                        $query->where(
                            'organizations.id',
                            $organization->id
                        );
                    }
                )
                ->whereHas(
                    'roles',
                    function ($query) {

                        $query->whereIn(
                            'code',
                            [
                                'bendahara_unit',
                                'kepala_unit',
                            ]
                        );
                    }
                )
                ->get()
                ->unique('id')
                ->values();
        }


        /*
    |--------------------------------------------------------------------------
    | Transaksi pada organisasi parent
    |--------------------------------------------------------------------------
    |
    | Untuk sementara, gunakan pengguna yang memiliki organisasi parent
    | dan role keuangan yang relevan.
    |
    */

        return User::query()
            ->where(
                'is_active',
                true
            )
            ->whereHas(
                'organizations',
                function ($query) use ($organization) {

                    $query->where(
                        'organizations.id',
                        $organization->id
                    );
                }
            )
            ->whereHas(
                'roles',
                function ($query) {

                    $query->whereIn(
                        'code',
                        [
                            'keuangan_induk',
                            'ketua_induk',
                        ]
                    );
                }
            )
            ->get()
            ->unique('id')
            ->values();
    }



    /**
     * Mendapatkan penerima notifikasi untuk setoran unit.
     */
    public static function forDeposit(
        FinanceDeposit $deposit
    ): Collection {
        $deposit->loadMissing([
            'organization',
            'targetOrganization',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Kepala Unit
    |--------------------------------------------------------------------------
    */

        $unitHeads = User::query()
            ->where('is_active', true)
            ->whereHas('organizations', function ($query) use ($deposit) {
                $query->where(
                    'organizations.id',
                    $deposit->organization_id
                );
            })
            ->whereHas('roles', function ($query) {
                $query->where(
                    'code',
                    'kepala_unit'
                );
            })
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Ketua INDUK
    |--------------------------------------------------------------------------
    */

        $indukChairs = User::query()
            ->where('is_active', true)
            ->whereHas('organizations', function ($query) use ($deposit) {
                $query->where(
                    'organizations.id',
                    $deposit->target_organization_id
                );
            })
            ->whereHas('roles', function ($query) {
                $query->where(
                    'code',
                    'ketua_induk'
                );
            })
            ->get();

        return $unitHeads
            ->merge($indukChairs)
            ->unique('id')
            ->values();
    }



    /**
     * Mendapatkan Bendahara Unit untuk suatu organisasi.
     */
    public static function unitTreasurers(
        int $organizationId
    ): Collection {
        return User::query()
            ->where('is_active', true)
            ->whereHas('organizations', function ($query) use ($organizationId) {
                $query->where(
                    'organizations.id',
                    $organizationId
                );
            })
            ->whereHas('roles', function ($query) {
                $query->where(
                    'code',
                    'bendahara_unit'
                );
            })
            ->get();
    }
}
