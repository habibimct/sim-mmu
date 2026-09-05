<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class PaymentPendingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Payment $payment
    ) {}

    /**
     * Channel notifikasi.
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
            'payment_pending',

        'title' =>
            'Pembayaran Menunggu Konfirmasi',

        'message' =>
            'Ada pembayaran siswa yang menunggu konfirmasi Anda.',

        'payment_id' =>
            $this->payment->id,

        'payment_number' =>
            $this->payment->payment_number,

        'amount' =>
            (float) $this->payment->amount,

        'payment_date' =>
            $this->payment->payment_date?->format('Y-m-d H:i:s'),

        'payment_method' =>
            $this->payment->payment_method,

        'organization_id' =>
            $this->payment->organization_id,

    ];
}
}
