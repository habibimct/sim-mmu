<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentPaymentCancelledNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Payment $payment,
        public string $cancellationReason,
        public string $cancelledByName,
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
                'student_payment_cancelled',

            'title' =>
                'Pembayaran Dibatalkan',

            'message' =>
                'Pembayaran '
                . $this->payment->payment_number
                . ' sebesar Rp'
                . number_format(
                    (float) $this->payment->amount,
                    0,
                    ',',
                    '.'
                )
                . ' telah dibatalkan.',

            'payment_id' =>
                $this->payment->id,

            'payment_number' =>
                $this->payment->payment_number,

            'amount' =>
                (float) $this->payment->amount,

            'cancellation_reason' =>
                $this->cancellationReason,

            'cancelled_by' =>
                $this->cancelledByName,

            'cancelled_at' =>
                now()->toDateTimeString(),

            'organization_id' =>
                $this->payment->organization_id,

        ];
    }
}
