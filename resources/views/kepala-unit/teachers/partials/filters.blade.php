<form
    method="GET"
    action="{{ route('kepala-unit.teachers.index') }}"
    class="bg-white rounded-xl border border-gray-200 shadow-sm p-4"
>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

        {{-- Pencarian --}}
        <div>

            <label
                for="search"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
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
                       focus:ring-indigo-500"
            >

        </div>


        {{-- Unit --}}
        @if ($organizations->count() > 1)

            <div>

                <label
                    for="organization_id"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Unit
                </label>

                <select
                    name="organization_id"
                    id="organization_id"
                    class="w-full rounded-lg border-gray-300
                           focus:border-indigo-500
                           focus:ring-indigo-500"
                >

                    <option value="">
                        Semua Unit
                    </option>

                    @foreach ($organizations as $organization)

                        <option
                            value="{{ $organization->id }}"
                            @selected(
                                request('organization_id') == $organization->id
                            )
                        >
                            {{ $organization->name }}
                        </option>

                    @endforeach

                </select>

            </div>

        @else

            <div>

                <label
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Unit
                </label>

                <div
                    class="w-full rounded-lg
                           border border-gray-300
                           bg-gray-50
                           px-3 py-2
                           text-sm text-gray-600"
                >
                    {{ $organizations->first()?->name ?? '-' }}
                </div>

            </div>

        @endif


        {{-- Status --}}
        <div>

            <label
                for="status"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Status
            </label>

            <select
                name="status"
                id="status"
                class="w-full rounded-lg border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500"
            >

                <option value="">
                    Semua Status
                </option>

                <option
                    value="active"
                    @selected(request('status') === 'active')
                >
                    Aktif
                </option>

                <option
                    value="inactive"
                    @selected(request('status') === 'inactive')
                >
                    Tidak Aktif
                </option>

            </select>

        </div>

    </div>


    {{-- Tombol --}}
    <div class="mt-4 flex items-center gap-2">

        <button
            type="submit"
            class="inline-flex items-center
                   px-4 py-2
                   rounded-lg
                   bg-indigo-600
                   text-white
                   text-sm
                   font-medium
                   hover:bg-indigo-700
                   focus:outline-none
                   focus:ring-2
                   focus:ring-indigo-500"
        >
            Filter
        </button>


        @if (
            request()->filled('search') ||
            request()->filled('organization_id') ||
            request()->filled('status')
        )

            <a
                href="{{ route('kepala-unit.teachers.index') }}"
                class="inline-flex items-center
                       px-4 py-2
                       rounded-lg
                       bg-gray-100
                       border border-gray-300
                       text-gray-700
                       text-sm
                       font-medium
                       hover:bg-gray-200"
            >
                Reset
            </a>

        @endif

    </div>

</form>
