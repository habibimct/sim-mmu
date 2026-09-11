<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentAcademicYear;
use App\Models\StudentBill;
use App\Models\User;
use App\Models\Organization;
use App\Notifications\PaymentPendingNotification;
use App\Notifications\StudentPaymentConfirmedNotification;
use App\Notifications\StudentPaymentCancelledNotification;
use App\Models\FinanceTransaction;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Daftar pembayaran siswa.
     */
    public function index(
        Request $request
    ): View {

        $user = $request->user();

        Gate::authorize(
            'viewAny',
            Payment::class
        );


        /*
| Organisasi yang menjadi kewenangan user
*/

        $organizationIds = $user
            ->organizations()->where(
                'organizations.is_active',
                true
            )->whereNotNull('organizations.parent_id')->pluck('organizations.id');


        /*
| Filter
*/

        $status = $request->input('status');

        $paymentMethod = $request->input('payment_method');

        $dateFrom = $request->input('date_from');

        $dateTo = $request->input('date_to');

        $search = $request->input('search');


        /*
| Validasi status
*/

        $allowedStatuses = [
            'pending',
            'confirmed',
            'failed',
            'cancelled',
        ];

        if (
            $status !== null
            && ! in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            $status = null;
        }


        /*
| Validasi metode pembayaran
*/

        $allowedPaymentMethods = [
            'cash',
            'bank_transfer',
            'online',
        ];

        if (
            $paymentMethod !== null
            && ! in_array(
                $paymentMethod,
                $allowedPaymentMethods,
                true
            )
        ) {
            $paymentMethod = null;
        }


        /*
| Query pembayaran
*/

        $query = Payment::query()
            ->with(['organization', 'creator', 'confirmer', 'allocations.studentBill', 'allocations.studentBill.studentAcademicYear.student',])
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->when($status, fn($query) => $query->where(
                'status',
                $status
            ))
            ->when($paymentMethod, fn($query) => $query->where(
                'payment_method',
                $paymentMethod
            ))
            ->when($dateFrom, fn($query) => $query->whereDate(
                'payment_date',
                '>=',
                $dateFrom
            ))
            ->when($dateTo, fn($query) => $query->whereDate(
                'payment_date',
                '<=',
                $dateTo
            ))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('payment_number', 'like', '%' . $search . '%')
                        ->orWhere('provider_transaction_id', 'like', '%' . $search . '%')
                        ->orWhere('provider_order_id', 'like', '%' . $search . '%');
                });
            });


        /*
| Ringkasan
*/

        $summaryQuery = clone $query;


        $totalPayments = (clone $summaryQuery)->count();

        $totalAmount = (clone $summaryQuery)->sum('amount');


        $pendingPayments = (clone $summaryQuery)->where('status', 'pending')->count();

        $pendingAmount = (clone $summaryQuery)->where('status', 'pending')->sum('amount');


        $confirmedPayments = (clone $summaryQuery)->where('status', 'confirmed')->count();

        $confirmedAmount = (clone $summaryQuery)->where('status', 'confirmed')->sum('amount');


        $failedPayments = (clone $summaryQuery)->where('status', 'failed')->count();

        $failedAmount = (clone $summaryQuery)->where('status', 'failed')->sum('amount');


        $cancelledPayments = (clone $summaryQuery)->where('status', 'cancelled')->count();

        $cancelledAmount = (clone $summaryQuery)->where('status', 'cancelled')->sum('amount');


        /*
| Daftar pembayaran
*/

        $payments = $query
            ->orderByDesc('payment_date')->orderByDesc('id')->paginate(15)->withQueryString();


        /*
| Siswa aktif untuk Create Payment
*/

        $studentAcademicYears = StudentAcademicYear::query()->with(['student', 'academicYear', 'organization', 'schoolClass',])->whereIn(
            'organization_id',
            $organizationIds
        )->where('status', 'active')->orderBy('academic_year_id')->orderBy('student_id')->get();


        return view(
            'admin.finance.payments.index',
            compact(
                'payments',
                'status',
                'paymentMethod',
                'dateFrom',
                'dateTo',
                'search',
                'totalPayments',
                'totalAmount',
                'pendingPayments',
                'pendingAmount',
                'confirmedPayments',
                'confirmedAmount',
                'failedPayments',
                'failedAmount',
                'cancelledPayments',
                'cancelledAmount',
                'studentAcademicYears'
            )
        );
    }

    /**
     * Mengambil tagihan siswa yang masih dapat dibayar.
     */
    public function studentBills(
        Request $request,
        StudentAcademicYear $studentAcademicYear
    ) {

        $user = $request->user();

        Gate::authorize(
            'create',
            Payment::class
        );


        /*
| Organisasi yang menjadi kewenangan user
*/

        $organizationIds = $user
            ->organizations()->where(
                'organizations.is_active',
                true
            )->whereNotNull('organizations.parent_id')->pluck('organizations.id');


        /*
| Pastikan StudentAcademicYear milik unit user
*/

        if (! $organizationIds->contains(
            (int) $studentAcademicYear->organization_id
        )) {
            abort(403, 'Siswa bukan berada pada unit yang menjadi kewenangan Anda.');
        }


        /*
| Ambil tagihan
*/

        $studentBills = StudentBill::query()
            ->with(['billType', 'paymentAllocations.payment',])
            ->where(
                'student_academic_year_id',
                $studentAcademicYear->id
            )
            ->whereIn('status', ['unpaid', 'partial',])
            ->orderBy('due_date')
            ->orderBy('id')
            ->get();


        /*
| Kembalikan data JSON
*/

        return response()->json(
            $studentBills->map(function ($studentBill) {
                /* Total Payment confirmed
*/
                $confirmedAmount = $studentBill
                    ->paymentAllocations
                    ->filter(function ($allocation) {
                        return $allocation
                            ->payment
                            ?->status === 'confirmed';
                    })->sum('amount');

                /* Total Payment pending
*/
                $pendingAmount = $studentBill
                    ->paymentAllocations
                    ->filter(function ($allocation) {
                        return $allocation
                            ->payment
                            ?->status === 'pending';
                    })->sum('amount');

                /* Sisa yang masih dapat dibayar
| Pending juga mengurangi sisa karena pembayaran
| tersebut sudah mengambil alokasi tagihan.|*/
                $remainingAmount = max(
                    0,
                    (float) $studentBill->amount
                        - (float) $confirmedAmount
                        - (float) $pendingAmount
                );

                /* Apakah ada Payment pending?*/
                $hasPendingPayment = $pendingAmount > 0;

                return [
                    'id' => $studentBill->id,
                    'bill_type' => $studentBill
                        ->billType
                        ?->name,
                    'period' => $studentBill->period,
                    'amount' => (float) $studentBill->amount,
                    'paid_amount' => (float) $confirmedAmount,
                    'pending_amount' => (float) $pendingAmount,
                    'remaining_amount' => (float) $remainingAmount,
                    'has_pending_payment' => $hasPendingPayment,
                    'due_date' => $studentBill->due_date
                        ?->format('Y-m-d'),
                    'status' => $studentBill->status,
                ];
            })
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {

        $user = $request->user();

        Gate::authorize(
            'create',
            Payment::class
        );


        /*
| Organisasi yang menjadi kewenangan user
*/

        $organizationIds = $user
            ->organizations()->where(
                'organizations.is_active',
                true
            )->whereNotNull('organizations.parent_id')->pluck('organizations.id');


        if ($organizationIds->isEmpty()) {
            abort(403, 'Anda tidak memiliki unit yang dapat mengelola pembayaran.');
        }


        /*
| Untuk Bendahara Unit gunakan unit pertama
*/

        $organizationId = (int) $organizationIds->first();


        /*
| Validasi input
*/

        $validated = $request->validate([
            'student_academic_year_id' => ['required', 'integer', 'exists:student_academic_years,id',],
            'amount' => ['required', 'numeric', 'min:0.01',],
            'payment_method' => ['required', 'in:cash,bank_transfer,online',],
            'payment_date' => ['required', 'date',],
            'description' => ['nullable', 'string', 'max:1000',],
            'bill_ids' => ['required', 'array', 'min:1',],
            'bill_ids.*' => ['integer', 'distinct', 'exists:student_bills,id',],

        ]);


        /*
| Pastikan StudentAcademicYear milik unit user
*/

        $studentAcademicYear = StudentAcademicYear::query()->findOrFail($validated['student_academic_year_id']);


        if (
            (int) $studentAcademicYear->organization_id
            !== $organizationId
        ) {
            abort(403, 'Siswa bukan berasal dari unit Anda.');
        }


        if (
            $studentAcademicYear->status !== 'active'
        ) {
            return back()->withInput()->withErrors(['student_academic_year_id' => 'Siswa tidak berstatus aktif.',]);
        }


        /*
| Status Payment
|
| Semua Payment yang dibuat dari halaman ini
| harus menunggu konfirmasi Kepala Unit.
|
*/

        $paymentStatus = 'pending';

        $paymentProvider = null;


        if (
            $validated['payment_method'] === 'online'
        ) {
            $paymentProvider = 'midtrans';
        }


        /*
| Simpan Payment + Allocation
|
| StudentBill dikunci di dalam transaction.
|
| Ini penting untuk mencegah dua request membuat
| Payment terhadap tagihan yang sama secara bersamaan.
|
*/

        $payment = DB::transaction(
            function () use (
                $validated,
                $organizationId,
                $paymentStatus,
                $paymentProvider,
                $studentAcademicYear,
                $user
            ) {
                /* Ambil dan kunci tagihan
*/
                $studentBills = StudentBill::query()->with(['paymentAllocations.payment',])->where(
                    'student_academic_year_id',
                    $studentAcademicYear->id
                )->whereIn('id', $validated['bill_ids'])->whereIn('status', ['unpaid', 'partial',])->lockForUpdate()->get();

                /* Pastikan semua tagihan ditemukan
*/
                if ($studentBills->count() !== count($validated['bill_ids'])) {
                    throw ValidationException::withMessages(['bill_ids' => 'Sebagian tagihan yang dipilih tidak dapat dibayar.',]);
                }

                /* Hitung sisa setiap tagihan
| Pending dan confirmed sama-sama dianggap
| sebagai alokasi aktif.|*/
                $billRemainingAmounts = [];
                $totalRemaining = 0;

                foreach (
                    $studentBills
                    as $studentBill
                ) {
                    $activeAllocatedAmount = $studentBill
                        ->paymentAllocations
                        ->filter(function ($allocation) {
                            return in_array(
                                $allocation->payment?->status,
                                ['pending', 'confirmed',],
                                true
                            );
                        })->sum('amount');

                    /* Sisa tagihan
*/
                    $remainingAmount = max(
                        0,
                        (float) $studentBill->amount
                            - (float) $activeAllocatedAmount
                    );

                    /* Tagihan sudah tidak tersedia
*/
                    if (
                        $remainingAmount <= 0
                    ) {
                        throw ValidationException::withMessages(['bill_ids' => 'Salah satu tagihan sudah memiliki pembayaran yang sedang diproses atau sudah lunas.',]);
                    }

                    $billRemainingAmounts[$studentBill->id] = $remainingAmount;

                    $totalRemaining += $remainingAmount;
                }

                /* Validasi nominal Payment
*/
                $paymentAmount = (float) $validated['amount'];

                if (
                    $paymentAmount > $totalRemaining
                ) {
                    throw ValidationException::withMessages(['amount' => 'Nominal pembayaran melebihi total sisa tagihan yang dipilih.',]);
                }

                /* Nomor pembayaran
*/
                $paymentNumber = 'PAY-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));

                /* Buat Payment
*/
                $payment = Payment::create([
                    'organization_id' => $organizationId,
                    'payment_number' => $paymentNumber,
                    'payment_date' => $validated['payment_date'],
                    'amount' => $paymentAmount,
                    'payment_method' => $validated['payment_method'],
                    'payment_provider' => $paymentProvider,
                    'status' => $paymentStatus,
                    'description' => $validated['description'] ?? null,
                    'created_by' => $user->id,
                ]);

                /* Alokasi pembayaran
*/
                $remainingPayment = $paymentAmount;

                foreach (
                    $studentBills
                    as $studentBill
                ) {
                    if (
                        $remainingPayment <= 0
                    ) {
                        break;
                    }

                    $allocationAmount = min($remainingPayment, $billRemainingAmounts[$studentBill->id]);

                    if (
                        $allocationAmount <= 0
                    ) {
                        continue;
                    }

                    $payment
                        ->allocations()->create([
                            'student_bill_id' => $studentBill->id,
                            'amount' => $allocationAmount,
                        ]);

                    $remainingPayment -= $allocationAmount;
                }

                /* Pastikan seluruh nominal Payment sudah dialokasikan
*/
                if (
                    $remainingPayment > 0
                ) {
                    throw ValidationException::withMessages(['amount' => 'Nominal pembayaran tidak dapat dialokasikan seluruhnya ke tagihan yang dipilih.',]);
                }

                /* Kembalikan Payment dari transaction
*/
                return $payment;
            }
        );


        /*
| Notifikasi kepada Kepala Unit
*/

        $kepalaUnitUsers = User::query()->whereHas('roles', function ($query) {
            $query->where('code', 'kepala_unit');
        })->whereHas('organizations', function ($query) use ($payment) {
            $query->where(
                'organizations.id',
                $payment->organization_id
            );
        })->get();


        foreach (
            $kepalaUnitUsers
            as $kepalaUnit
        ) {
            $kepalaUnit->notify(new PaymentPendingNotification(
                $payment
            ));
        }


        /*
| Kembali ke daftar
*/

        return redirect()->route('admin.finance.payments.index')->with('success', 'Pembayaran berhasil dicatat dengan nomor ' . $payment->payment_number
            . '. Status pembayaran: Menunggu konfirmasi.');
    }


    public function detail(
        Request $request,
        Payment $payment
    ) {

        Gate::authorize(
            'view',
            $payment
        );


        $payment->load([
            'organization',
            'creator',
            'confirmer',
            'allocations.studentBill.billType',
            'allocations.studentBill.studentAcademicYear.student',
            'allocations.studentBill.studentAcademicYear.academicYear',
            'allocations.studentBill.studentAcademicYear.schoolClass',
        ]);


        return response()->json([
            'id' => $payment->id,
            'payment_number' => $payment->payment_number,
            'status' => $payment->status,
            'payment_date' => $payment->payment_date
                ?->format('d/m/Y H:i'),
            'payment_method' => $payment->payment_method,
            'amount' => (float) $payment->amount,
            'description' => $payment->description,
            'organization' => $payment->organization?->name,
            'creator' => $payment->creator?->name,
            'confirmer' => $payment->confirmer?->name,
            'allocations' => $payment->allocations
                ->map(function ($allocation) {
                    $bill = $allocation->studentBill;
                    $studentAcademicYear = $bill?->studentAcademicYear;
                    $student = $studentAcademicYear?->student;
                    return [
                        'student_name' => $student?->name,
                        'nis' => $student?->nis,
                        'bill_type' => $bill?->billType?->name,
                        'period' => $bill?->period,
                        'amount' => (float) $allocation->amount,
                    ];
                })->values(),

        ]);
    }


    public function confirm(
        Request $request,
        Payment $payment
    ) {
        $user = $request->user();

        Gate::authorize(
            'confirm',
            $payment
        );


        if (
            $payment->status !== 'pending'
        ) {
            return response()->json(['message' => 'Pembayaran sudah tidak berstatus menunggu konfirmasi.',], 422);
        }


        DB::transaction(function () use (
            $payment,
            $user
        ) {
            /*
| Konfirmasi Payment
*/
            $payment->update([
                'status' => 'confirmed',
                'confirmed_by' => $user->id,
                'confirmed_at' => now(),
            ]);

            /*
| Ambil seluruh alokasi
*/
            $payment->load(['allocations.studentBill',]);

            /*
| Perbarui status setiap StudentBill
*/
            foreach (
                $payment->allocations
                as $allocation
            ) {
                $studentBill = $allocation->studentBill;

                if (! $studentBill) {
                    continue;
                }

                /* Hitung seluruh pembayaran yang sudah confirmed
*/
                $paidAmount = $studentBill
                    ->paymentAllocations()->whereHas('payment', function ($query) {
                        $query->where('status', 'confirmed');
                    })->sum('amount');

                /* Tentukan status tagihan
*/
                if (
                    $paidAmount >= $studentBill->amount
                ) {
                    $studentBill->update(['status' => 'paid',]);
                } elseif (
                    $paidAmount > 0
                ) {
                    $studentBill->update(['status' => 'partial',]);
                } else {
                    $studentBill->update(['status' => 'unpaid',]);
                }
            }
        });


        /*
| Buat transaksi pemasukan Unit
*/

        $transaction = FinanceTransaction::create([
            'organization_id' => $payment->organization_id,
            'transaction_date' => $payment->payment_date,
            'type' => 'income',
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'category' => 'Pembayaran Siswa',
            'source_type' => 'student_payment',
            'payment_id' => $payment->id,
            'created_by' => $user->id,
            'description' => 'Pemasukan pembayaran siswa - ' . $payment->payment_number,
            'status' => 'confirmed',
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),

        ]);


        $recipients = User::query()->whereHas('roles', function ($query) {
            $query->where('code', 'bendahara_unit');
        })->whereHas('organizations', function ($query) use ($payment) {
            $query->where(
                'organizations.id',
                $payment->organization_id
            );
        })->get();


        $kepalaUnitUsers = User::query()->whereHas('roles', function ($query) {
            $query->where('code', 'kepala_unit');
        })->whereHas('organizations', function ($query) use ($payment) {
            $query->where(
                'organizations.id',
                $payment->organization_id
            );
        })->get();

        $recipients = $recipients
            ->merge($kepalaUnitUsers)->unique('id');


        foreach ($recipients as $recipient) {
            $recipient->notify(new StudentPaymentConfirmedNotification(
                $payment,
                $transaction
            ));
        }


        return response()->json([
            'message' => 'Pembayaran berhasil dikonfirmasi dan status tagihan telah diperbarui.',
        ]);
    }


    public function cancel(
        Request $request,
        Payment $payment
    ) {
        $user = $request->user();

        Gate::authorize(
            'cancel',
            $payment
        );


        /*
| Pastikan Payment masih confirmed
*/

        if (
            $payment->status !== 'confirmed'
        ) {
            return response()->json(['message' => 'Pembayaran ini tidak dapat dibatalkan.',], 422);
        }


        /*
| Alasan pembatalan
*/

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:1000',],
        ]);


        DB::transaction(function () use (
            $payment,
            $user,
            $validated
        ) {
            /*
| Batalkan Payment
*/
            $payment->update(['status' => 'cancelled',]);

            /*
| Batalkan FinanceTransaction
*/
            $transaction = $payment->financeTransaction;

            if (
                $transaction
                && $transaction->status === 'confirmed'
            ) {
                $transaction->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $validated['cancellation_reason'],
                    'cancelled_by' => $user->id,
                    'cancelled_at' => now(),
                ]);
            }

            /*
| Ambil alokasi
*/
            $payment->load(['allocations.studentBill',]);

            /*
| Hitung ulang StudentBill
*/
            foreach (
                $payment->allocations
                as $allocation
            ) {
                $studentBill = $allocation->studentBill;

                if (! $studentBill) {
                    continue;
                }

                $paidAmount = $studentBill
                    ->paymentAllocations()->whereHas('payment', function ($query) {
                        $query->where('status', 'confirmed');
                    })->sum('amount');

                if (
                    $paidAmount >= $studentBill->amount
                ) {
                    $studentBill->update(['status' => 'paid',]);
                } elseif (
                    $paidAmount > 0
                ) {
                    $studentBill->update(['status' => 'partial',]);
                } else {
                    $studentBill->update(['status' => 'unpaid',]);
                }
            }
        });


        /*
| Kirim notifikasi pembatalan
*/

        $payment->load([
            'creator',
        ]);

        $recipients = collect();


        /*
| Bendahara Unit
*/

        $bendaharaUsers = User::query()->whereHas('roles', function ($query) {
            $query->where('code', 'bendahara_unit');
        })->whereHas('organizations', function ($query) use ($payment) {
            $query->where(
                'organizations.id',
                $payment->organization_id
            );
        })->get();

        $recipients = $recipients
            ->merge($bendaharaUsers);


        /*
| Kepala Unit
*/

        $kepalaUnitUsers = User::query()->whereHas('roles', function ($query) {
            $query->where('code', 'kepala_unit');
        })->whereHas('organizations', function ($query) use ($payment) {
            $query->where(
                'organizations.id',
                $payment->organization_id
            );
        })->get();

        $recipients = $recipients
            ->merge($kepalaUnitUsers)->unique('id');


        /*
| Kirim
*/

        foreach ($recipients as $recipient) {
            $recipient->notify(new StudentPaymentCancelledNotification($payment, $validated['cancellation_reason'], $user->name,));
        }


        return response()->json([
            'message' => 'Konfirmasi pembayaran berhasil dibatalkan.',
        ]);
    }
}
