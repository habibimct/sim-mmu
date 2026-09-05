<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceDeposit;
use App\Models\Organization;
use App\Models\FinanceTransaction;
use App\Models\User;
use App\Notifications\FinanceDepositCreated;
use App\Notifications\FinanceDepositConfirmed;
use App\Notifications\FinanceDepositRejected;
use App\Notifications\FinanceTransactionCreated;
use App\Support\FinanceNotificationRecipients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class FinanceDepositController extends Controller
{
    /**
     * Daftar setoran.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Organisasi yang boleh dilihat
        |--------------------------------------------------------------------------
        */

        $organizations = $user
            ->organizations()
            ->where('is_active', true)
            ->get();

        $organizationIds = collect();

        foreach ($organizations as $organization) {

            $organizationIds->push(
                $organization->id
            );

            /*
            | Parent dapat melihat setoran child.
            */

            if ($organization->parent_id === null) {

                $childIds = Organization::query()
                    ->where(
                        'parent_id',
                        $organization->id
                    )
                    ->where('is_active', true)
                    ->pluck('id');

                $organizationIds = $organizationIds
                    ->merge($childIds);
            }
        }

        $organizationIds = $organizationIds
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        */

        $query = FinanceDeposit::query()
            ->with([
                'organization',
                'targetOrganization',
                'creator',
                'confirmer',
            ])
            ->whereIn(
                'organization_id',
                $organizationIds
            );

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('organization_id')) {

            $organizationId = (int)
            $request->organization_id;

            abort_unless(
                $organizationIds->contains(
                    $organizationId
                ),
                403
            );

            $query->where(
                'organization_id',
                $organizationId
            );
        }

        $deposits = $query
            ->orderByDesc('deposit_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.finance.deposits.index',
            compact(
                'deposits',
                'organizations'
            )
        );
    }


    /**
     * Menyimpan setoran baru.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Organisasi user
        |--------------------------------------------------------------------------
        */

        $userOrganizations = $user
            ->organizations()
            ->where('is_active', true)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Bendahara Unit hanya boleh membuat setoran
        | dari organisasi miliknya.
        |--------------------------------------------------------------------------
        */

        $organizationIds = $userOrganizations
            ->pluck('id');

        $validated = $request->validate([
            'organization_id' => [
                'required',
                'integer',
                Rule::in(
                    $organizationIds->all()
                ),
            ],

            'deposit_date' => [
                'required',
                'date',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'cash',
                    'bank_transfer',
                    'online',
                ]),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'proof' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cari parent
        |--------------------------------------------------------------------------
        */

        $organization = Organization::query()
            ->whereKey(
                $validated['organization_id']
            )
            ->where('is_active', true)
            ->firstOrFail();

        if ($organization->parent_id === null) {
            return back()
                ->withErrors([
                    'organization_id' =>
                    'Organisasi parent tidak dapat membuat setoran ke dirinya sendiri.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Parent harus aktif
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
        | Upload bukti
        |--------------------------------------------------------------------------
        |
        | Untuk sementara kita simpan file asli dulu.
        | Kompresi akan kita pasang setelah alur dasar berhasil.
        |
        */

        $proof = $request->file('proof');

        $proofPath = $proof->store(
            'finance/deposits',
            'public'
        );

        /*
        |--------------------------------------------------------------------------
        | Simpan setoran
        |--------------------------------------------------------------------------
        */

        $deposit = FinanceDeposit::create([
            'organization_id' => $organization->id,

            'target_organization_id' => $parent->id,

            'deposit_date' =>
            $validated['deposit_date'],

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

    /**
     * Mengonfirmasi setoran unit.
     */
    public function confirm(
        Request $request,
        FinanceDeposit $deposit
    ) {
        Gate::authorize(
            'confirm',
            $deposit
        );

        $user = $request->user();

        DB::transaction(function () use (
            $deposit,
            $user
        ) {

            /*
        |--------------------------------------------------------------------------
        | Kunci setoran
        |--------------------------------------------------------------------------
        */

            $deposit = FinanceDeposit::query()
                ->lockForUpdate()
                ->findOrFail($deposit->id);

            /*
        |--------------------------------------------------------------------------
        | Pastikan masih pending
        |--------------------------------------------------------------------------
        */

            if ($deposit->status !== 'pending') {

                abort(
                    422,
                    'Setoran ini sudah diproses.'
                );
            }

            /*
        |--------------------------------------------------------------------------
        | Transaksi pengeluaran pada Unit
        |--------------------------------------------------------------------------
        */

            $unitTransaction = FinanceTransaction::create([
                'organization_id' =>
                $deposit->organization_id,

                'transaction_date' =>
                $deposit->deposit_date,

                'type' =>
                'expense',

                'amount' =>
                $deposit->amount,

                'payment_method' =>
                $deposit->payment_method,

                'category' =>
                'Setoran Unit',

                'source_type' => 'deposit',

                'description' =>
                $deposit->description
                    ?? 'Setoran Unit ke Parent',

                'created_by' =>
                $user->id,
            ]);

            /*
        |--------------------------------------------------------------------------
        | Transaksi pemasukan pada Parent
        |--------------------------------------------------------------------------
        */

            $parentTransaction = FinanceTransaction::create([
                'organization_id' =>
                $deposit->target_organization_id,

                'transaction_date' =>
                $deposit->deposit_date,

                'type' =>
                'income',

                'amount' =>
                $deposit->amount,

                'payment_method' =>
                $deposit->payment_method,

                'category' =>
                'Setoran Unit',

                'source_type' => 'deposit',

                'description' =>
                $deposit->description
                    ?? 'Penerimaan Setoran Unit',

                'created_by' =>
                $user->id,
            ]);

            /*
        |--------------------------------------------------------------------------
        | Tandai setoran telah dikonfirmasi
        |--------------------------------------------------------------------------
        */

            $unitTransaction->load('organization');
            $parentTransaction->load('organization');

            foreach (
                FinanceNotificationRecipients::forTransaction(
                    $unitTransaction
                ) as $recipient
            ) {

                $recipient->notify(
                    new FinanceTransactionCreated(
                        $unitTransaction
                    )
                );
            }


            foreach (
                FinanceNotificationRecipients::forTransaction(
                    $parentTransaction
                ) as $recipient
            ) {

                $recipient->notify(
                    new FinanceTransactionCreated(
                        $parentTransaction
                    )
                );
            }

            $deposit->update([
                'status' =>
                'confirmed',

                'confirmed_by' =>
                $user->id,

                'confirmed_at' =>
                now(),
            ]);

            /*
        |--------------------------------------------------------------------------
        | Ambil Bendahara Unit
        |--------------------------------------------------------------------------
        */

            $unitTreasurers = User::query()
                ->where('is_active', true)

                ->whereHas('organizations', function ($query) use ($deposit) {

                    $query->where(
                        'organizations.id',
                        $deposit->organization_id
                    );
                })

                ->whereHas('roles', function ($query) {

                    $query->where(
                        'code',
                        'bendahara_unit'
                    );
                })

                ->get();

            /*
        |--------------------------------------------------------------------------
        | Kirim notifikasi konfirmasi
        |--------------------------------------------------------------------------
        */

            $deposit->load([
                'organization',
                'targetOrganization',
            ]);

            foreach ($unitTreasurers as $treasurer) {

                $treasurer->notify(
                    new FinanceDepositConfirmed(
                        $deposit
                    )
                );
            }
        });

        return redirect()
            ->route(
                'admin.finance.transactions.index'
            )
            ->with(
                'success',
                'Setoran berhasil dikonfirmasi, transaksi keuangan telah dibuat, dan Bendahara Unit telah diberi pemberitahuan.'
            );
    }

    /**
     * Menolak setoran unit.
     */
    public function reject(
        Request $request,
        FinanceDeposit $deposit
    ) {
        Gate::authorize(
            'reject',
            $deposit
        );

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        if ($deposit->status !== 'pending') {

            return back()
                ->withErrors([
                    'deposit' =>
                    'Setoran ini sudah diproses.',
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Tolak setoran + catat transaksi
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use (
            $deposit,
            $validated,
            $request
        ) {

            /*
        |--------------------------------------------------------------------------
        | 1. Update FinanceDeposit
        |--------------------------------------------------------------------------
        */

            $deposit->update([
                'status' =>
                'rejected',

                'rejection_reason' =>
                $validated['rejection_reason'],
            ]);


            /*
        |--------------------------------------------------------------------------
        | 2. Catat FinanceTransaction
        |--------------------------------------------------------------------------
        |
        | Setoran yang ditolak tetap dicatat sebagai histori,
        | tetapi status = rejected sehingga tidak masuk saldo.
        |
        */

            FinanceTransaction::create([
                'organization_id' =>
                $deposit->organization_id,

                'transaction_date' =>
                $deposit->deposit_date,

                'type' =>
                'income',

                'amount' =>
                $deposit->amount,

                'payment_method' =>
                $deposit->payment_method,

                'category' =>
                'Setoran Unit',

                'source_type' =>
                'deposit',

                'created_by' =>
                $request->user()->id,

                'description' =>
                $deposit->description,

                'status' =>
                'rejected',

                'rejection_reason' =>
                $validated['rejection_reason'],
            ]);
        });


        /*
    |--------------------------------------------------------------------------
    | Siapkan relasi
    |--------------------------------------------------------------------------
    */

        $deposit->load([
            'organization',
            'targetOrganization',
        ]);


        /*
    |--------------------------------------------------------------------------
    | Bendahara Unit
    |--------------------------------------------------------------------------
    */

        $unitTreasurers = User::query()
            ->where('is_active', true)
            ->whereHas('organizations', function ($query) use ($deposit) {
                $query->where(
                    'organizations.id',
                    $deposit->organization_id
                );
            })
            ->whereHas('roles', function ($query) {
                $query->where(
                    'code',
                    'bendahara_unit'
                );
            })
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Kepala Unit
    |--------------------------------------------------------------------------
    */

        $unitHeads = User::query()
            ->where('is_active', true)
            ->whereHas('organizations', function ($query) use ($deposit) {
                $query->where(
                    'organizations.id',
                    $deposit->organization_id
                );
            })
            ->whereHas('roles', function ($query) {
                $query->where(
                    'code',
                    'kepala_unit'
                );
            })
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Ketua INDUK
    |--------------------------------------------------------------------------
    */

        $indukChairs = User::query()
            ->where('is_active', true)
            ->whereHas('organizations', function ($query) use ($deposit) {
                $query->where(
                    'organizations.id',
                    $deposit->target_organization_id
                );
            })
            ->whereHas('roles', function ($query) {
                $query->where(
                    'code',
                    'ketua_induk'
                );
            })
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Gabungkan penerima
    |--------------------------------------------------------------------------
    */

        $recipients = $unitTreasurers
            ->merge($unitHeads)
            ->merge($indukChairs)
            ->unique('id')
            ->values();


        /*
    |--------------------------------------------------------------------------
    | Kirim notifikasi penolakan
    |--------------------------------------------------------------------------
    */

        foreach ($recipients as $recipient) {

            $recipient->notify(
                new FinanceDepositRejected(
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
                'Setoran telah ditolak dan transaksi penolakan telah dicatat.'
            );
    }
}
