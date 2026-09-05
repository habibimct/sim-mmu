<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Models\FinanceTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentPaymentConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Payment $payment,
        public FinanceTransaction $transaction
    ) {
    }

    /**
     * Kanal notifikasi.
     */
    public function via(
        object $notifiable
    ): array {

        return [
            'database',
        ];
    }

    /**
     * Data notifikasi database.
     */
    public function toDatabase(
        object $notifiable
    ): array {

        return [

            'type' =>
                'student_payment_confirmed',

            'title' =>
                'Pembayaran Telah Dikonfirmasi',

            'message' =>
                'Pembayaran '
                . $this->payment->payment_number
                . ' telah dikonfirmasi dan dicatat sebagai pemasukan Unit.',

            'payment_id' =>
                $this->payment->id,

            'payment_number' =>
                $this->payment->payment_number,

            'transaction_id' =>
                $this->transaction->id,

            'amount' =>
                (float) $this->payment->amount,

            'organization_id' =>
                $this->payment->organization_id,

        ];
    }
}
