<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToCollection, WithHeadingRow
{
    /**
     * Seluruh baris hasil pembacaan Excel.
     */
    public Collection $rows;

    public function __construct()
    {
        $this->rows = collect();
    }

    /**
     * Ambil seluruh data Excel tanpa menyimpan ke database.
     *
     * Penyimpanan database akan dilakukan setelah
     * seluruh baris dinyatakan valid.
     */
    public function collection(Collection $rows): void
    {
        $this->rows = $rows;
    }
}
