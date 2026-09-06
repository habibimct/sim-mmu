<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        $organizations = Organization::withCount([
            'studentAcademicYears',
            'financeTransactions',
        ])
            ->orderByRaw(
                "CASE WHEN type = 'INDUK' THEN 0 ELSE 1 END"
            )
            ->orderBy('name')
            ->get();

        return view('admin.organizations.index', compact('organizations'));
    }


    public function create(): View
    {
        return view('admin.organizations.create');
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:organizations,code',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                'in:INDUK,UNIT',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Hanya boleh ada satu INDUK
        |--------------------------------------------------------------------------
        */

        if ($validated['type'] === 'INDUK') {

            $indukExists = Organization::where('type', 'INDUK')->exists();

            if ($indukExists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'type' => 'Organisasi INDUK sudah ada. Sistem hanya mengizinkan satu INDUK.',
                    ]);
            }
        }


        Organization::create($validated);


        return redirect()
            ->route('admin.organizations.index')
            ->with('success', 'Organisasi berhasil ditambahkan.');
    }


    public function edit(Organization $organization): View
    {
        return view('admin.organizations.edit', compact('organization'));
    }


    public function update(
        Request $request,
        Organization $organization
    ): RedirectResponse {

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('organizations', 'code')
                    ->ignore($organization->id),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                'in:INDUK,UNIT',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | INDUK tidak boleh diubah menjadi UNIT
        |--------------------------------------------------------------------------
        */

        if (
            $organization->type === 'INDUK'
            && $validated['type'] !== 'INDUK'
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'type' => 'Organisasi INDUK tidak boleh diubah menjadi UNIT.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Unit tidak boleh diubah menjadi INDUK jika INDUK sudah ada
        |--------------------------------------------------------------------------
        */

        if (
            $organization->type !== 'INDUK'
            && $validated['type'] === 'INDUK'
        ) {

            $indukExists = Organization::where('type', 'INDUK')
                ->where('id', '!=', $organization->id)
                ->exists();

            if ($indukExists) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'type' => 'Organisasi INDUK sudah ada. Sistem hanya mengizinkan satu INDUK.',
                    ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | INDUK harus selalu aktif
        |--------------------------------------------------------------------------
        */

        if (
            $organization->type === 'INDUK'
            && !$validated['is_active']
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'is_active' => 'Organisasi INDUK tidak boleh dinonaktifkan.',
                ]);
        }


        $organization->update($validated);


        return redirect()
            ->route('admin.organizations.index')
            ->with('success', 'Organisasi berhasil diperbarui.');
    }


    public function toggleStatus(Organization $organization): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | INDUK tidak boleh dinonaktifkan
        |--------------------------------------------------------------------------
        */

        if (
            $organization->type === 'INDUK'
            && $organization->is_active
        ) {

            return back()->withErrors([
                'status' => 'Organisasi INDUK tidak boleh dinonaktifkan.',
            ]);
        }


        $organization->update([
            'is_active' => !$organization->is_active,
        ]);


        $status = $organization->is_active
            ? 'diaktifkan'
            : 'dinonaktifkan';


        return back()->with(
            'success',
            "Organisasi {$organization->name} berhasil {$status}."
        );
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        /*
    |--------------------------------------------------------------------------
    | Organisasi INDUK tidak boleh dihapus
    |--------------------------------------------------------------------------
    */

        if ($organization->type === 'INDUK') {
            return back()->withErrors([
                'delete' => 'Organisasi INDUK tidak boleh dihapus.',
            ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Tidak boleh dihapus jika sudah memiliki siswa
    |--------------------------------------------------------------------------
    */

        if ($organization->studentAcademicYears()->exists()) {
            return back()->withErrors([
                'delete' => "Organisasi {$organization->name} tidak dapat dihapus karena sudah memiliki data siswa.",
            ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Tidak boleh dihapus jika sudah memiliki transaksi keuangan
    |--------------------------------------------------------------------------
    */

        if ($organization->financeTransactions()->exists()) {
            return back()->withErrors([
                'delete' => "Organisasi {$organization->name} tidak dapat dihapus karena sudah memiliki transaksi keuangan.",
            ]);
        }


        $organizationName = $organization->name;

        $organization->delete();


        return redirect()
            ->route('admin.organizations.index')
            ->with(
                'success',
                "Organisasi {$organizationName} berhasil dihapus."
            );
    }
}
