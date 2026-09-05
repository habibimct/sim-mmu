<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Teacher;
use App\Models\Role;
use App\Models\User;
use App\Exports\TeachersImportTemplateExport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TeachersImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;
use App\Exports\TeachersExport;

class TeacherController extends Controller
{
    /**
     * Menampilkan daftar guru.
     */
    public function index(Request $request): View
    {
        $organizationIds = Teacher::organizationIdsForUser();

        $query = Teacher::query()
            ->with([
                'organizations',
                'user.roles',
            ])
            ->forCurrentUser();

        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter organisasi
        |--------------------------------------------------------------------------
        */

        if ($request->filled('organization_id')) {
            $organizationId = (int) $request->organization_id;

            if ($organizationIds->contains($organizationId)) {
                $query->whereHas(
                    'organizations',
                    function ($q) use ($organizationId) {
                        $q->where(
                            'organizations.id',
                            $organizationId
                        );
                    }
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil data
        |--------------------------------------------------------------------------
        */

        $teachers = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Organisasi untuk filter
        |--------------------------------------------------------------------------
        */

        $organizations = Organization::whereIn(
            'id',
            $organizationIds
        )
            ->where('is_active', true)
            ->orderByRaw('parent_id IS NOT NULL')
            ->orderBy('name')
            ->get();


        $isGlobalOrganizationManager =
            $request->user()->hasPermission('organizations.manage');

        return view(
            'admin.teachers.index',
            compact(
                'teachers',
                'organizations',
                'isGlobalOrganizationManager'
            )
        );
    }

    /**
     * Form tambah guru.
     */
    public function create(): View
    {
        $organizationIds = Teacher::organizationIdsForUser();

        $organizations = Organization::whereIn(
            'id',
            $organizationIds
        )
            ->where('is_active', true)
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        return view(
            'admin.teachers.create',
            compact('organizations')
        );
    }


    /**
     * Menyimpan guru baru.
     */
    public function store(Request $request)
    {
        $organizationIds = Teacher::organizationIdsForUser();

        $validated = $request->validate([
            'organization_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'organization_ids.*' => [
                'integer',
                'distinct',
                Rule::in($organizationIds->all()),
            ],

            'nik' => [
                'required',
                'string',
                'digits:16',
                'unique:teachers,nik',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'gender' => [
                'required',
                'in:male,female',
            ],

            'birth_place' => [
                'nullable',
                'string',
                'max:100',
            ],

            'birth_date' => [
                'nullable',
                'date',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Role Guru
    |--------------------------------------------------------------------------
    */

        $guruRole = Role::where('code', 'guru')
            ->where('is_active', true)
            ->first();

        abort_unless(
            $guruRole,
            500,
            'Role Guru tidak tersedia atau tidak aktif.'
        );

        /*
    |--------------------------------------------------------------------------
    | Password awal Guru
    |--------------------------------------------------------------------------
    */

        $initialPassword = 'PMUB@' . $validated['nik'];

        /*
    |--------------------------------------------------------------------------
    | Buat User + Teacher dalam satu transaksi
    |--------------------------------------------------------------------------
    */

        $teacher = DB::transaction(function () use (
            $validated,
            $guruRole,
            $initialPassword
        ) {

            /*
        |----------------------------------------------------------------------
        | Buat akun User
        |----------------------------------------------------------------------
        */

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'password' => $initialPassword,
                'is_active' => $validated['is_active'],
            ]);

            /*
        |----------------------------------------------------------------------
        | Role Guru
        |----------------------------------------------------------------------
        */

            $user->roles()->sync([
                $guruRole->id,
            ]);

            /*
        |----------------------------------------------------------------------
        | Buat Teacher
        |----------------------------------------------------------------------
        */

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'nik' => $validated['nik'],
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'initial_password' => $initialPassword,
                'is_active' => $validated['is_active'],
            ]);

            /*
        |----------------------------------------------------------------------
        | Organisasi / Unit
        |----------------------------------------------------------------------
        */

            $teacher->organizations()->sync(
                $validated['organization_ids']
            );

            return $teacher;
        });

        /*
    |--------------------------------------------------------------------------
    | Kembali ke daftar Guru
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin.teachers.index')
            ->with(
                'success',
                "Guru {$teacher->name} berhasil ditambahkan."
            );
    }

    /**
     * Form edit guru.
     */
    public function edit(Teacher $teacher): View
    {
        $organizationIds = Teacher::organizationIdsForUser();

        /*
        |--------------------------------------------------------------------------
        | Pastikan guru mempunyai relasi dengan organisasi
        | yang dapat diakses user.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $teacher->organizations()
                ->whereIn(
                    'organizations.id',
                    $organizationIds
                )
                ->exists(),
            403
        );

        $organizations = Organization::whereIn(
            'id',
            $organizationIds
        )
            ->where('is_active', true)
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        $teacher->load('organizations');

        return view(
            'admin.teachers.edit',
            compact(
                'teacher',
                'organizations'
            )
        );
    }

    /**
     * Memperbarui data guru.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $organizationIds = Teacher::organizationIdsForUser();

        /*
        |--------------------------------------------------------------------------
        | Pastikan guru memang dapat diakses user.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $teacher->organizations()
                ->whereIn(
                    'organizations.id',
                    $organizationIds
                )
                ->exists(),
            403
        );

        $validated = $request->validate([
            'organization_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'organization_ids.*' => [
                'integer',
                'distinct',
                Rule::in($organizationIds->all()),
            ],

            'nik' => [
                'required',
                'string',
                'digits:16',
                Rule::unique('teachers', 'nik')
                    ->ignore($teacher->id),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'gender' => [
                'required',
                'in:male,female',
            ],

            'birth_place' => [
                'nullable',
                'string',
                'max:100',
            ],

            'birth_date' => [
                'nullable',
                'date',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update data guru
        |--------------------------------------------------------------------------
        */

        $teacher->update([
            'nik' => $validated['nik'],
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update organisasi guru
        |--------------------------------------------------------------------------
        |
        | Untuk sementara form memilih satu unit.
        | Relasi database tetap mendukung banyak unit.
        |
        */

        $currentOrganizationIds = $teacher->organizations()
            ->pluck('organizations.id');

        $manageableOrganizationIds = Teacher::organizationIdsForUser();

        /*                                                                         |
| -------------------------------------------------------------------------- |
| Unit yang berada di luar kewenangan user                                   |
| tidak boleh disentuh.                                                      |
| -------------------------------------------------------------------------- |
| */

        $protectedOrganizationIds = $currentOrganizationIds
            ->diff($manageableOrganizationIds);

        /*                                                                         |
| -------------------------------------------------------------------------- |
| Unit yang dipilih user.                                                    |
| -------------------------------------------------------------------------- |
| */

        $selectedOrganizationIds = collect(
            $validated['organization_ids']
        );

        /*                                                                         |
| -------------------------------------------------------------------------- |
| Gabungkan:                                                                 |
|                                                                            |
| 1. Unit yang dipilih user                                                  |
| 2. Unit lama yang berada di luar kewenangannya                             |
| --------------------------------------------------------------------------
| */

        $finalOrganizationIds = $selectedOrganizationIds
            ->merge($protectedOrganizationIds)
            ->unique()
            ->values()
            ->all();

        $teacher->organizations()->sync(
            $finalOrganizationIds
        );


        return redirect()
            ->route('admin.teachers.index')
            ->with(
                'success',
                "Data guru {$teacher->name} berhasil diperbarui."
            );
    }

    /**

     * Mengaktifkan atau menonaktifkan guru.
     */
    public function toggleStatus(Teacher $teacher)
    {
        $organizationIds = Teacher::organizationIdsForUser();

        /*
  | -------------------------------------------------------------------------- |
  | Pastikan guru dapat diakses oleh user saat ini.                            |
  | -------------------------------------------------------------------------- |
  */

        abort_unless(
            $teacher->organizations()
                ->whereIn(
                    'organizations.id',
                    $organizationIds
                )
                ->exists(),
            403
        );

        /*                                                                         |
  | -------------------------------------------------------------------------- |
  | Ubah status guru.                                                          |
  | -------------------------------------------------------------------------- |
  */

        $teacher->update([
            'is_active' => ! $teacher->is_active,
        ]);

        $status = $teacher->is_active
            ? 'diaktifkan'
            : 'dinonaktifkan';

        return redirect()
            ->route('admin.teachers.index')
            ->with(
                'success',
                "Guru {$teacher->name} berhasil {$status}."
            );
    }

    /**
     * Menampilkan popup/form import guru.
     *
     * Popup sebenarnya berada di halaman index,
     * sehingga method ini tidak digunakan untuk menampilkan view.
     */
    public function import()
    {
        abort(404);
    }

    /**
     * Memproses import guru.
     */
    public function storeImport(Request $request)
    {
        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Organisasi yang dapat diakses user
    |--------------------------------------------------------------------------
    */

        $organizationIds = Teacher::organizationIdsForUser();

        /*
    |--------------------------------------------------------------------------
    | Akses organisasi global
    |--------------------------------------------------------------------------
    */

        $isGlobalOrganizationManager =
            $user->hasPermission('organizations.manage');

        /*
    |--------------------------------------------------------------------------
    | Validasi file
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240',
            ],

            'organization_id' => [
                $isGlobalOrganizationManager
                    ? 'required'
                    : 'nullable',

                'integer',
                'exists:organizations,id',
            ],
        ], [
            'file.required' =>
            'File Excel wajib dipilih.',

            'file.file' =>
            'File yang dipilih tidak valid.',

            'file.mimes' =>
            'File harus berupa Excel (.xlsx atau .xls).',

            'file.max' =>
            'Ukuran file maksimal 10 MB.',

            'organization_id.required' =>
            'Unit tujuan wajib dipilih.',

            'organization_id.exists' =>
            'Unit tujuan tidak valid.',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Tentukan Unit tujuan
    |--------------------------------------------------------------------------
    */

        if ($isGlobalOrganizationManager) {

            $organizationId = (int) $validated['organization_id'];

            abort_unless(
                $organizationIds->contains($organizationId),
                403,
                'Anda tidak memiliki akses ke Unit tersebut.'
            );
        } else {

            abort_unless(
                $organizationIds->count() === 1,
                403,
                'Import Guru hanya dapat dilakukan pada Unit yang menjadi kewenangan Anda.'
            );

            $organizationId = (int) $organizationIds->first();
        }

        /*
    |--------------------------------------------------------------------------
    | Jalankan import
    |--------------------------------------------------------------------------
    */

        try {

            Excel::import(
                new TeachersImport($organizationId),
                $validated['file']
            );
        } catch (ExcelValidationException $e) {

            $failures = $e->failures();

            /*
        |--------------------------------------------------------------------------
        | Kumpulkan pesan kesalahan
        |--------------------------------------------------------------------------
        */

            $importErrors = [];

            foreach ($failures as $failure) {

                $row = $failure->row();

                foreach ($failure->errors() as $error) {

                    $importErrors[] = [
                        'row' => $row,
                        'message' => $error,
                    ];
                }
            }

            return redirect()
                ->route('admin.teachers.index')
                ->with('import_errors', $importErrors);
        }

        return redirect()
            ->route('admin.teachers.index')
            ->with(
                'success',
                'Data Guru berhasil diimport.'
            );
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new TeachersImportTemplateExport(),
            'template-import-guru.xlsx'
        );
    }

    public function export(Request $request)
    {
        $organizationIds = Teacher::organizationIdsForUser();

        $query = Teacher::query()
            ->with('organizations')
            ->forCurrentUser();

        /*
    |--------------------------------------------------------------------------
    | Pencarian
    |--------------------------------------------------------------------------
    */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        /*
    |--------------------------------------------------------------------------
    | Filter status
    |--------------------------------------------------------------------------
    */

        if ($request->filled('status')) {
            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter organisasi
    |--------------------------------------------------------------------------
    */

        if ($request->filled('organization_id')) {

            $organizationId = (int) $request->organization_id;

            /*
        |--------------------------------------------------------------------------
        | Pastikan organisasi memang berada dalam kewenangan user.
        |--------------------------------------------------------------------------
        */

            if ($organizationIds->contains($organizationId)) {

                $query->whereHas(
                    'organizations',
                    function ($q) use ($organizationId) {
                        $q->where(
                            'organizations.id',
                            $organizationId
                        );
                    }
                );
            }
        }

        return Excel::download(
            new TeachersExport($query),
            'data-guru.xlsx'
        );
    }

    public function resetPassword(Teacher $teacher)
    {
        $organizationIds = Teacher::organizationIdsForUser();

        abort_unless(
            $teacher->organizations()
                ->whereIn('organizations.id', $organizationIds)
                ->exists(),
            403
        );

        if (!$teacher->user) {
            return redirect()
                ->route('admin.teachers.index')
                ->with('error', "Akun untuk guru {$teacher->name} belum tersedia.");
        }

        $newPassword = 'PMUB@' . \Illuminate\Support\Str::random(8);

        DB::transaction(function () use ($teacher, $newPassword) {
            $teacher->user->update([
                'password' => $newPassword,
            ]);

            $teacher->update([
                'initial_password' => $newPassword,
            ]);
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with(
                'success',
                "Password guru {$teacher->name} berhasil direset."
            );
    }

    public function createAccount(Teacher $teacher)
    {
        $organizationIds = Teacher::organizationIdsForUser();

        abort_unless(
            $teacher->organizations()
                ->whereIn('organizations.id', $organizationIds)
                ->exists(),
            403
        );

        // Jika akun sudah ada, jangan membuat akun baru.
        if ($teacher->user) {
            return redirect()
                ->route('admin.teachers.index')
                ->with('error', "Guru {$teacher->name} sudah memiliki akun.");
        }

        $guruRole = Role::where('code', 'guru')
            ->where('is_active', true)
            ->first();

        abort_unless(
            $guruRole,
            500,
            'Role Guru tidak tersedia atau tidak aktif.'
        );

        $initialPassword = 'PMUB@' . $teacher->nik;

        DB::transaction(function () use ($teacher, $guruRole, $initialPassword) {
            $user = User::create([
                'name' => $teacher->name,
                'email' => $teacher->email,
                'password' => $initialPassword,
                'is_active' => $teacher->is_active,
            ]);

            $user->roles()->sync([$guruRole->id]);

            $teacher->update([
                'user_id' => $user->id,
                'initial_password' => $initialPassword,
            ]);
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with(
                'success',
                "Akun guru {$teacher->name} berhasil dibuat."
            );
    }
}
