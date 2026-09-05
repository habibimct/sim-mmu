<?php

namespace App\Notifications;

use App\Models\FinanceDeposit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FinanceDepositRejected extends Notification
{
    use Queueable;

    public function __construct(
        public FinanceDeposit $deposit
    ) {
    }

    /**
     * Channel notifikasi.
     */
    public function via(object $notifiable): array
    {
        return [
            'database',
        ];
    }

    /**
     * Data notifikasi yang disimpan ke database.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'finance_deposit_rejected',

            'title' => 'Setoran Unit Ditolak',

            'message' =>
                'Setoran dari '
                . $this->deposit->organization->name
                . ' sebesar Rp '
                . number_format(
                    $this->deposit->amount,
                    0,
                    ',',
                    '.'
                )
                . ' telah ditolak.',

            'deposit_id' =>
                $this->deposit->id,

            'organization_id' =>
                $this->deposit->organization_id,

            'target_organization_id' =>
                $this->deposit->target_organization_id,

            'amount' =>
                $this->deposit->amount,

            'rejection_reason' =>
                $this->deposit->rejection_reason,
        ];
    }
}
