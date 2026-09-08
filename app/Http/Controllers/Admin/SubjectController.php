<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    /**
     * Daftar mata pelajaran.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Query mata pelajaran
    |--------------------------------------------------------------------------
    */

        $query = Subject::query()
            ->with('organization');


        /*
    |--------------------------------------------------------------------------
    | Hak akses melihat
    |--------------------------------------------------------------------------
    |
    | Admin Induk dapat melihat seluruh mata pelajaran.
    | Admin Unit hanya dapat melihat mata pelajaran unitnya.
    |
    */

        if (!$user->hasRole('admin_sistem')) {

            $organizationIds = $user
                ->organizations()
                ->where('is_active', true)
                ->pluck('organizations.id');

            $query->whereIn(
                'organization_id',
                $organizationIds
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Pencarian
    |--------------------------------------------------------------------------
    */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(function ($q) use ($search) {

                $q->where(
                    'code',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'name',
                        'like',
                        "%{$search}%"
                    );
            });
        }


        /*
    |--------------------------------------------------------------------------
    | Filter status
    |--------------------------------------------------------------------------
    */

        if ($request->status === 'active') {

            $query->where(
                'is_active',
                true
            );
        } elseif ($request->status === 'inactive') {

            $query->where(
                'is_active',
                false
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Data
    |--------------------------------------------------------------------------
    */

        $subjects = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();


        return view(
            'admin.subjects.index',
            compact('subjects')
        );
    }


    /**
     * Menyimpan mata pelajaran baru.
     */
    public function store(
        Request $request
    ): RedirectResponse {

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Organisasi user
        |--------------------------------------------------------------------------
        */

        $organizationIds = $user
            ->organizations()
            ->where('is_active', true)
            ->pluck('organizations.id');


        /*
        |--------------------------------------------------------------------------
        | Pastikan Admin Unit memiliki satu organisasi aktif
        |--------------------------------------------------------------------------
        */

        if ($organizationIds->count() !== 1) {

            abort(
                403,
                'Akun Anda tidak memiliki satu organisasi unit yang valid.'
            );
        }


        $organizationId =
            $organizationIds->first();


        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'code' => [
                'required',
                'string',
                'max:50',
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

        ]);


        $code = strtoupper(
            trim($validated['code'])
        );

        $name = trim(
            $validated['name']
        );


        /*
        |--------------------------------------------------------------------------
        | Cek kode duplikat
        |--------------------------------------------------------------------------
        */

        $exists = Subject::query()
            ->where(
                'organization_id',
                $organizationId
            )
            ->where(
                'code',
                $code
            )
            ->exists();


        if ($exists) {

            return back()
                ->withErrors([
                    'code' =>
                    'Kode mata pelajaran sudah digunakan.',
                ])
                ->withInput()
                ->with(
                    'open_subject_modal',
                    true
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan
        |--------------------------------------------------------------------------
        */

        Subject::create([

            'organization_id' =>
            $organizationId,

            'code' =>
            $code,

            'name' =>
            $name,

            'is_active' =>
            $request->boolean(
                'is_active',
                true
            ),

        ]);


        return redirect()
            ->route(
                'admin.subjects.index'
            )
            ->with(
                'success',
                'Mata pelajaran berhasil ditambahkan.'
            );
    }


    /**
     * Memperbarui mata pelajaran.
     */
    public function update(
        Request $request,
        Subject $subject
    ): RedirectResponse {

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Organisasi yang boleh dikelola user
        |--------------------------------------------------------------------------
        */

        $organizationIds = $user
            ->organizations()
            ->where('is_active', true)
            ->pluck('organizations.id');


        /*
        |--------------------------------------------------------------------------
        | Pastikan Subject berada dalam organisasi user
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $organizationIds->contains(
                $subject->organization_id
            ),
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'code' => [
                'required',
                'string',
                'max:50',
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

        ]);


        $code = strtoupper(
            trim($validated['code'])
        );

        $name = trim(
            $validated['name']
        );


        /*
        |--------------------------------------------------------------------------
        | Cek kode duplikat
        |--------------------------------------------------------------------------
        */

        $exists = Subject::query()
            ->where(
                'organization_id',
                $subject->organization_id
            )
            ->where(
                'code',
                $code
            )
            ->where(
                'id',
                '!=',
                $subject->id
            )
            ->exists();


        if ($exists) {

            return back()
                ->withErrors([
                    'code' =>
                    'Kode mata pelajaran sudah digunakan.',
                ])
                ->withInput()
                ->with(
                    'edit_subject_id',
                    $subject->id
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $subject->update([

            'code' =>
            $code,

            'name' =>
            $name,

            'is_active' =>
            $request->boolean(
                'is_active',
                false
            ),

        ]);


        return redirect()
            ->route(
                'admin.subjects.index'
            )
            ->with(
                'success',
                'Mata pelajaran berhasil diperbarui.'
            );
    }


    /**
     * Menghapus mata pelajaran.
     */
    public function destroy(
        Request $request,
        Subject $subject
    ): RedirectResponse {

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Organisasi yang boleh dikelola user
        |--------------------------------------------------------------------------
        */

        $organizationIds = $user
            ->organizations()
            ->where('is_active', true)
            ->pluck('organizations.id');


        /*
        |--------------------------------------------------------------------------
        | Pastikan Subject berada dalam organisasi user
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $organizationIds->contains(
                $subject->organization_id
            ),
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Hapus
        |--------------------------------------------------------------------------
        */

        $subject->delete();


        return redirect()
            ->route(
                'admin.subjects.index'
            )
            ->with(
                'success',
                'Mata pelajaran berhasil dihapus.'
            );
    }
}
