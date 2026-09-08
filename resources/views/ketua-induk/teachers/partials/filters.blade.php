<form method="GET"
      action="{{ route('ketua-induk.teachers.index') }}"
      class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Pencarian --}}
        <div class="md:col-span-2">

            <label
                for="search"
                class="block text-sm font-medium text-gray-700 mb-1">
                Cari Guru
            </label>

            <input
                type="text"
                name="search"
                id="search"
                value="{{ request('search') }}"
                placeholder="NIK, nama, atau email..."
                class="w-full rounded-lg border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500">
        </div>


        {{-- Organisasi --}}
        <div>

            <label
                for="organization_id"
                class="block text-sm font-medium text-gray-700 mb-1">
                Unit
            </label>

            <select
                name="organization_id"
                id="organization_id"
                class="w-full rounded-lg border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500">

                <option value="">
                    Semua Unit
                </option>

                @foreach ($organizations as $organization)

                    <option
                        value="{{ $organization->id }}"
                        @selected(request('organization_id') == $organization->id)>
                        {{ $organization->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- Status --}}
        <div>

            <label
                for="status"
                class="block text-sm font-medium text-gray-700 mb-1">
                Status
            </label>

            <select
                name="status"
                id="status"
                class="w-full rounded-lg border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500">

                <option value="">
                    Semua Status
                </option>

                <option
                    value="active"
                    @selected(request('status') === 'active')>
                    Aktif
                </option>

                <option
                    value="inactive"
                    @selected(request('status') === 'inactive')>
                    Tidak Aktif
                </option>

            </select>

        </div>

    </div>


    {{-- Tombol --}}
    <div class="mt-4 flex items-center gap-2">

        <button
            type="submit"
            class="inline-flex items-center px-4 py-2
                   bg-indigo-600 border border-transparent
                   rounded-lg font-semibold text-xs text-white
                   uppercase tracking-widest
                   hover:bg-indigo-700
                   focus:outline-none focus:ring-2
                   focus:ring-indigo-500">

            <svg
                class="w-4 h-4 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m21 21-4.35-4.35
                       m1.35-5.65a7 7 0 1 1-14 0
                       7 7 0 0 1 14 0Z" />

            </svg>

            Filter

        </button>


        @if (
            request()->filled('search') ||
            request()->filled('organization_id') ||
            request()->filled('status')
        )

            <a
                href="{{ route('ketua-induk.teachers.index') }}"
                class="inline-flex items-center px-4 py-2
                       bg-gray-100 border border-gray-300
                       rounded-lg font-semibold text-xs
                       text-gray-700 uppercase tracking-widest
                       hover:bg-gray-200">

                Reset

            </a>

        @endif

    </div>

</form>
