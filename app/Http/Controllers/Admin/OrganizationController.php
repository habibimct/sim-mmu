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
                "CASE WHEN type = 'induk' THEN 0 ELSE 1 END"
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
                'in:induk,unit',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Hanya boleh ada satu induk
        |--------------------------------------------------------------------------
        */

        if ($validated['type'] === 'induk') {

            if (!$validated['is_active']) {
                return back()->withInput()->withErrors([
                    'is_active' => 'Organisasi induk harus selalu aktif.',
                ]);
            }

            $indukExists = Organization::where('type', 'induk')->exists();

            if ($indukExists) {
                return back()->withInput()->withErrors([
                    'type' => 'Organisasi induk sudah ada. Sistem hanya mengizinkan satu induk.',
                ]);
            }
        }

        $validated['parent_id'] = $validated['type'] === 'unit'
            ? 1
            : null;

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
                'in:induk,unit',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | induk tidak boleh diubah menjadi UNIT
        |--------------------------------------------------------------------------
        */

        if (
            $organization->type === 'induk'
            && $validated['type'] !== 'induk'
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'type' => 'Organisasi induk tidak boleh diubah menjadi unit.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Unit tidak boleh diubah menjadi induk jika induk sudah ada
        |--------------------------------------------------------------------------
        */

        if (
            $organization->type !== 'induk'
            && $validated['type'] === 'induk'
        ) {

            $indukExists = Organization::where('type', 'induk')
                ->where('id', '!=', $organization->id)
                ->exists();

            if ($indukExists) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'type' => 'Organisasi induk sudah ada. Sistem hanya mengizinkan satu induk.',
                    ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | induk harus selalu aktif
        |--------------------------------------------------------------------------
        */

        if (
            $organization->type === 'induk'
            && !$validated['is_active']
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'is_active' => 'Organisasi induk tidak boleh dinonaktifkan.',
                ]);
        }


        $validated['parent_id'] = $validated['type'] === 'unit'
            ? 1
            : null;

        $organization->update($validated);


        return redirect()
            ->route('admin.organizations.index')
            ->with('success', 'Organisasi berhasil diperbarui.');
    }


    public function toggleStatus(Organization $organization): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | induk tidak boleh dinonaktifkan
        |--------------------------------------------------------------------------
        */

        if (
            $organization->type === 'induk'
            && $organization->is_active
        ) {

            return back()->withErrors([
                'status' => 'Organisasi induk tidak boleh dinonaktifkan.',
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
    | Organisasi induk tidak boleh dihapus
    |--------------------------------------------------------------------------
    */

        if ($organization->type === 'induk') {
            return back()->withErrors([
                'delete' => 'Organisasi induk tidak boleh dihapus.',
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
