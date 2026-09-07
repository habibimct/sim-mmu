<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentAcademicYear;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    /**
     * Menampilkan daftar tahun ajaran.
     */
    public function index(): View
    {
        $academicYears = AcademicYear::query()
            ->withCount([
                'schoolClasses',
            ])
            ->withCount([
                'studentAcademicYears',
            ])
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(15);

        return view(
            'admin.academic_years.index',
            compact('academicYears')
        );
    }

    /**
     * Form tambah tahun ajaran.
     */

    /**
     * Menyimpan tahun ajaran baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                'unique:academic_years,name',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $academicYear = AcademicYear::create($validated);

        return redirect()
            ->route('admin.academic-years.index')
            ->with(
                'success',
                "Tahun ajaran {$academicYear->name} berhasil ditambahkan."
            );
    }

    /**
     * Form edit tahun ajaran.
     *
     * Hanya tahun ajaran yang masih terbuka
     * yang boleh diedit.
     */

    /**
     * Memperbarui tahun ajaran.
     *
     * Hanya tahun ajaran yang masih terbuka
     * yang boleh diubah.
     */
    public function update(
        Request $request,
        AcademicYear $academicYear
    ) {
        abort_unless(
            $academicYear->is_active,
            403,
            'Tahun ajaran sudah ditutup dan hanya dapat dilihat.'
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('academic_years', 'name')
                    ->ignore($academicYear->id),
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
        ]);

        $academicYear->update($validated);

        return redirect()
            ->route('admin.academic-years.index')
            ->with(
                'success',
                "Tahun ajaran {$academicYear->name} berhasil diperbarui."
            );
    }

    /**
     * Membuka atau menutup tahun ajaran.
     */
    public function toggleStatus(AcademicYear $academicYear)
    {
        $academicYear->update([
            'is_active' => ! $academicYear->is_active,
        ]);

        $status = $academicYear->is_active
            ? 'dibuka kembali untuk pengelolaan'
            : 'ditutup dari pengelolaan';

        return redirect()
            ->route('admin.academic-years.index')
            ->with(
                'success',
                "Tahun ajaran {$academicYear->name} berhasil {$status}."
            );
    }

    /**
     * Menghapus tahun akademik.
     *
     * Tahun akademik hanya boleh dihapus jika:
     * - belum memiliki kelas
     * - belum memiliki data siswa
     * - masih terbuka
     */
    public function destroy(AcademicYear $academicYear)
    {
        /*
    |--------------------------------------------------------------------------
    | Tahun akademik yang sudah ditutup menjadi histori
    |--------------------------------------------------------------------------
    */

        if (! $academicYear->is_active) {

            return back()->with(
                'error',
                "Tahun akademik {$academicYear->name} sudah ditutup dan tidak dapat dihapus."
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Sudah memiliki kelas
    |--------------------------------------------------------------------------
    */

        if (
            SchoolClass::where(
                'academic_year_id',
                $academicYear->id
            )->exists()
        ) {

            return back()->with(
                'error',
                "Tahun akademik {$academicYear->name} tidak dapat dihapus karena sudah memiliki kelas."
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Sudah memiliki data siswa
    |--------------------------------------------------------------------------
    */

        if (
            StudentAcademicYear::where(
                'academic_year_id',
                $academicYear->id
            )->exists()
        ) {

            return back()->with(
                'error',
                "Tahun akademik {$academicYear->name} tidak dapat dihapus karena sudah memiliki data siswa."
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Hapus
    |--------------------------------------------------------------------------
    */

        $name = $academicYear->name;

        $academicYear->delete();

        return back()->with(
            'success',
            "Tahun akademik {$name} berhasil dihapus."
        );
    }
}
