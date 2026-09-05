<?php

namespace App\Notifications;

use App\Models\FinanceTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FinanceTransactionCancelled extends Notification
{
    use Queueable;

    public function __construct(
        public FinanceTransaction $transaction
    ) {
    }

    public function via(object $notifiable): array
    {
        return [
            'database',
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'finance_transaction_cancelled',

            'title' => 'Transaksi Keuangan Dibatalkan',

            'message' =>
                'Transaksi '
                . ($this->transaction->category ?? 'keuangan')
                . ' sebesar Rp '
                . number_format(
                    (float) $this->transaction->amount,
                    0,
                    ',',
                    '.'
                )
                . ' telah dibatalkan.',

            'transaction_id' =>
                $this->transaction->id,

            'organization_id' =>
                $this->transaction->organization_id,

            'amount' =>
                (float) $this->transaction->amount,

            'category' =>
                $this->transaction->category,

            'transaction_type' =>
                $this->transaction->type,

            'cancellation_reason' =>
                $this->transaction->cancellation_reason,

            'cancelled_by' =>
                $this->transaction->cancelled_by,

            'cancelled_at' =>
                $this->transaction->cancelled_at?->toDateTimeString(),
        ];
    }
}
