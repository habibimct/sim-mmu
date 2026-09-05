<?php

namespace App\Notifications;

use App\Models\FinanceTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FinanceTransactionCreated extends Notification
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
            'type' =>
                'finance_transaction_created',

            'title' =>
                $this->transaction->type === 'income'
                    ? 'Pemasukan Keuangan'
                    : 'Pengeluaran Keuangan',

            'message' =>
                $this->transaction->organization->name
                . ' mencatat '
                . (
                    $this->transaction->type === 'income'
                        ? 'pemasukan'
                        : 'pengeluaran'
                )
                . ' sebesar Rp '
                . number_format(
                    $this->transaction->amount,
                    0,
                    ',',
                    '.'
                )
                . '.',

            'transaction_id' =>
                $this->transaction->id,

            'organization_id' =>
                $this->transaction->organization_id,

            'transaction_type' =>
                $this->transaction->type,

            'amount' =>
                $this->transaction->amount,

            'category' =>
                $this->transaction->category,

            'transaction_date' =>
                $this->transaction->transaction_date,
        ];
    }
}
