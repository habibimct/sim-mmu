<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PaymentReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Enumerable
    {
        $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | Scope organisasi
    |--------------------------------------------------------------------------
    */

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        /*
        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */
        $status = $this->filters['status'] ?? null;
        $paymentMethod = $this->filters['payment_method'] ?? null;
        $year = $this->filters['year'] ?? null;
        $month = $this->filters['month'] ?? null;
        $dateFrom = $this->filters['date_from'] ?? null;
        $dateTo = $this->filters['date_to'] ?? null;
        $search = $this->filters['search'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        */
        return Payment::query()
            ->with([
                'organization',
                'creator',
                'confirmer',
                'allocations.studentBill.billType',
                'allocations.studentBill.studentAcademicYear.student',
                'allocations.studentBill.studentAcademicYear.schoolClass',
                'allocations.studentBill.studentAcademicYear.academicYear',
            ])
            ->whereIn('organization_id', $organizationIds)

            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })

            ->when($paymentMethod, function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })

            ->when($year, function ($query) use ($year) {
                $query->whereYear('payment_date', $year);
            })

            ->when($month, function ($query) use ($month) {
                $query->whereMonth('payment_date', $month);
            })

            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('payment_date', '>=', $dateFrom);
            })

            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('payment_date', '<=', $dateTo);
            })

            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {

                    $query->where(
                        'payment_number',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhereHas(
                        'allocations.studentBill.studentAcademicYear.student',
                        function ($query) use ($search) {

                            $query->where('name', 'like', '%' . $search . '%')
                                ->orWhere('nis', 'like', '%' . $search . '%');
                        }
                    );
                });
            })

            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Heading Excel
    |--------------------------------------------------------------------------
    */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nomor Pembayaran',
            'Unit',
            'Siswa',
            'NIS',
            'Kelas',
            'Tahun Ajaran',
            'Jenis Tagihan',
            'Periode',
            'Nominal Pembayaran',
            'Metode Pembayaran',
            'Status',
            'Keterangan',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Mapping
    |--------------------------------------------------------------------------
    */
    public function map($payment): array
    {
        /*
        |--------------------------------------------------------------------------
        | Satu pembayaran dapat memiliki banyak alokasi.
        |
        | Kita tampilkan setiap alokasi dalam satu baris,
        | tetapi nominal pembayaran hanya ditampilkan pada baris pertama
        | agar tidak terjadi penggandaan nominal ketika dijumlahkan di Excel.
        |--------------------------------------------------------------------------
        */

        $allocations = $payment->allocations;

        if ($allocations->isEmpty()) {
            return [[
                $payment->id,
                optional($payment->payment_date)->format('d/m/Y'),
                $payment->payment_number,
                $payment->organization?->name,
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                $payment->amount,
                $this->paymentMethodLabel($payment->payment_method),
                $this->statusLabel($payment->status),
                $payment->description,
            ]];
        }

        $rows = [];

        foreach ($allocations as $index => $allocation) {

            $bill = $allocation->studentBill;
            $studentAcademicYear = $bill?->studentAcademicYear;
            $student = $studentAcademicYear?->student;
            $schoolClass = $studentAcademicYear?->schoolClass;
            $academicYear = $studentAcademicYear?->academicYear;
            $billType = $bill?->billType;

            $rows[] = [
                $index === 0 ? $payment->id : '',
                $index === 0
                    ? optional($payment->payment_date)->format('d/m/Y')
                    : '',
                $index === 0 ? $payment->payment_number : '',
                $index === 0 ? $payment->organization?->name : '',
                $student?->name ?? '-',
                $student?->nis ?? '-',
                $schoolClass?->name ?? '-',
                $academicYear?->name ?? '-',
                $billType?->name ?? '-',
                $bill?->period ?? '-',

                /*
                |--------------------------------------------------------------------------
                | Nominal pembayaran hanya sekali.
                |--------------------------------------------------------------------------
                */
                $index === 0 ? $payment->amount : '',

                $index === 0
                    ? $this->paymentMethodLabel($payment->payment_method)
                    : '',

                $index === 0
                    ? $this->statusLabel($payment->status)
                    : '',

                $index === 0
                    ? $payment->description
                    : '',
            ];
        }

        return $rows;
    }

    /*
    |--------------------------------------------------------------------------
    | Label metode pembayaran
    |--------------------------------------------------------------------------
    */
    protected function paymentMethodLabel(?string $method): string
    {
        return match ($method) {
            'cash' => 'Tunai',
            'bank_transfer' => 'Transfer Bank',
            'online' => 'Online',
            default => $method ?? '-',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Label status
    |--------------------------------------------------------------------------
    */
    protected function statusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'failed' => 'Gagal',
            'cancelled' => 'Dibatalkan',
            default => $status ?? '-',
        };
    }
}
