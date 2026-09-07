<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Exports\UsersExport;
use App\Exports\UsersImportTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user.
     */
    public function index(): View
    {
        $users = User::with([
            'roles',
            'organizations',
        ])
            ->orderBy('name')
            ->get();

        $roles = Role::where('is_active', true)
            ->orderBy('name')
            ->get();

        $organizations = Organization::where('is_active', true)
            ->orderByRaw("CASE WHEN type = 'INDUK' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact(
            'users',
            'roles',
            'organizations'
        ));
    }


    /**
     * Menyimpan user baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],

            'organization_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'organization_ids.*' => [
                'integer',
                'exists:organizations,id',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Pastikan role yang dipilih masih aktif
        |--------------------------------------------------------------------------
        */

        $role = Role::where('id', $validated['role_id'])
            ->where('is_active', true)
            ->first();

        if (!$role) {
            return back()
                ->withInput()
                ->withErrors([
                    'role_id' => 'Role yang dipilih tidak aktif.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan organisasi yang dipilih masih aktif
        |--------------------------------------------------------------------------
        */

        $organizationIds = Organization::whereIn(
            'id',
            $validated['organization_ids']
        )
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();


        if (count($organizationIds) !== count($validated['organization_ids'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'organization_ids' =>
                        'Terdapat organisasi yang tidak aktif atau tidak valid.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Buat User
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],

            // Password harus disimpan dalam bentuk hash
            'password' => Hash::make($validated['password']),

            // Password awal yang diberikan administrator
            'initial_password' => $validated['password'],

            'is_active' => $validated['is_active'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Hubungkan Role
        |--------------------------------------------------------------------------
        */

        $user->roles()->sync([
            $validated['role_id'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Hubungkan Organisasi
        |--------------------------------------------------------------------------
        */

        $user->organizations()->sync($organizationIds);


        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }


    /**
     * Memperbarui user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],

            'organization_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'organization_ids.*' => [
                'integer',
                'exists:organizations,id',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Pastikan role aktif
        |--------------------------------------------------------------------------
        */

        $role = Role::where('id', $validated['role_id'])
            ->where('is_active', true)
            ->first();

        if (!$role) {
            return back()
                ->withInput()
                ->withErrors([
                    'role_id' => 'Role yang dipilih tidak aktif.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan organisasi aktif
        |--------------------------------------------------------------------------
        */

        $organizationIds = Organization::whereIn(
            'id',
            $validated['organization_ids']
        )
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();


        if (count($organizationIds) !== count($validated['organization_ids'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'organization_ids' =>
                        'Terdapat organisasi yang tidak aktif atau tidak valid.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Update User
        |--------------------------------------------------------------------------
        */

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $validated['is_active'],
        ];


        /*
        |--------------------------------------------------------------------------
        | Jika password baru diisi
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['password'])) {

            $data['password'] = Hash::make(
                $validated['password']
            );

            $data['initial_password'] =
                $validated['password'];
        }


        $user->update($data);


        /*
        |--------------------------------------------------------------------------
        | Update Role
        |--------------------------------------------------------------------------
        */

        $user->roles()->sync([
            $validated['role_id'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Update Organisasi
        |--------------------------------------------------------------------------
        */

        $user->organizations()->sync($organizationIds);


        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }


    /**
     * Reset password user.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        $password = 'PMUB@' . Str::random(8);

        $user->update([
            'password' => Hash::make($password),
            'initial_password' => $password,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                "Password {$user->name} berhasil direset. Password baru: {$password}"
            );
    }


    /**
     * Menghapus user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {

            return back()->with(
                'error',
                'Anda tidak dapat menghapus akun yang sedang digunakan.'
            );
        }


        $user->roles()->detach();

        $user->organizations()->detach();

        $user->delete();


        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User berhasil dihapus.'
            );
    }

    /**
 * Export User.
 */
public function export()
{
    return Excel::download(
        new UsersExport,
        'users.xlsx'
    );
}

/**
 * Download template import User.
 */
public function downloadTemplate()
{
    return Excel::download(
        new UsersImportTemplateExport,
        'template-import-users.xlsx'
    );
}

/**
 * Import User dari Excel.
 *
 * Prinsip:
 * - Semua baris divalidasi terlebih dahulu.
 * - Jika ada satu kesalahan, tidak ada data yang disimpan.
 * - Jika semua valid, seluruh data disimpan dalam satu transaction.
 */
public function import(Request $request)
{
    $request->validate([
        '_form' => ['nullable', 'in:import'],

        'file' => [
            'required',
            'file',
            'mimes:xlsx,xls,csv',
            'max:10240',
        ],
    ], [
        'file.required' => 'File Excel wajib dipilih.',
        'file.file' => 'File yang dikirim tidak valid.',
        'file.mimes' => 'File harus berformat XLSX, XLS, atau CSV.',
        'file.max' => 'Ukuran file maksimal 10 MB.',
    ]);


    try {

        /*
        |--------------------------------------------------------------------------
        | 1. Baca seluruh Excel
        |--------------------------------------------------------------------------
        */

        $import = new UsersImport();

        Excel::import(
            $import,
            $request->file('file')
        );

        $rows = $import->rows;


        /*
        |--------------------------------------------------------------------------
        | 2. Pastikan ada data
        |--------------------------------------------------------------------------
        */

        if ($rows->isEmpty()) {

            return back()
                ->with('open_import_modal', true)
                ->with('import_errors', [
                    'File Excel tidak memiliki data User.'
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 3. Ambil Role aktif
        |--------------------------------------------------------------------------
        */

        $roles = \App\Models\Role::where('is_active', true)
            ->get()
            ->keyBy(function ($role) {
                return mb_strtolower(trim($role->name));
            });


        /*
        |--------------------------------------------------------------------------
        | 4. Ambil Organisasi aktif
        |--------------------------------------------------------------------------
        */

        $organizations = \App\Models\Organization::where('is_active', true)
            ->get()
            ->keyBy(function ($organization) {
                return mb_strtolower(trim($organization->code));
            });


        /*
        |--------------------------------------------------------------------------
        | 5. Kumpulkan email dari Excel
        |--------------------------------------------------------------------------
        */

        $emails = $rows
            ->map(function ($row) {
                return mb_strtolower(
                    trim((string) ($row['email'] ?? ''))
                );
            })
            ->filter()
            ->unique()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | 6. Cek email yang sudah ada di database
        |--------------------------------------------------------------------------
        */

        $existingEmails = User::whereIn('email', $emails)
            ->pluck('email')
            ->map(function ($email) {
                return mb_strtolower(trim($email));
            })
            ->flip();


        /*
        |--------------------------------------------------------------------------
        | 7. Validasi seluruh baris
        |--------------------------------------------------------------------------
        */

        $errors = [];
        $validRows = [];
        $seenEmails = [];


        foreach ($rows as $index => $row) {

            /*
            |--------------------------------------------------------------------------
            | Nomor baris Excel
            |--------------------------------------------------------------------------
            |
            | Baris pertama adalah heading.
            | Maka data pertama = baris 2.
            |
            */

            $excelRow = $index + 2;


            /*
            |--------------------------------------------------------------------------
            | Abaikan baris kosong sepenuhnya
            |--------------------------------------------------------------------------
            */

            $hasData = collect($row)
                ->filter(function ($value) {
                    return trim((string) $value) !== '';
                })
                ->isNotEmpty();

            if (!$hasData) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Ambil data
            |--------------------------------------------------------------------------
            */

            $name = trim((string) ($row['nama'] ?? ''));

            $email = mb_strtolower(
                trim((string) ($row['email'] ?? ''))
            );

            $password = (string) ($row['password'] ?? '');

            $roleName = trim(
                (string) ($row['role'] ?? '')
            );

            $organizationValue = trim(
                (string) ($row['organisasi'] ?? '')
            );

            $status = mb_strtolower(
                trim((string) ($row['status'] ?? ''))
            );


            /*
            |--------------------------------------------------------------------------
            | Nama
            |--------------------------------------------------------------------------
            */

            if ($name === '') {

                $errors[] =
                    "Baris {$excelRow}: Nama wajib diisi.";

            } elseif (mb_strlen($name) > 255) {

                $errors[] =
                    "Baris {$excelRow}: Nama maksimal 255 karakter.";
            }


            /*
            |--------------------------------------------------------------------------
            | Email
            |--------------------------------------------------------------------------
            */

            if ($email === '') {

                $errors[] =
                    "Baris {$excelRow}: Email wajib diisi.";

            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $errors[] =
                    "Baris {$excelRow}: Format email tidak valid.";

            } elseif (isset($seenEmails[$email])) {

                $errors[] =
                    "Baris {$excelRow}: Email {$email} duplikat di dalam file.";

            } elseif ($existingEmails->has($email)) {

                $errors[] =
                    "Baris {$excelRow}: Email {$email} sudah terdaftar.";
            }

            $seenEmails[$email] = true;


            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            */

            if ($password === '') {

                $errors[] =
                    "Baris {$excelRow}: Password wajib diisi.";

            } elseif (mb_strlen($password) < 8) {

                $errors[] =
                    "Baris {$excelRow}: Password minimal 8 karakter.";
            }


            /*
            |--------------------------------------------------------------------------
            | Role
            |--------------------------------------------------------------------------
            */

            $role = null;

            if ($roleName === '') {

                $errors[] =
                    "Baris {$excelRow}: Role wajib diisi.";

            } else {

                $roleKey = mb_strtolower($roleName);

                if (!$roles->has($roleKey)) {

                    $errors[] =
                        "Baris {$excelRow}: Role \"{$roleName}\" tidak ditemukan atau tidak aktif.";

                } else {

                    $role = $roles->get($roleKey);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Organisasi
            |--------------------------------------------------------------------------
            */

            $organizationIds = [];

            if ($organizationValue === '') {

                $errors[] =
                    "Baris {$excelRow}: Organisasi wajib diisi.";

            } else {

                /*
                | Organisasi dipisahkan dengan koma.
                |
                | Contoh:
                | PMUB
                |
                | atau:
                | UNIT01, UNIT02
                */

                $organizationCodes = preg_split(
                    '/\s*,\s*/',
                    $organizationValue,
                    -1,
                    PREG_SPLIT_NO_EMPTY
                );

                $organizationCodes = array_unique(
                    array_map(
                        function ($code) {
                            return mb_strtolower(trim($code));
                        },
                        $organizationCodes
                    )
                );


                foreach ($organizationCodes as $organizationCode) {

                    if (!$organizations->has($organizationCode)) {

                        $originalCode = strtoupper(
                            trim($organizationCode)
                        );

                        $errors[] =
                            "Baris {$excelRow}: Organisasi \"{$originalCode}\" tidak ditemukan atau tidak aktif.";

                    } else {

                        $organizationIds[] =
                            $organizations->get($organizationCode)->id;
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $isActive = null;

            if ($status === 'aktif') {

                $isActive = true;

            } elseif ($status === 'tidak aktif') {

                $isActive = false;

            } else {

                $errors[] =
                    "Baris {$excelRow}: Status harus \"Aktif\" atau \"Tidak Aktif\".";
            }


            /*
            |--------------------------------------------------------------------------
            | Simpan data valid sementara
            |--------------------------------------------------------------------------
            |
            | Belum masuk database.
            |
            */

            if (
                $name !== '' &&
                filter_var($email, FILTER_VALIDATE_EMAIL) &&
                $password !== '' &&
                mb_strlen($password) >= 8 &&
                $role !== null &&
                !empty($organizationIds) &&
                $isActive !== null &&
                !isset($existingEmails[$email])
            ) {

                $validRows[] = [
                    'row' => $excelRow,
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'role_id' => $role->id,
                    'organization_ids' => $organizationIds,
                    'is_active' => $isActive,
                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | 8. JIKA ADA SATU SAJA ERROR → BATALKAN SEMUA
        |--------------------------------------------------------------------------
        */

        if (!empty($errors)) {

            /*
            | Batasi pesan agar session tidak terlalu besar.
            */

            $displayErrors = array_slice($errors, 0, 100);

            if (count($errors) > 100) {

                $displayErrors[] =
                    'Masih terdapat ' .
                    (count($errors) - 100) .
                    ' kesalahan lainnya.';
            }


            return back()
                ->with('open_import_modal', true)
                ->with('import_errors', $displayErrors);
        }


        /*
        |--------------------------------------------------------------------------
        | 9. Semua valid → SIMPAN DALAM TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($validRows) {

            foreach ($validRows as $data) {

                $user = User::create([
                    'name' => $data['name'],

                    'email' => $data['email'],

                    /*
                    | Password database harus HASH.
                    */
                    'password' => \Illuminate\Support\Facades\Hash::make(
                        $data['password']
                    ),

                    /*
                    | Password asli untuk kebutuhan
                    | administrasi PMUB.
                    */
                    'initial_password' => $data['password'],

                    'is_active' => $data['is_active'],
                ]);


                /*
                |--------------------------------------------------------------------------
                | Role
                |--------------------------------------------------------------------------
                */

                $user->roles()->sync([
                    $data['role_id']
                ]);


                /*
                |--------------------------------------------------------------------------
                | Organisasi
                |--------------------------------------------------------------------------
                */

                $user->organizations()->sync(
                    $data['organization_ids']
                );
            }
        });


        /*
        |--------------------------------------------------------------------------
        | 10. Berhasil
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                count($validRows) .
                ' User berhasil diimport.'
            );


    } catch (Throwable $e) {

        /*
        |--------------------------------------------------------------------------
        | Jika terjadi error database / Excel
        |--------------------------------------------------------------------------
        |
        | Transaction akan rollback.
        |
        */

        report($e);

        return back()
            ->with('open_import_modal', true)
            ->with('import_errors', [
                'Import gagal diproses. Tidak ada data User yang disimpan.'
            ]);
    }
}
}
