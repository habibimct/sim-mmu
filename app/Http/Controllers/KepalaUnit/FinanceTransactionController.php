<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceTransactionController extends Controller
{
    /**
     * Monitoring transaksi keuangan unit.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Organisasi yang menjadi kewenangan user
        |--------------------------------------------------------------------------
        */

        $organizationIds = $user
            ->organizations()
            ->where('organizations.is_active', true)
            ->pluck('organizations.id');

        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $type = $request->input('type');
        $paymentMethod = $request->input('payment_method');
        $category = $request->input('category');
        $search = $request->input('search');

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
                $organizationIds
            )
            ->when(
                $dateFrom,
                fn($query) =>
                $query->whereDate(
                    'transaction_date',
                    '>=',
                    $dateFrom
                )
            )
            ->when(
                $dateTo,
                fn($query) =>
                $query->whereDate(
                    'transaction_date',
                    '<=',
                    $dateTo
                )
            )
            ->when(
                $type,
                fn($query) =>
                $query->where(
                    'type',
                    $type
                )
            )
            ->when(
                $paymentMethod,
                fn($query) =>
                $query->where(
                    'payment_method',
                    $paymentMethod
                )
            )
            ->when(
                $category,
                fn($query) =>
                $query->where(
                    'category',
                    $category
                )
            )
            ->when(
                $search,
                function ($query) use ($search) {

                    $query->where(function ($query) use ($search) {

                        $query
                            ->where(
                                'description',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'category',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Ringkasan berdasarkan filter
        |--------------------------------------------------------------------------
        |
        | Ringkasan dihitung dari dataset yang sama dengan tabel.
        |
        */

        $summaryQuery = clone $query;

        $totalIncome = (clone $summaryQuery)
            ->where('status', 'confirmed')
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (clone $summaryQuery)
            ->where('status', 'confirmed')
            ->where('type', 'expense')
            ->sum('amount');

        $netAmount =
            $totalIncome - $totalExpense;

        /*
        |--------------------------------------------------------------------------
        | Tabel transaksi
        |--------------------------------------------------------------------------
        */

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Kategori yang tersedia
        |--------------------------------------------------------------------------
        */

        $categories = FinanceTransaction::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view(
            'kepala-unit.finance.transactions.index',
            compact(
                'transactions',
                'categories',
                'dateFrom',
                'dateTo',
                'type',
                'paymentMethod',
                'category',
                'search',
                'totalIncome',
                'totalExpense',
                'netAmount'
            )
        );
    }
}
