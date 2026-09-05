<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BillTypeController extends Controller
{
    /**
     * Daftar jenis tagihan.
     */
    public function index(
        Request $request
    ): View {

        $user = $request->user();

        Gate::authorize(
            'viewAny',
            BillType::class
        );


        /*
    |--------------------------------------------------------------------------
    | Organisasi yang menjadi kewenangan user
    |--------------------------------------------------------------------------
    */

        $organizationIds = $user
            ->organizations()
            ->where(
                'organizations.is_active',
                true
            )
            ->pluck(
                'organizations.id'
            );


        /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

        $organizationId =
            $request->input(
                'organization_id'
            );

        $status =
            $request->input(
                'status'
            );


        /*
    |--------------------------------------------------------------------------
    | Validasi organisasi
    |--------------------------------------------------------------------------
    |
    | Jangan sampai user memasukkan ID organisasi
    | yang bukan kewenangannya melalui URL.
    |
    */

        if (
            $organizationId !== null
            && ! $organizationIds->contains(
                (int) $organizationId
            )
        ) {
            $organizationId = null;
        }


        /*
    |--------------------------------------------------------------------------
    | Validasi status
    |--------------------------------------------------------------------------
    */

        if (
            $status !== null
            && ! in_array(
                $status,
                [
                    'active',
                    'inactive',
                ],
                true
            )
        ) {
            $status = null;
        }


        /*
    |--------------------------------------------------------------------------
    | Query jenis tagihan
    |--------------------------------------------------------------------------
    */

        $billTypes = BillType::query()
            ->with('organization')

            ->whereIn(
                'organization_id',
                $organizationIds
            )

            ->when(
                $organizationId,
                fn($query) =>
                $query->where(
                    'organization_id',
                    $organizationId
                )
            )

            ->when(
                $status === 'active',
                fn($query) =>
                $query->where(
                    'is_active',
                    true
                )
            )

            ->when(
                $status === 'inactive',
                fn($query) =>
                $query->where(
                    'is_active',
                    false
                )
            )

            ->orderBy(
                'organization_id'
            )

            ->orderBy(
                'name'
            )

            ->get();


        /*
    |--------------------------------------------------------------------------
    | Organisasi untuk filter dan modal
    |--------------------------------------------------------------------------
    */

        $organizations = $user
            ->organizations()
            ->where(
                'organizations.is_active',
                true
            )
            ->orderBy(
                'name'
            )
            ->get();


        return view(
            'admin.finance.bill-types.index',
            compact(
                'billTypes',
                'organizations',
                'organizationId',
                'status'
            )
        );
    }

    /**
     * Menyimpan jenis tagihan baru.
     */
    /**
     * Menyimpan jenis tagihan baru.
     */
    public function store(
        Request $request
    ): RedirectResponse {

        Gate::authorize(
            'create',
            BillType::class
        );


        /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([

            'organization_id' => [
                'required',
                'integer',
                'exists:organizations,id',
            ],

            'code' => [
                'required',
                'string',
                'max:50',
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Pastikan user memang memiliki akses
    | ke organisasi yang dipilih
    |--------------------------------------------------------------------------
    */

        $hasOrganizationAccess = $request
            ->user()
            ->organizations()
            ->where(
                'organizations.id',
                $validated['organization_id']
            )
            ->where(
                'organizations.is_active',
                true
            )
            ->exists();


        if (! $hasOrganizationAccess) {

            abort(
                403,
                'Anda tidak memiliki akses ke organisasi tersebut.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Normalisasi kode
    |--------------------------------------------------------------------------
    */

        $validated['code'] =
            strtoupper(
                trim(
                    $validated['code']
                )
            );


        /*
    |--------------------------------------------------------------------------
    | Pastikan kode unik dalam organisasi
    |--------------------------------------------------------------------------
    */

        $codeExists = BillType::query()
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where(
                'code',
                $validated['code']
            )
            ->exists();


        if ($codeExists) {

            return back()
                ->withInput()
                ->withErrors([
                    'code' =>
                    'Kode jenis tagihan tersebut sudah digunakan oleh organisasi ini.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

        $validated['is_active'] =
            $request->boolean('is_active');


        /*
    |--------------------------------------------------------------------------
    | Simpan
    |--------------------------------------------------------------------------
    */

        BillType::create(
            $validated
        );


        return redirect()
            ->route(
                'admin.finance.bill-types.index'
            )
            ->with(
                'success',
                'Jenis tagihan berhasil ditambahkan.'
            );
    }

    /**
     * Mengubah jenis tagihan.
     */
    public function update(
        Request $request,
        BillType $billType
    ): RedirectResponse {

        Gate::authorize(
            'update',
            $billType
        );


        /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([

            'organization_id' => [
                'required',
                'integer',
                'exists:organizations,id',
            ],

            'code' => [
                'required',
                'string',
                'max:50',
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Pastikan user memiliki akses ke organisasi
    |--------------------------------------------------------------------------
    */

        $hasOrganizationAccess = $request
            ->user()
            ->organizations()
            ->where(
                'organizations.id',
                $validated['organization_id']
            )
            ->where(
                'organizations.is_active',
                true
            )
            ->exists();


        if (! $hasOrganizationAccess) {

            abort(
                403,
                'Anda tidak memiliki akses ke organisasi tersebut.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Normalisasi kode
    |--------------------------------------------------------------------------
    */

        $validated['code'] =
            strtoupper(
                trim(
                    $validated['code']
                )
            );


        /*
    |--------------------------------------------------------------------------
    | Cek duplikasi kode
    |--------------------------------------------------------------------------
    |
    | Record yang sedang diedit harus dikecualikan.
    |
    */

        $codeExists = BillType::query()
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where(
                'code',
                $validated['code']
            )
            ->where(
                'id',
                '!=',
                $billType->id
            )
            ->exists();


        if ($codeExists) {

            return back()
                ->withInput()
                ->withErrors([
                    'code' =>
                    'Kode jenis tagihan tersebut sudah digunakan oleh organisasi ini.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

        $validated['is_active'] =
            $request->boolean('is_active');


        /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

        $billType->update(
            $validated
        );


        return redirect()
            ->route(
                'admin.finance.bill-types.index'
            )
            ->with(
                'success',
                'Jenis tagihan berhasil diperbarui.'
            );
    }

    /**
     * Menghapus jenis tagihan.
     */
    /**
     * Menghapus jenis tagihan.
     */
    public function destroy(
        BillType $billType
    ): RedirectResponse {

        Gate::authorize(
            'delete',
            $billType
        );


        /*
    |--------------------------------------------------------------------------
    | Hapus
    |--------------------------------------------------------------------------
    */

        $billType->delete();


        return redirect()
            ->route(
                'admin.finance.bill-types.index'
            )
            ->with(
                'success',
                'Jenis tagihan berhasil dihapus.'
            );
    }
}
