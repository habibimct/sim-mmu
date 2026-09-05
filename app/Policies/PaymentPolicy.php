<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Melihat daftar pembayaran.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'payments.view'
        )
            && $user->organizations()
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
     * Melihat detail pembayaran.
     */
    public function view(
        User $user,
        Payment $payment
    ): bool {

        return $user->can(
            'payments.view'
        )
            && $user->organizations()
            ->where(
                'organizations.is_active',
                true
            )
            ->where(
                'organizations.id',
                $payment->organization_id
            )
            ->whereNotNull(
                'organizations.parent_id'
            )
            ->exists();
    }

    /**
     * Membuat pembayaran.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'payments.create'
        )
            && $user->organizations()
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
     * Mengubah pembayaran.
     */
    public function update(
        User $user,
        Payment $payment
    ): bool {

        return $user->can(
            'payments.create'
        )
            && $payment->status === 'pending'
            && $user->organizations()
            ->where(
                'organizations.is_active',
                true
            )
            ->where(
                'organizations.id',
                $payment->organization_id
            )
            ->whereNotNull(
                'organizations.parent_id'
            )
            ->exists();
    }


    /**
     * Mengonfirmasi pembayaran.
     */
    public function confirm(
        User $user,
        Payment $payment
    ): bool {

        return $user->can(
            'payments.confirm'
        )
            && $payment->status === 'pending'
            && $user->organizations()
            ->where(
                'organizations.is_active',
                true
            )
            ->where(
                'organizations.id',
                $payment->organization_id
            )
            ->whereNotNull(
                'organizations.parent_id'
            )
            ->exists();
    }


    /**
     * Membatalkan / membatalkan konfirmasi pembayaran.
     */
    public function cancel(
        User $user,
        Payment $payment
    ): bool {

        /*
    |--------------------------------------------------------------------------
    | Payment harus pending atau confirmed
    |--------------------------------------------------------------------------
    */

        if (
            ! in_array(
                $payment->status,
                [
                    'pending',
                    'confirmed',
                ],
                true
            )
        ) {

            return false;
        }


        /*
    |--------------------------------------------------------------------------
    | Harus Kepala Unit
    |--------------------------------------------------------------------------
    */

        $isKepalaUnit =
            $user
            ->roles()
            ->where(
                'code',
                'kepala_unit'
            )
            ->exists();

        if (
            ! $isKepalaUnit
        ) {

            return false;
        }


        /*
    |--------------------------------------------------------------------------
    | Harus memiliki kewenangan terhadap Unit Payment
    |--------------------------------------------------------------------------
    */

        return $user
            ->organizations()
            ->where(
                'organizations.id',
                $payment->organization_id
            )
            ->where(
                'organizations.is_active',
                true
            )
            ->exists();
    }
}
