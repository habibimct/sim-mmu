<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $guardian = $user->guardian()
            ->with('organization')
            ->firstOrFail();

        return view('wali.profile.index', compact(
            'user',
            'guardian'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $guardian = $user->guardian()->firstOrFail();

        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $guardian->update([
            'phone' => $validated['phone'] ?? null,
        ]);

        return redirect()
            ->route('wali.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }


    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('wali.profile')
            ->with('password_success', 'Kata sandi berhasil diperbarui.');
    }
}
