<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Perbarui nama, foto profil, dan alamat email akun Anda.') }}
        </p>
    </header>

    {{-- Foto Profil --}}
    <div class="mt-6">
        <div class="flex items-center gap-5">

            <div>
                @if ($user->profile_photo_path)
                    <img
                        src="{{ asset('storage/' . $user->profile_photo_path) }}"
                        alt="Foto Profil"
                        class="h-24 w-24 rounded-full object-cover border border-gray-200"
                    >
                @else
                    <div class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center border border-gray-200">
                        <svg
                            class="h-12 w-12 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 19.5a7.5 7.5 0 0115 0"
                            />
                        </svg>
                    </div>
                @endif
            </div>

            <div>
                <label
                    for="profile_photo"
                    class="block text-sm font-medium text-gray-700"
                >
                    Foto Profil
                </label>

                <input
                    id="profile_photo"
                    name="profile_photo"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    class="mt-2 block w-full text-sm text-gray-600"
                    form="profile-information-form"
                >

                <p class="mt-1 text-xs text-gray-500">
                    JPG, PNG, atau WebP. Maksimal 5 MB.
                </p>

                <x-input-error
                    class="mt-2"
                    :messages="$errors->get('profile_photo')"
                />
            </div>
        </div>

        @if ($user->profile_photo_path)
            <form
                method="post"
                action="{{ route('profile.photo.destroy') }}"
                class="mt-3"
            >
                @csrf
                @method('delete')

                <button
                    type="submit"
                    class="text-sm text-red-600 hover:text-red-800"
                    onclick="return confirm('Hapus foto profil?')"
                >
                    Hapus Foto
                </button>
            </form>
        @endif
    </div>

    {{-- Informasi User --}}
    <form
        id="profile-information-form"
        method="post"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="mt-6 space-y-6"
    >
        @csrf
        @method('patch')

        <div>
            <x-input-label
                for="name"
                :value="__('Nama')"
            />

            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('name')"
            />
        </div>

        <div>
            <x-input-label
                for="email"
                :value="__('Email')"
            />

            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('email')"
            />

            @if (
                $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
                && ! $user->hasVerifiedEmail()
            )
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Alamat email Anda belum diverifikasi.') }}

                        <button
                            form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900"
                        >
                            {{ __('Kirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Link verifikasi baru telah dikirim ke email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                {{ __('Simpan') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >
                    {{ __('Tersimpan.') }}
                </p>
            @endif
        </div>
    </form>

    <form
        id="send-verification"
        method="post"
        action="{{ route('verification.send') }}"
        class="hidden"
    >
        @csrf
    </form>
</section>
