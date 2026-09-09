<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\Organization;
use App\Models\FinanceDeposit;
use App\Notifications\FinanceTransactionCreated;
use App\Notifications\FinanceDepositCreated;
use App\Support\FinanceNotificationRecipients;
use App\Notifications\FinanceTransactionCancelled;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceTransactionController extends Controller
{
    /**
     * Menampilkan daftar transaksi keuangan.
     */
    public function index(Request $request): View
    {
        $start = microtime(true);
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Organisasi yang boleh dilihat
        |--------------------------------------------------------------------------
        |
        | User boleh melihat:
        | - organisasi sendiri
        | - child langsung jika organisasinya adalah parent
        |
        */

        $viewOrganizationIds = $this->viewOrganizationIds($user);

        /*
        |--------------------------------------------------------------------------
        | Query transaksi
        |--------------------------------------------------------------------------
        */

        $query = FinanceTransaction::query()
            ->with([
                'organization',
                'creator',
            ])
            ->whereIn(
                'organization_id',
                $viewOrganizationIds
            );

        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'category',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'description',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter jenis transaksi
        |--------------------------------------------------------------------------
        */

        if ($request->filled('type')) {

            $query->where(
                'type',
                $request->type
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter organisasi
        |--------------------------------------------------------------------------
        */

        if ($request->filled('organization_id')) {

            $organizationId = (int) $request->organization_id;

            if (
                ! $viewOrganizationIds->contains(
                    $organizationId
                )
            ) {
                abort(403);
            }

            $query->where(
                'organization_id',
                $organizationId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter metode pembayaran
        |--------------------------------------------------------------------------
        */

        if ($request->filled('payment_method')) {

            $query->where(
                'payment_method',
                $request->payment_method
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Urutan transaksi
        |--------------------------------------------------------------------------
        */

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();


        $pendingDeposits = FinanceDeposit::query()
            ->with([
                'organization',
                'targetOrganization',
                'creator',
            ])
            ->whereIn(
                'organization_id',
                $viewOrganizationIds
            )
            ->where('status', 'pending')
            ->orderByDesc('deposit_date')
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Dropdown organisasi
        |--------------------------------------------------------------------------
        */

        $organizations = Organization::query()
            ->whereIn(
                'id',
                $viewOrganizationIds
            )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $summary = (clone $query)
            ->where('status', 'confirmed')
            ->reorder()
            ->selectRaw("
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
            ")
            ->first();

        $totalIncome = (float) $summary->total_income;
        $totalExpense = (float) $summary->total_expense;

        $netBalance = $totalIncome - $totalExpense;

        Log::info('Finance transactions index time', [
            'time' => round(microtime(true) - $start, 3),
        ]);

        return view(
            'admin.finance.transactions.index',
            compact(
                'transactions',
                'organizations',
                'pendingDeposits',
                'totalIncome',
                'totalExpense',
                'netBalance'
            )
        );
    }


    /**
     * Menyimpan transaksi baru.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Jenis transaksi
    |--------------------------------------------------------------------------
    |
    | income  = pemasukan biasa
    | expense = pengeluaran biasa
    | deposit = setoran unit ke parent
    |
    */

        $transactionKind = $request->input('transaction_kind');

        /*
    |--------------------------------------------------------------------------
    | Organisasi yang boleh dikelola
    |--------------------------------------------------------------------------
    */

        $manageOrganizationIds = $this->manageOrganizationIds($user);

        /*
    |--------------------------------------------------------------------------
    | Organisasi user
    |--------------------------------------------------------------------------
    */

        $userOrganization = $user
            ->organizations()
            ->where('is_active', true)
            ->first();

        if (! $userOrganization) {
            abort(403, 'Anda belum memiliki organisasi.');
        }

        $organizationId = $userOrganization->id;


        if (! $manageOrganizationIds->contains($organizationId)) {
            abort(403);
        }
        /*
    |--------------------------------------------------------------------------
    | Validasi dasar
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'transaction_kind' => [
                'required',
                'in:normal,deposit',
            ],

            'organization_id' => [
                'required',
                'integer',
                'exists:organizations,id',

                function ($attribute, $value, $fail) use ($manageOrganizationIds) {

                    if (
                        ! $manageOrganizationIds->contains(
                            (int) $value
                        )
                    ) {
                        $fail(
                            'Anda tidak memiliki kewenangan mengelola organisasi tersebut.'
                        );
                    }
                },
            ],

            'transaction_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'type' => [
                'nullable',
                'in:income,expense',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'payment_method' => [
                'required',
                'in:cash,bank_transfer,online',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            /*
        |--------------------------------------------------------------------------
        | Bukti hanya digunakan untuk setoran
        |--------------------------------------------------------------------------
        */

            'proof' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | SETORAN UNIT
    |--------------------------------------------------------------------------
    */

        if ($transactionKind === 'deposit') {

            /*
        |--------------------------------------------------------------------------
        | Organisasi pengirim
        |--------------------------------------------------------------------------
        */

            $organization = Organization::query()
                ->whereKey(
                    $validated['organization_id']
                )
                ->where('is_active', true)
                ->firstOrFail();

            /*
        |--------------------------------------------------------------------------
        | Pengirim tidak boleh parent
        |--------------------------------------------------------------------------
        */

            if ($organization->parent_id === null) {

                return back()
                    ->withErrors([
                        'organization_id' =>
                        'Organisasi parent tidak dapat membuat setoran kepada dirinya sendiri.',
                    ])
                    ->withInput();
            }

            /*
        |--------------------------------------------------------------------------
        | Cari parent otomatis
        |--------------------------------------------------------------------------
        */

            $parent = Organization::query()
                ->whereKey(
                    $organization->parent_id
                )
                ->where('is_active', true)
                ->first();

            if (! $parent) {

                return back()
                    ->withErrors([
                        'organization_id' =>
                        'Organisasi parent tidak ditemukan atau tidak aktif.',
                    ])
                    ->withInput();
            }

            /*
        |--------------------------------------------------------------------------
        | Bukti setoran wajib
        |--------------------------------------------------------------------------
        */

            if (! $request->hasFile('proof')) {

                return back()
                    ->withErrors([
                        'proof' =>
                        'Bukti setoran wajib diunggah.',
                    ])
                    ->withInput();
            }

            /*
        |--------------------------------------------------------------------------
        | Upload bukti
        |--------------------------------------------------------------------------
        |
        | Untuk sementara kita simpan file asli.
        | Kompresi gambar kita pasang setelah alur dasarnya
        | sudah berhasil.
        |
        */

            $proof = $request->file('proof');

            $proofPath = $proof->store(
                'finance/deposits',
                'public'
            );

            /*
        |--------------------------------------------------------------------------
        | Buat FinanceDeposit
        |--------------------------------------------------------------------------
        */

            $deposit = FinanceDeposit::create([
                'organization_id' =>
                $organization->id,

                'target_organization_id' =>
                $parent->id,

                'deposit_date' =>
                $validated['transaction_date'],

                'amount' =>
                $validated['amount'],

                'payment_method' =>
                $validated['payment_method'],

                'description' =>
                $validated['description'] ?? null,

                'proof_path' =>
                $proofPath,

                'proof_original_name' =>
                $proof->getClientOriginalName(),

                'proof_size' =>
                $proof->getSize(),

                'status' =>
                'pending',

                'created_by' =>
                $user->id,
            ]);

            /*
|--------------------------------------------------------------------------
| Notifikasi setoran
|--------------------------------------------------------------------------
*/

            $recipients =
                FinanceNotificationRecipients::forDeposit(
                    $deposit
                );

            foreach ($recipients as $recipient) {

                $recipient->notify(
                    new FinanceDepositCreated(
                        $deposit
                    )
                );
            }

            return redirect()
                ->route(
                    'admin.finance.transactions.index'
                )
                ->with(
                    'success',
                    'Setoran berhasil dibuat dan menunggu konfirmasi.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | TRANSAKSI BIASA
    |--------------------------------------------------------------------------
    */

        $validated['created_by'] = $user->id;

        /*
    |--------------------------------------------------------------------------
    | transaction_kind tidak disimpan ke FinanceTransaction
    |--------------------------------------------------------------------------
    */

        unset(
            $validated['transaction_kind'],
            $validated['proof']
        );

        /*
    |--------------------------------------------------------------------------
    | Category wajib untuk transaksi biasa
    |--------------------------------------------------------------------------
    */

        if (empty($validated['category'])) {

            return back()
                ->withErrors([
                    'category' =>
                    'Kategori wajib diisi untuk transaksi biasa.',
                ])
                ->withInput();
        }

        $transaction = FinanceTransaction::create(
            $validated
        );

        /*
|--------------------------------------------------------------------------
| Notifikasi transaksi
|--------------------------------------------------------------------------
*/

        $recipients =
            FinanceNotificationRecipients::forTransaction(
                $transaction
            );

        foreach ($recipients as $recipient) {

            $recipient->notify(
                new FinanceTransactionCreated(
                    $transaction
                )
            );
        }

        return redirect()
            ->route(
                'admin.finance.transactions.index'
            )
            ->with(
                'success',
                'Transaksi keuangan berhasil ditambahkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ORGANIZATION SCOPE
    |--------------------------------------------------------------------------
    */

    /**
     * Organisasi yang boleh dilihat user.
     *
     * Jika user berada di parent:
     * parent + seluruh child langsung.
     *
     * Jika user berada di child:
     * child sendiri saja.
     */
    private function viewOrganizationIds($user)
    {
        $organizations = $user
            ->organizations()
            ->where('is_active', true)
            ->get();

        $ids = collect();

        foreach ($organizations as $organization) {

            /*
            | Organisasi sendiri
            */

            $ids->push(
                $organization->id
            );

            /*
            | Jika parent, tambahkan child.
            */

            if ($organization->parent_id === null) {

                $childIds = Organization::query()
                    ->where(
                        'parent_id',
                        $organization->id
                    )
                    ->where('is_active', true)
                    ->pluck('id');

                $ids = $ids->merge(
                    $childIds
                );
            }
        }

        return $ids->unique()->values();
    }


    /**
     * Organisasi yang boleh dikelola user.
     *
     * HANYA organisasi yang langsung dimiliki user.
     *
     * Parent tidak otomatis boleh mengelola child.
     */
    private function manageOrganizationIds($user)
    {
        return $user
            ->organizations()
            ->where('is_active', true)
            ->pluck('organizations.id')
            ->unique()
            ->values();
    }

    public function cancel(
        Request $request,
        FinanceTransaction $transaction
    ): RedirectResponse {

        Gate::authorize(
            'cancel',
            $transaction
        );


        /*
    |--------------------------------------------------------------------------
    | Validasi ulang status
    |--------------------------------------------------------------------------
    |
    | Jangan hanya mengandalkan tombol Blade.
    |
    */

        if ($transaction->source_type === 'deposit') {

            return back()
                ->withErrors([
                    'transaction' =>
                    'Setoran Unit tidak dapat dibatalkan.',
                ]);
        }

        if (
            $transaction->source_type === 'student_payment'
        ) {

            return back()
                ->withErrors([
                    'transaction' =>
                    'Pembayaran siswa hanya dapat dibatalkan melalui modul Payment.',
                ]);
        }

        if (
            $transaction->status !== 'confirmed'
        ) {

            return back()
                ->withErrors([
                    'transaction' =>
                    'Transaksi ini sudah tidak dapat dibatalkan.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Validasi batas 1 x 24 jam
    |--------------------------------------------------------------------------
    */

        if (
            $transaction->created_at
            ->addDay()
            ->isPast()
        ) {

            return back()
                ->withErrors([
                    'transaction' =>
                    'Transaksi sudah melewati batas pembatalan 1 × 24 jam.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Validasi alasan
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'cancellation_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Batalkan transaksi
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use (
            $transaction,
            $validated,
            $request
        ) {

            /*
    |--------------------------------------------------------------------------
    | Batalkan FinanceTransaction
    |--------------------------------------------------------------------------
    */

            $transaction->update([
                'status' =>
                'cancelled',

                'cancellation_reason' =>
                $validated['cancellation_reason'],

                'cancelled_by' =>
                $request->user()->id,

                'cancelled_at' =>
                now(),
            ]);


            /*
    |--------------------------------------------------------------------------
    | Jika berasal dari pembayaran siswa
    |--------------------------------------------------------------------------
    */

            if (
                $transaction->source_type ===
                'student_payment'
            ) {

                $payment =
                    $transaction->payment;


                /*
        |--------------------------------------------------------------------------
        | Pastikan Payment tersedia
        |--------------------------------------------------------------------------
        */

                if (
                    ! $payment
                ) {

                    throw new \RuntimeException(
                        'Payment sumber transaksi tidak ditemukan.'
                    );
                }


                /*
        |--------------------------------------------------------------------------
        | Batalkan Payment
        |--------------------------------------------------------------------------
        */

                $payment->update([
                    'status' =>
                    'cancelled',
                ]);


                /*
        |--------------------------------------------------------------------------
        | Ambil seluruh alokasi
        |--------------------------------------------------------------------------
        */

                $payment->load([
                    'allocations.studentBill',
                ]);


                /*
        |--------------------------------------------------------------------------
        | Hitung ulang setiap StudentBill
        |--------------------------------------------------------------------------
        */

                foreach (
                    $payment->allocations
                    as $allocation
                ) {

                    $studentBill =
                        $allocation->studentBill;


                    if (
                        ! $studentBill
                    ) {

                        continue;
                    }


                    /*
            |--------------------------------------------------------------------------
            | Jumlah pembayaran yang masih confirmed
            |--------------------------------------------------------------------------
            */

                    $paidAmount =
                        $studentBill
                        ->paymentAllocations()
                        ->whereHas(
                            'payment',
                            function ($query) {

                                $query->where(
                                    'status',
                                    'confirmed'
                                );
                            }
                        )
                        ->sum(
                            'amount'
                        );


                    /*
            |--------------------------------------------------------------------------
            | Tentukan kembali status tagihan
            |--------------------------------------------------------------------------
            */

                    if (
                        $paidAmount >=
                        $studentBill->amount
                    ) {

                        $studentBill->update([
                            'status' =>
                            'paid',
                        ]);
                    } elseif (
                        $paidAmount > 0
                    ) {

                        $studentBill->update([
                            'status' =>
                            'partial',
                        ]);
                    } else {

                        $studentBill->update([
                            'status' =>
                            'unpaid',
                        ]);
                    }
                }
            }
        });

        /*
|--------------------------------------------------------------------------
| Kirim notifikasi pembatalan transaksi normal
|--------------------------------------------------------------------------
*/

        $transaction->load([
            'organization',
        ]);

        $recipients = collect();


        /*
|--------------------------------------------------------------------------
| TRANSAKSI ORGANISASI PMUB / INDUK
|--------------------------------------------------------------------------
|
| Jika transaksi berasal dari organisasi induk/PMUB,
| penerima:
| - keuangan_induk
| - ketua_induk
|
*/

        if ($transaction->organization->parent_id === null) {

            $indukUsers = User::query()
                ->whereHas('roles', function ($query) {
                    $query->whereIn('code', [
                        'keuangan_induk',
                        'ketua_induk',
                        'pengurus_induk',
                    ]);
                })
                ->whereHas('organizations', function ($query) use ($transaction) {
                    $query->where(
                        'organizations.id',
                        $transaction->organization_id
                    );
                })
                ->get();

            $recipients = $recipients->merge(
                $indukUsers
            );
        }


        /*
|--------------------------------------------------------------------------
| TRANSAKSI ORGANISASI UNIT
|--------------------------------------------------------------------------
|
| Jika transaksi berasal dari Unit,
| penerima:
| - bendahara_unit
| - kepala_unit
|
*/ else {

            $unitUsers = User::query()
                ->whereHas('roles', function ($query) {
                    $query->whereIn('code', [
                        'bendahara_unit',
                        'kepala_unit',
                    ]);
                })
                ->whereHas('organizations', function ($query) use ($transaction) {
                    $query->where(
                        'organizations.id',
                        $transaction->organization_id
                    );
                })
                ->get();

            $recipients = $recipients->merge(
                $unitUsers
            );
        }


        /*
|--------------------------------------------------------------------------
| Hilangkan penerima ganda
|--------------------------------------------------------------------------
*/

        $recipients = $recipients->unique('id');


        /*
|--------------------------------------------------------------------------
| Kirim notifikasi
|--------------------------------------------------------------------------
*/

        foreach ($recipients as $recipient) {

            $recipient->notify(
                new FinanceTransactionCancelled(
                    $transaction->fresh()
                )
            );
        }

        return redirect()
            ->route(
                'admin.finance.transactions.index'
            )
            ->with(
                'success',
                'Transaksi berhasil dibatalkan.'
            );
    }
}
