<?php

namespace App\Imports;

use App\Models\AcademicYear;
use App\Models\Organization;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAcademicYear;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use RuntimeException;

class StudentsImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsEmptyRows
{
    protected int $organizationId;

    protected array $nisDalamFile = [];

    public function __construct(
        int $organizationId
    ) {
        $this->organizationId =
            $organizationId;

        HeadingRowFormatter::extend(
            'students',
            function ($value) {

                return match (
                    strtolower(trim($value))
                ) {

                    'nis' =>
                        'nis',

                    'nama' =>
                        'name',

                    'jenis kelamin' =>
                        'gender',

                    'tempat lahir' =>
                        'birth_place',

                    'tanggal lahir' =>
                        'birth_date',

                    'organisasi' =>
                        'organization',

                    'tahun akademik' =>
                        'academic_year',

                    'kelas' =>
                        'school_class',

                    default =>
                        $value,
                };
            }
        );

        HeadingRowFormatter::default(
            'students'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Import
    |--------------------------------------------------------------------------
    */

    public function collection(
        Collection $collection
    ): void {

        DB::transaction(
            function () use ($collection) {

                /*
                |--------------------------------------------------------------------------
                | Pastikan organisasi import valid
                |--------------------------------------------------------------------------
                */

                $organization =
                    Organization::query()
                    ->whereKey(
                        $this->organizationId
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->first();

                if (! $organization) {

                    throw new RuntimeException(
                        'Organisasi yang dipilih tidak ditemukan atau tidak aktif.'
                    );
                }


                foreach (
                    $collection as $index => $row
                ) {

                    $rowNumber =
                        $index + 2;


                    /*
                    |--------------------------------------------------------------------------
                    | Data Excel
                    |--------------------------------------------------------------------------
                    */

                    $nis =
                        trim(
                            (string) $row['nis']
                        );

                    $name =
                        trim(
                            (string) $row['name']
                        );

                    $gender =
                        strtoupper(
                            trim(
                                (string) $row['gender']
                            )
                        );

                    $organizationName =
                        trim(
                            (string) $row['organization']
                        );

                    $academicYearName =
                        trim(
                            (string) $row['academic_year']
                        );

                    $schoolClassName =
                        trim(
                            (string) $row['school_class']
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Periksa organisasi
                    |--------------------------------------------------------------------------
                    */

                    if (
                        strcasecmp(
                            $organizationName,
                            $organization->name
                        ) !== 0
                    ) {

                        throw new RuntimeException(
                            "Baris {$rowNumber}: organisasi pada Excel tidak sesuai dengan organisasi yang dipilih."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Cari tahun akademik
                    |--------------------------------------------------------------------------
                    */

                    $academicYear =
                        AcademicYear::query()
                        ->where(
                            'name',
                            $academicYearName
                        )
                        ->where(
                            'is_active',
                            true
                        )
                        ->first();

                    if (! $academicYear) {

                        throw new RuntimeException(
                            "Baris {$rowNumber}: tahun akademik '{$academicYearName}' tidak ditemukan atau tidak aktif."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Cari kelas
                    |--------------------------------------------------------------------------
                    */

                    $schoolClass =
                        SchoolClass::query()
                        ->where(
                            'organization_id',
                            $this->organizationId
                        )
                        ->where(
                            'academic_year_id',
                            $academicYear->id
                        )
                        ->where(
                            'name',
                            $schoolClassName
                        )
                        ->where(
                            'is_active',
                            true
                        )
                        ->first();

                    if (! $schoolClass) {

                        throw new RuntimeException(
                            "Baris {$rowNumber}: kelas '{$schoolClassName}' tidak ditemukan untuk organisasi '{$organization->name}' dan tahun akademik '{$academicYearName}'."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Tanggal lahir
                    |--------------------------------------------------------------------------
                    */

                    $birthDate =
                        $row['birth_date']
                        ?? null;

                    if (
                        $birthDate !== null
                        &&
                        is_numeric($birthDate)
                    ) {

                        $birthDate =
                            Date::excelToDateTimeObject(
                                $birthDate
                            )->format('Y-m-d');

                    } elseif (
                        $birthDate !== null
                        &&
                        trim((string) $birthDate) !== ''
                    ) {

                        $birthDate =
                            date(
                                'Y-m-d',
                                strtotime(
                                    $birthDate
                                )
                            );
                    } else {

                        $birthDate = null;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Buat siswa
                    |--------------------------------------------------------------------------
                    */

                    $student =
                        Student::create([

                            'organization_id' =>
                                $this->organizationId,

                            'nis' =>
                                $nis,

                            'name' =>
                                $name,

                            'gender' =>
                                $gender,

                            'birth_place' =>
                                $row['birth_place']
                                    ?? null,

                            'birth_date' =>
                                $birthDate,

                            'is_active' =>
                                true,
                        ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Buat riwayat akademik
                    |--------------------------------------------------------------------------
                    */

                    StudentAcademicYear::create([

                        'student_id' =>
                            $student->id,

                        'academic_year_id' =>
                            $academicYear->id,

                        'organization_id' =>
                            $this->organizationId,

                        'school_class_id' =>
                            $schoolClass->id,

                        'status' =>
                            'active',

                        'started_at' =>
                            $academicYear->start_date
                                ?? now()->toDateString(),

                        'ended_at' =>
                            null,
                    ]);
                }
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [

            'nis' => [
                'required',
                'string',
                'max:50',

                function (
                    $attribute,
                    $value,
                    $fail
                ) {

                    $value =
                        trim(
                            (string) $value
                        );


                    /*
                    | Cek database
                    */

                    $exists =
                        Student::query()
                        ->where(
                            'organization_id',
                            $this->organizationId
                        )
                        ->where(
                            'nis',
                            $value
                        )
                        ->exists();


                    if ($exists) {

                        $fail(
                            'NIS tersebut sudah digunakan pada organisasi yang dipilih.'
                        );

                        return;
                    }


                    /*
                    | Cek duplikat dalam file
                    */

                    if (
                        in_array(
                            $value,
                            $this->nisDalamFile,
                            true
                        )
                    ) {

                        $fail(
                            'NIS tersebut muncul lebih dari satu kali dalam file Excel.'
                        );

                        return;
                    }


                    $this->nisDalamFile[] =
                        $value;
                },
            ],


            'name' => [
                'required',
                'string',
                'max:255',
            ],


            'gender' => [
                'required',
                'in:L,P,l,p',
            ],


            'birth_place' => [
                'nullable',
                'string',
                'max:100',
            ],


            'birth_date' => [
                'nullable',
            ],


            'organization' => [
                'required',
                'string',
            ],


            'academic_year' => [
                'required',
                'string',
            ],


            'school_class' => [
                'required',
                'string',
            ],
        ];
    }
}
