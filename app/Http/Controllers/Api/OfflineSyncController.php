<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfflineSyncController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sync_id' => ['required', 'uuid'],
            'transaction_kind' => ['required', 'in:normal'],
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,bank_transfer,online'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User belum terautentikasi.',
            ], 401);
        }

        $existing = FinanceTransaction::where(
            'sync_id',
            $validated['sync_id']
        )->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi offline sudah pernah disinkronkan.',
                'ack' => [
                    'sync_id' => $existing->sync_id,
                    'status' => 'duplicate',
                    'transaction_id' => $existing->id,
                ],
                'server_time' => now()->toIso8601String(),
            ]);
        }

        /*
         * Untuk sementara kita pastikan organisasi yang dikirim
         * memang berada dalam daftar organisasi yang dapat dikelola user.
         *
         * Pola detail pengecekan akan kita samakan dengan
         * FinanceTransactionController setelah endpoint dasar ini
         * berhasil diuji.
         */
        $manageOrganizationIds = $user
            ->organizations()
            ->where('is_active', true)
            ->pluck('organizations.id')
            ->unique()
            ->values();

        if (! $manageOrganizationIds->contains(
            (int) $validated['organization_id']
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki kewenangan mengelola organisasi tersebut.',
            ], 403);
        }

        $transaction = DB::transaction(function () use (
            $validated,
            $user
        ) {
            return FinanceTransaction::create([
                'sync_id' => $validated['sync_id'],
                'organization_id' => $validated['organization_id'],
                'transaction_date' => $validated['transaction_date'],
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'category' => $validated['category'],
                'source_type' => 'normal',
                'created_by' => $user->id,
                'description' => $validated['description'] ?? null,
                'status' => 'confirmed',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi offline berhasil disinkronkan.',
            'ack' => [
                'sync_id' => $transaction->sync_id,
                'status' => 'acknowledged',
                'transaction_id' => $transaction->id,
            ],
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
