@extends('layouts.wali')

@section('title', 'Profil Saya')

@section('content')
    <div class="space-y-5">

        <div>
            <h1 class="text-xl font-bold text-gray-800">
                Profil Saya
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Informasi akun Wali / Orang Tua.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50
                    px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50
                    px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">
                    Periksa kembali data yang diisi.
                </p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Identitas wali --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm
                ring-1 ring-gray-100">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-14 w-14 shrink-0 items-center
                        justify-center rounded-full bg-green-100
                        text-xl font-bold text-green-700">
                    {{ strtoupper(substr($guardian->name ?: 'W', 0, 1)) }}
                </div>

                <div class="min-w-0">
                    <h2 class="font-bold text-gray-800">
                        {{ $guardian->name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Wali / Orang Tua
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        @php
                            $guardianOrganizations = $guardian->students
                                ->pluck('organization')
                                ->filter()
                                ->unique('id')
                                ->sortBy('name');
                        @endphp

                        @forelse ($guardianOrganizations as $organization)
                            <span class="badge bg-light text-dark me-1 mb-1">
                                {{ $organization->code }}
                            </span>
                        @empty
                            <span class="text-muted">-</span>
                        @endforelse
                    </p>
                </div>
            </div>
        </div>

        {{-- Informasi akun --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm
                ring-1 ring-gray-100">
            <h2 class="mb-4 font-semibold text-gray-800">
                Informasi Akun
            </h2>

            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500">NIK</p>
                    <p class="mt-1 text-sm font-medium text-gray-800">
                        {{ $guardian->nik ?: '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Form edit profil --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm
                ring-1 ring-gray-100">
            <h2 class="font-semibold text-gray-800">
                Perbarui Profil
            </h2>
            <p class="mt-1 mb-5 text-sm text-gray-500">
                Pastikan nama dan nomor HP selalu benar.
            </p>

            <form action="{{ route('wali.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Nama lengkap
                    </label>

                    <input type="text" id="name" value="{{ $guardian->name }}" readonly
                        class="w-full cursor-not-allowed rounded-xl border border-gray-200
                        bg-gray-100 px-4 py-3 text-sm text-gray-600">

                    <p class="mt-1.5 text-xs text-gray-500">
                        Nama tidak dapat diubah melalui profil. Hubungi admin jika terdapat kesalahan.
                    </p>
                </div>

                <div>
                    <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Nomor HP / WhatsApp
                    </label>

                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $guardian->phone) }}"
                        maxlength="30" autocomplete="tel" placeholder="Contoh: 081234567890"
                        class="w-full rounded-xl border border-gray-300
                           px-4 py-3 text-sm text-gray-800
                           focus:border-green-600 focus:outline-none
                           focus:ring-2 focus:ring-green-100">
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-green-600 px-4 py-3
                       text-sm font-semibold text-white
                       transition hover:bg-green-700
                       focus:outline-none focus:ring-2
                       focus:ring-green-500 focus:ring-offset-2">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <p class="px-2 text-center text-xs text-gray-400">
            Jika NIK perlu diperbaiki, silahkan hubungi administrator.
        </p>


        {{-- Ganti kata sandi --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <h2 class="font-semibold text-gray-800">
                Keamanan Akun
            </h2>

            <p class="mt-1 mb-5 text-sm text-gray-500">
                Gunakan kata sandi yang kuat dan jangan membagikannya kepada orang lain.
            </p>

            @if (session('password_success'))
                <div
                    class="mb-4 rounded-xl border border-green-200
                    bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('password_success') }}
                </div>
            @endif

            @if ($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
                <div
                    class="mb-4 rounded-xl border border-red-200
                    bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-inside list-disc">
                        @foreach (['current_password', 'password', 'password_confirmation'] as $field)
                            @foreach ($errors->get($field) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('wali.profile.password.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Kata sandi saat ini
                    </label>
                    <input type="password" id="current_password" name="current_password" required
                        autocomplete="current-password"
                        class="w-full rounded-xl border border-gray-300
                       px-4 py-3 text-sm focus:border-green-600
                       focus:outline-none focus:ring-2 focus:ring-green-100">
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Kata sandi baru
                    </label>
                    <input type="password" id="password" name="password" required minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-300
                       px-4 py-3 text-sm focus:border-green-600
                       focus:outline-none focus:ring-2 focus:ring-green-100">
                    <p class="mt-1 text-xs text-gray-500">
                        Minimal 8 karakter.
                    </p>
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Konfirmasi kata sandi baru
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-300
                       px-4 py-3 text-sm focus:border-green-600
                       focus:outline-none focus:ring-2 focus:ring-green-100">
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-gray-800 px-4 py-3
                   text-sm font-semibold text-white transition
                   hover:bg-gray-900 focus:outline-none
                   focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Ganti Kata Sandi
                </button>
            </form>
        </div>

    </div>
@endsection
