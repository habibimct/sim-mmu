<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUnitProfileRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Format;
use Intervention\Image\Laravel\Facades\Image;

class UnitProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $unitsQuery = Organization::query()
            ->where('type', 'unit')
            ->where('is_active', true);

        /*
         * Admin Sistem / user dengan organizations.manage
         * dapat mengelola seluruh Unit.
         */
        if (! $user->hasPermission('organizations.manage')) {
            $organizationIds = $user->organizations()
                ->where('type', 'unit')
                ->pluck('organizations.id');

            $unitsQuery->whereIn('id', $organizationIds);
        }

        $units = $unitsQuery
            ->orderBy('name')
            ->get();

        $selectedUnit = null;

        if ($units->isNotEmpty()) {
            $unitId = $request->integer('unit_id');

            $selectedUnit = $units->firstWhere('id', $unitId)
                ?? $units->first();
        }

        return view('admin.settings.profile-unit.index', [
            'units' => $units,
            'selectedUnit' => $selectedUnit,
        ]);
    }

    public function update(
        UpdateUnitProfileRequest $request,
        Organization $unit
    ): RedirectResponse {
        $this->authorizeUnit($request, $unit);

        $unit->address = $request->validated('address');
        $unit->phone = $request->validated('phone');
        $unit->email = $request->validated('email');
        $unit->website = $request->validated('website');

        if ($request->hasFile('logo')) {
            $oldLogo = $unit->logo_path;

            $image = Image::decode(
                $request->file('logo')
            );

            $image->scaleDown(
                width: 800,
                height: 800
            );

            $filename = Str::uuid() . '.webp';
            $path = 'organization-logos/' . $filename;

            Storage::disk('public')->put(
                $path,
                $image->encodeUsingFormat(
                    Format::WEBP,
                    quality: 85
                )
            );

            $unit->logo_path = $path;

            if ($oldLogo && $oldLogo !== $path) {
                Storage::disk('public')->delete($oldLogo);
            }
        }

        $unit->save();

        return redirect()
            ->route('admin.settings.profile-unit.index', [
                'unit_id' => $unit->id,
            ])
            ->with('success', 'Profil Unit berhasil diperbarui.');
    }

    public function removeLogo(
        Request $request,
        Organization $unit
    ): RedirectResponse {
        $this->authorizeUnit($request, $unit);

        if ($unit->logo_path) {
            Storage::disk('public')->delete(
                $unit->logo_path
            );

            $unit->logo_path = null;
            $unit->save();
        }

        return redirect()
            ->route('admin.settings.profile-unit.index', [
                'unit_id' => $unit->id,
            ])
            ->with('success', 'Logo Unit berhasil dihapus.');
    }

    private function authorizeUnit(
        Request $request,
        Organization $unit
    ): void {
        abort_unless(
            $unit->type === 'unit',
            404
        );

        $user = $request->user();

        /*
         * User yang memiliki organizations.manage
         * dapat mengelola seluruh Unit.
         */
        if ($user->hasPermission('organizations.manage')) {
            return;
        }

        /*
         * User biasa hanya boleh mengelola Unit
         * yang menjadi organization scope-nya.
         */
        abort_unless(
            $user->organizations()
                ->where('organizations.id', $unit->id)
                ->where('organizations.type', 'unit')
                ->exists(),
            403
        );
    }
}
