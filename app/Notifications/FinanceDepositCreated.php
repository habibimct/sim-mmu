<?php

namespace App\Notifications;

use App\Models\FinanceDeposit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FinanceDepositCreated extends Notification
{
    use Queueable;

    public function __construct(
        public FinanceDeposit $deposit
    ) {}

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
     * Data notifikasi.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' =>
            'finance_deposit_created',

            'title' =>
            'Setoran Unit Baru',

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
                . ' menunggu konfirmasi.',

            'deposit_id' =>
            $this->deposit->id,

            'organization_id' =>
            $this->deposit->organization_id,

            'target_organization_id' =>
            $this->deposit->target_organization_id,

            'amount' =>
            $this->deposit->amount,

            'payment_method' =>
            $this->deposit->payment_method,

            'description' =>
            $this->deposit->description,

            'deposit_date' =>
            $this->deposit->deposit_date?->toDateString(),

            'proof_path' =>
            $this->deposit->proof_path,

            'proof_original_name' =>
            $this->deposit->proof_original_name,
        ];
    }
}
