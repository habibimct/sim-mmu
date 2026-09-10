<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BillReportExport implements FromView
{
    public function __construct(
        protected array $data
    ) {}

    public function view(): View
    {
        return view(
            'admin.reports.bills.exports.excel',
            $this->data
        );
    }
}
