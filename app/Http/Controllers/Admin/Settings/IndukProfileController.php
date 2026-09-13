<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateIndukProfileRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Format;
use Intervention\Image\Laravel\Facades\Image;

class IndukProfileController extends Controller
{
    public function edit(): View
    {
        $induk = Organization::query()
            ->where('type', 'induk')
            ->firstOrFail();

        return view('admin.settings.profile-induk.edit', [
            'induk' => $induk,
        ]);
    }

    public function update(
        UpdateIndukProfileRequest $request
    ): RedirectResponse {
        $induk = Organization::query()
            ->where('type', 'induk')
            ->firstOrFail();

        $induk->name = $request->validated('name');
        $induk->address = $request->validated('address');
        $induk->phone = $request->validated('phone');
        $induk->email = $request->validated('email');
        $induk->website = $request->validated('website');

        if ($request->hasFile('logo')) {
            $oldLogo = $induk->logo_path;

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

            $induk->logo_path = $path;

            if ($oldLogo && $oldLogo !== $path) {
                Storage::disk('public')->delete($oldLogo);
            }
        }

        $induk->save();

        return redirect()
            ->route('admin.settings.profile-induk.edit')
            ->with('success', 'Profil Induk berhasil diperbarui.');
    }

    public function removeLogo(): RedirectResponse
    {
        $induk = Organization::query()
            ->where('type', 'induk')
            ->firstOrFail();

        if ($induk->logo_path) {
            Storage::disk('public')->delete(
                $induk->logo_path
            );

            $induk->logo_path = null;
            $induk->save();
        }

        return redirect()
            ->route('admin.settings.profile-induk.edit')
            ->with('success', 'Logo Induk berhasil dihapus.');
    }
}
