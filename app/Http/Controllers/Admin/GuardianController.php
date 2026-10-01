<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class GuardianController extends Controller
{
    /**
     * Daftar wali / orang tua.
     */
    public function index(Request $request): View
    {
        $organizationIds = Organization::accessibleIdsForUser();

        $query = Guardian::query()
            ->with([
                'user',
                'students.organization',
            ])
            ->withCount('students')
            ->whereHas('students', function ($q) use ($organizationIds) {
                $q->whereIn('organization_id', $organizationIds);
            });

        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'nik',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter organisasi
        |--------------------------------------------------------------------------
        */

        if ($request->filled('organization_id')) {
            $organizationId = (int) $request->organization_id;

            if (! $organizationIds->contains($organizationId)) {
                abort(403);
            }

            $query->whereHas('students', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
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
        | Data wali
        |--------------------------------------------------------------------------
        */

        $guardians = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Dropdown organisasi
        |--------------------------------------------------------------------------
        */

        $organizations = Organization::query()
            ->whereIn(
                'id',
                $organizationIds
            )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.guardians.index',
            compact(
                'guardians',
                'organizations'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $organizationIds = Organization::accessibleIdsForUser();

        $validated = $request->validate([
            'organization_id' => [
                'required',
                'integer',
                Rule::in($organizationIds->all()),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'nik' => [
                'required',
                'digits:16',
                'unique:guardians,nik',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            /*
        |--------------------------------------------------------------------------
        | Buat User terlebih dahulu
        |--------------------------------------------------------------------------
        */
            $user = User::create([
                'name' => $validated['name'],
                'email' => null,
                'password' => Hash::make(
                    'PMUB@' . $validated['nik']
                ),
                'is_active' => true,
            ]);

            $waliRole = Role::where('code', 'wali')->firstOrFail();

            $user->roles()->attach($waliRole->id);

            /*
        |--------------------------------------------------------------------------
        | Buat Guardian
        |--------------------------------------------------------------------------
        */
            Guardian::create([
                'user_id' => $user->id,
                'organization_id' => $validated['organization_id'],
                'name' => $validated['name'],
                'nik' => $validated['nik'],
                'phone' => $validated['phone'] ?? null,
                'initial_password' => 'PMUB@' . $validated['nik'],
                'is_active' => true,
            ]);
        });

        return redirect()
            ->route('admin.guardians.index')
            ->with('success', 'Wali / Orang Tua berhasil ditambahkan.');
    }


    public function update(
        Request $request,
        Guardian $guardian
    ): RedirectResponse {
        $organizationIds = Organization::accessibleIdsForUser();

        abort_unless(
            $organizationIds->contains($guardian->organization_id),
            403
        );

        $validated = $request->validate([
            'organization_id' => [
                'required',
                'integer',
                Rule::in($organizationIds->all()),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('guardians', 'nik')
                    ->ignore($guardian->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $guardian
        ) {
            $guardian->update([
                'organization_id' => $validated['organization_id'],
                'name' => $validated['name'],
                'nik' => $validated['nik'],
                'phone' => $validated['phone'] ?? null,
            ]);

            if ($guardian->user) {
                $guardian->user->update([
                    'name' => $validated['name'],
                ]);
            }
        });

        return redirect()
            ->route('admin.guardians.index')
            ->with(
                'success',
                'Data Wali / Orang Tua berhasil diperbarui.'
            );
    }

    public function toggleStatus(
        Guardian $guardian
    ): RedirectResponse {
        $organizationIds = Organization::accessibleIdsForUser();

        abort_unless(
            $organizationIds->contains($guardian->organization_id),
            403
        );

        DB::transaction(function () use ($guardian) {
            $guardian->update([
                'is_active' => ! $guardian->is_active,
            ]);

            if ($guardian->user) {
                $guardian->user->update([
                    'is_active' => $guardian->is_active,
                ]);
            }
        });

        return redirect()
            ->route('admin.guardians.index')
            ->with(
                'success',
                $guardian->is_active
                    ? 'Wali / Orang Tua berhasil diaktifkan.'
                    : 'Wali / Orang Tua berhasil dinonaktifkan.'
            );
    }

    public function students(
        Guardian $guardian
    ): View {
        $organizationIds = Organization::accessibleIdsForUser();

        /*
    |--------------------------------------------------------------------------
    | Guardian harus memiliki minimal satu anak yang berada di
    | organisasi yang dapat diakses user saat ini.
    |--------------------------------------------------------------------------
    */
        $hasAccessibleStudent = $guardian->students()
            ->whereIn('organization_id', $organizationIds)
            ->exists();

        abort_unless(
            $hasAccessibleStudent,
            403
        );

        $guardian->load([
            'students' => function ($query) {
                $query
                    ->with('organization')
                    ->orderBy('name');
            },
        ]);

        /*
    |--------------------------------------------------------------------------
    | Tampilkan siswa dari seluruh organisasi yang dapat diakses user.
    | Siswa yang sudah terhubung dengan Guardian tidak ditampilkan lagi.
    |--------------------------------------------------------------------------
    */
        $students = Student::query()
            ->with('organization')
            ->whereIn('organization_id', $organizationIds)
            ->whereNotIn(
                'id',
                $guardian->students->pluck('id')
            )
            ->orderBy('name')
            ->get();

        return view(
            'admin.guardians.partials.students-modal',
            compact(
                'guardian',
                'students'
            )
        );
    }

    public function attachStudent(
        Request $request,
        Guardian $guardian
    ) {
        $organizationIds = Organization::accessibleIdsForUser();

        /*
    |--------------------------------------------------------------------------
    | Guardian harus memiliki minimal satu anak yang dapat diakses
    | oleh user saat ini.
    |--------------------------------------------------------------------------
    */
        $hasAccessibleStudent = $guardian->students()
            ->whereIn('organization_id', $organizationIds)
            ->exists();

        abort_unless(
            $hasAccessibleStudent,
            403
        );

        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                Rule::exists('students', 'id'),
            ],
            'relationship' => [
                'required',
                'string',
                'max:50',
            ],
            'is_primary' => [
                'nullable',
                'boolean',
            ],
        ]);

        $student = Student::findOrFail(
            $validated['student_id']
        );

        /*
    |--------------------------------------------------------------------------
    | Siswa harus berada pada organisasi yang dapat diakses
    | oleh user yang sedang login.
    |--------------------------------------------------------------------------
    */
        abort_unless(
            $organizationIds->contains(
                $student->organization_id
            ),
            403
        );

        $guardian->students()->syncWithoutDetaching([
            $student->id => [
                'relationship' => $validated['relationship'],
                'is_primary' => $request->boolean('is_primary'),
            ],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil ditambahkan sebagai anak Wali.',
            ]);
        }

        return redirect()
            ->route('admin.guardians.index')
            ->with(
                'success',
                'Siswa berhasil ditambahkan sebagai anak Wali.'
            );
    }

    public function detachStudent(
        Request $request,
        Guardian $guardian,
        Student $student
    ) {
        $organizationIds = Organization::accessibleIdsForUser();

        /*
    |--------------------------------------------------------------------------
    | Guardian harus memiliki minimal satu anak yang dapat diakses
    | oleh user saat ini.
    |--------------------------------------------------------------------------
    */
        $hasAccessibleStudent = $guardian->students()
            ->whereIn('organization_id', $organizationIds)
            ->exists();

        abort_unless(
            $hasAccessibleStudent,
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Siswa yang akan dilepas juga harus berada pada organisasi
    | yang dapat diakses user.
    |--------------------------------------------------------------------------
    */
        abort_unless(
            $organizationIds->contains(
                $student->organization_id
            ),
            403
        );

        $guardian->students()->detach(
            $student->id
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hubungan Wali dengan siswa berhasil dihapus.',
            ]);
        }

        return redirect()
            ->route('admin.guardians.index')
            ->with(
                'success',
                'Hubungan Wali dengan siswa berhasil dihapus.'
            );
    }

    public function generatePassword(
        Guardian $guardian
    ): RedirectResponse {
        $organizationIds = Organization::accessibleIdsForUser();

        abort_unless(
            $organizationIds->contains($guardian->organization_id),
            403
        );

        abort_unless(
            $guardian->user_id !== null,
            422,
            'Akun Wali belum tersedia.'
        );

        $password = 'PMUB@' . Str::random(8);

        DB::transaction(function () use ($guardian, $password) {
            $guardian->user->update([
                'password' => Hash::make($password),
                'initial_password' => $password,
            ]);

            $guardian->update([
                'initial_password' => $password,
            ]);
        });

        return redirect()
            ->route('admin.guardians.index')
            ->with(
                'success',
                'Password Wali berhasil dibuat ulang.'
            );
    }
}
