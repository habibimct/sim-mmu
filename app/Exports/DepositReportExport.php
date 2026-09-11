<?php

namespace App\Exports;

use App\Models\FinanceDeposit;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DepositReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected Request $request;

    protected Collection $organizationIds;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->organizationIds =
            Organization::accessibleIdsForUser(
                $request->user()
            );
    }

    public function collection(): Enumerable
    {
        $status = $this->request->input('status');
        $year = $this->request->input('year');
        $month = $this->request->input('month');
        $dateFrom = $this->request->input('date_from');
        $dateTo = $this->request->input('date_to');
        $search = $this->request->input('search');

        return FinanceDeposit::query()
            ->with([
                'organization',
                'targetOrganization',
                'creator',
                'confirmer',
            ])
            ->whereIn(
                'organization_id',
                $this->organizationIds
            )
            ->when(
                $status,
                fn ($query) =>
                    $query->where('status', $status)
            )
            ->when(
                $year,
                fn ($query) =>
                    $query->whereYear('deposit_date', $year)
            )
            ->when(
                $month,
                fn ($query) =>
                    $query->whereMonth('deposit_date', $month)
            )
            ->when(
                $dateFrom,
                fn ($query) =>
                    $query->whereDate(
                        'deposit_date',
                        '>=',
                        $dateFrom
                    )
            )
            ->when(
                $dateTo,
                fn ($query) =>
                    $query->whereDate(
                        'deposit_date',
                        '<=',
                        $dateTo
                    )
            )
            ->when(
                $search,
                function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {

                        $query->whereHas(
                            'organization',
                            function ($query) use ($search) {
                                $query->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                );
                            }
                        )

                        ->orWhereHas(
                            'targetOrganization',
                            function ($query) use ($search) {
                                $query->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                );
                            }
                        )

                        ->orWhere(
                            'description',
                            'like',
                            '%' . $search . '%'
                        );
                    });
                }
            )
            ->orderByDesc('deposit_date')
            ->orderByDesc('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Unit Pengirim',
            'Tujuan',
            'Nominal',
            'Metode Pembayaran',
            'Status',
            'Keterangan',
            'Dibuat Oleh',
            'Dikonfirmasi Oleh',
            'Waktu Konfirmasi',
        ];
    }

    public function map($deposit): array
    {
        $status = match ($deposit->status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'rejected' => 'Ditolak',
            default => ucfirst($deposit->status),
        };

        $paymentMethod = match ($deposit->payment_method) {
            'cash' => 'Tunai',
            'bank_transfer' => 'Transfer Bank',
            'online' => 'Online',
            default => ucfirst($deposit->payment_method),
        };

        return [
            $deposit->id,
            optional($deposit->deposit_date)->format('d/m/Y'),
            $deposit->organization?->name ?? '-',
            $deposit->targetOrganization?->name ?? '-',
            (float) $deposit->amount,
            $paymentMethod,
            $status,
            $deposit->description ?? '-',
            $deposit->creator?->name ?? '-',
            $deposit->confirmer?->name ?? '-',
            optional($deposit->confirmed_at)->format('d/m/Y H:i'),
        ];
    }
}
