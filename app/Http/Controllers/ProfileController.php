<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use Intervention\Image\Format;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill(
            $request->safe()->only([
                'name',
                'email',
            ])
        );

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Foto Profil
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('profile_photo')) {
            $oldPhoto = $user->profile_photo_path;

            $image = Image::decode(
                $request->file('profile_photo')
            );

            $image->scaleDown(
                width: 600,
                height: 600
            );

            $filename = Str::uuid() . '.webp';
            $path = 'profile-photos/' . $filename;

            Storage::disk('public')->put(
                $path,
                $image->encodeUsingFormat(
                    Format::WEBP,
                    quality: 80
                )
            );

            $user->profile_photo_path = $path;

            if ($oldPhoto && $oldPhoto !== $path) {
                Storage::disk('public')->delete($oldPhoto);
            }
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's profile photo.
     */
    public function removePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete(
                $user->profile_photo_path
            );

            $user->profile_photo_path = null;
            $user->save();
        }

        return Redirect::route('profile.edit')
            ->with('status', 'profile-photo-removed');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Hapus Foto Profil Sebelum User Dihapus
        |--------------------------------------------------------------------------
        */

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete(
                $user->profile_photo_path
            );
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
