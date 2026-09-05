<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FinanceDeposit;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Daftar notifikasi user.
     */
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        if ($request->routeIs('admin.notifications.index')) {

            return view(
                'admin.notifications.index',
                compact('notifications')
            );
        }

        if ($request->routeIs('kepala-unit.notifications.index')) {

            return view(
                'kepala-unit.notifications.index',
                compact('notifications')
            );
        }

        if ($request->routeIs('ketua-induk.notifications.index')) {

            return view(
                'ketua-induk.notifications.index',
                compact('notifications')
            );
        }

        return view(
            'notifications.index',
            compact('notifications')
        );
    }

    /**
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(
        Request $request,
        DatabaseNotification $notification
    ): RedirectResponse {
        abort_unless(
            $notification->notifiable_id === $request->user()->id
                && $notification->notifiable_type === $request->user()->getMorphClass(),
            403
        );

        $notification->markAsRead();

        return redirect()
            ->back()
            ->with('success', 'Notifikasi telah ditandai sebagai sudah dibaca.');
    }

    /**
     * Menandai seluruh notifikasi sebagai sudah dibaca.
     */
    public function markAllAsRead(
        Request $request
    ): RedirectResponse {
        $request->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Semua notifikasi telah ditandai sebagai sudah dibaca.'
            );
    }

    /**
     * Menampilkan slip setoran dari notifikasi.
     */
    public function depositProof(
        Request $request,
        DatabaseNotification $notification
    ): StreamedResponse {

        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Notifikasi harus milik user yang sedang login
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $notification->notifiable_id === $user->id
                && $notification->notifiable_type ===
                $user->getMorphClass(),
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Jenis notifikasi yang boleh membuka slip
    |--------------------------------------------------------------------------
    */

        $data = $notification->data;

        abort_unless(
            in_array(
                $data['type'] ?? null,
                [
                    'finance_deposit_created',
                    'finance_deposit_confirmed',
                    'finance_deposit_rejected',
                ],
                true
            ),
            404
        );

        /*
    |--------------------------------------------------------------------------
    | Ambil setoran
    |--------------------------------------------------------------------------
    */

        $depositId =
            $data['deposit_id'] ?? null;

        abort_unless(
            $depositId,
            404
        );

        $deposit = FinanceDeposit::findOrFail(
            $depositId
        );

        /*
    |--------------------------------------------------------------------------
    | Otorisasi berdasarkan FinanceDepositPolicy
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $user->can(
                'view',
                $deposit
            ),
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Pastikan ada file bukti
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $deposit->proof_path,
            404
        );

        abort_unless(
            Storage::disk('public')
                ->exists(
                    $deposit->proof_path
                ),
            404
        );

        /*
    |--------------------------------------------------------------------------
    | Tandai notifikasi sudah dibaca
    |--------------------------------------------------------------------------
    */

        $notification->markAsRead();

        /*
    |--------------------------------------------------------------------------
    | Tampilkan file
    |--------------------------------------------------------------------------
    */

        return Storage::disk('public')->response(
            $deposit->proof_path,
            $deposit->proof_original_name
        );
    }
}
