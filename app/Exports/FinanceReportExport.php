<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FinanceReportExport implements FromView, ShouldAutoSize
{
    public function __construct(
        public $transactions,
        public $organizationSummary,
        public $totalIncome,
        public $totalExpense,
        public $netBalance,
        public $dateFrom,
        public $dateTo,
        public $organization,
        public $type,
        public $category,
    ) {
    }

    public function view(): View
    {
        return view(
            'admin.reports.finance.exports.excel',
            [
                'transactions' =>
                    $this->transactions,

                'organizationSummary' =>
                    $this->organizationSummary,

                'totalIncome' =>
                    $this->totalIncome,

                'totalExpense' =>
                    $this->totalExpense,

                'netBalance' =>
                    $this->netBalance,

                'dateFrom' =>
                    $this->dateFrom,

                'dateTo' =>
                    $this->dateTo,

                'organization' =>
                    $this->organization,

                'type' =>
                    $this->type,

                'category' =>
                    $this->category,
            ]
        );
    }
}
