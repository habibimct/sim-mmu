<?php

namespace App\Imports;

use App\Models\Teacher;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TeachersImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsEmptyRows
{
    protected int $organizationId;

    public function __construct(int $organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Memproses data Excel.
     */
    public function collection(Collection $rows): void
    {
        DB::transaction(function () use ($rows) {

            foreach ($rows as $row) {

                /*
                |--------------------------------------------------------------------------
                | Konversi tanggal lahir
                |--------------------------------------------------------------------------
                */

                $birthDate = $this->convertBirthDate(
                    $row['tanggal_lahir'] ?? null
                );

                /*
                |--------------------------------------------------------------------------
                | Jenis kelamin
                |--------------------------------------------------------------------------
                */

                $gender = strtoupper(
                    trim((string) ($row['jenis_kelamin'] ?? ''))
                );

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $status = strtolower(
                    trim((string) ($row['status'] ?? ''))
                );

                /*
                |--------------------------------------------------------------------------
                | Simpan Guru
                |--------------------------------------------------------------------------
                */

                $teacher = Teacher::create([
                    'nik' => trim(
                        (string) $row['nik']
                    ),

                    'name' => trim(
                        (string) $row['nama']
                    ),

                    'gender' => $gender === 'L'
                        ? 'male'
                        : 'female',

                    'birth_place' => !empty(
                        $row['tempat_lahir']
                    )
                        ? trim(
                            (string) $row['tempat_lahir']
                        )
                        : null,

                    'birth_date' => $birthDate,

                    'phone' => !empty(
                        $row['no_hp']
                    )
                        ? trim(
                            (string) $row['no_hp']
                        )
                        : null,

                    'email' => !empty(
                        $row['email']
                    )
                        ? trim(
                            (string) $row['email']
                        )
                        : null,

                    'is_active' => $status === 'aktif',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Hubungkan Guru dengan Unit
                |--------------------------------------------------------------------------
                */

                $teacher->organizations()->attach(
                    $this->organizationId
                );
            }
        });
    }

    /**
     * Konversi tanggal Excel menjadi Y-m-d.
     */
    private function convertBirthDate($value): ?string
    {
        /*
        |--------------------------------------------------------------------------
        | Kosong
        |--------------------------------------------------------------------------
        */

        if ($value === null || $value === '') {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Excel menyimpan tanggal sebagai angka serial.
        |--------------------------------------------------------------------------
        */

        if (is_numeric($value)) {

            return Date::excelToDateTimeObject(
                (float) $value
            )->format('Y-m-d');
        }

        /*
        |--------------------------------------------------------------------------
        | Tanggal berupa teks
        |--------------------------------------------------------------------------
        */

        $value = trim((string) $value);

        $formats = [
            'd/m/Y',
            'd-m-Y',
            'Y-m-d',
        ];

        foreach ($formats as $format) {

            $date = DateTime::createFromFormat(
                '!' . $format,
                $value
            );

            if (
                $date !== false &&
                $date->format($format) === $value
            ) {
                return $date->format('Y-m-d');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Seharusnya tidak tercapai karena sudah divalidasi sebelumnya.
        |--------------------------------------------------------------------------
        */

        throw new \InvalidArgumentException(
            'Tanggal lahir tidak valid.'
        );
    }

    /**
     * Validasi data Excel.
     */
    public function rules(): array
    {
        return [

            'nik' => [
                'required',
                'digits:16',
                'distinct',
                'unique:teachers,nik',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'jenis_kelamin' => [
                'required',
                'in:L,P',
            ],

            'tempat_lahir' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Validasi tanggal lahir
            |--------------------------------------------------------------------------
            |
            | Dapat menerima:
            |
            | 1. Tanggal Excel berupa serial number
            | 2. Format dd/mm/yyyy
            | 3. Format dd-mm-yyyy
            | 4. Format yyyy-mm-dd
            |
            | Tanggal harus benar-benar valid.
            |
            */

            'tanggal_lahir' => [
                'nullable',
                function ($attribute, $value, $fail) {

                    if ($value === null || $value === '') {
                        return;
                    }

                    $valid = false;

                    /*
                    |--------------------------------------------------------------------------
                    | Excel serial date
                    |--------------------------------------------------------------------------
                    */

                    if (is_numeric($value)) {

                        try {

                            $date = Date::excelToDateTimeObject(
                                (float) $value
                            );

                            $valid = $date !== false;

                        } catch (\Throwable $e) {

                            $valid = false;
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Tanggal berupa teks
                    |--------------------------------------------------------------------------
                    */

                    else {

                        $value = trim((string) $value);

                        $formats = [
                            'd/m/Y',
                            'd-m-Y',
                            'Y-m-d',
                        ];

                        foreach ($formats as $format) {

                            $date = DateTime::createFromFormat(
                                '!' . $format,
                                $value
                            );

                            if (
                                $date !== false &&
                                $date->format($format) === $value
                            ) {
                                $valid = true;
                                break;
                            }
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Tanggal tidak valid
                    |--------------------------------------------------------------------------
                    */

                    if (!$valid) {

                        $fail(
                            'Tanggal lahir harus berupa tanggal yang valid dengan format dd/mm/yyyy.'
                        );
                    }
                },
            ],

            'no_hp' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'status' => [
                'required',
                'in:Aktif,Nonaktif',
            ],
        ];
    }

    /**
     * Pesan validasi.
     */
    public function customValidationMessages(): array
    {
        return [

            'nik.required' =>
                'NIK wajib diisi.',

            'nik.digits' =>
                'NIK harus terdiri dari 16 digit.',

            'nik.distinct' =>
                'NIK tidak boleh muncul lebih dari satu kali dalam file import.',

            'nik.unique' =>
                'NIK sudah terdaftar.',

            'nama.required' =>
                'Nama Guru wajib diisi.',

            'jenis_kelamin.required' =>
                'Jenis kelamin wajib diisi.',

            'jenis_kelamin.in' =>
                'Jenis kelamin harus L atau P.',

            'tempat_lahir.max' =>
                'Tempat lahir maksimal 100 karakter.',

            'no_hp.max' =>
                'No. HP maksimal 30 karakter.',

            'email.email' =>
                'Format email tidak valid.',

            'email.max' =>
                'Email maksimal 255 karakter.',

            'status.required' =>
                'Status wajib diisi.',

            'status.in' =>
                'Status harus Aktif atau Nonaktif.',
        ];
    }
}
