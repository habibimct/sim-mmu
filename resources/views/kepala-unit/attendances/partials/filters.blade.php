<form
    method="GET"
    action="{{ route('kepala-unit.attendances.index') }}"
    class="bg-white rounded-xl border border-gray-200 shadow-sm p-4"
>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Tahun Akademik --}}
        <div>

            <label
                for="academic_year_id"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Tahun Akademik
            </label>

            <select
                name="academic_year_id"
                id="academic_year_id"
                class="w-full rounded-lg border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500"
            >

                @foreach ($academicYears as $academicYear)

                    <option
                        value="{{ $academicYear->id }}"
                        @selected(
                            $academicYearId == $academicYear->id
                        )
                    >
                        {{ $academicYear->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- Tanggal --}}
        <div>

            <label
                for="date"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Tanggal
            </label>

            <input
                type="date"
                name="date"
                id="date"
                value="{{ $date }}"
                class="w-full rounded-lg border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500"
            >

        </div>


        {{-- Kelas --}}
        <div>

            <label
                for="school_class_id"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Kelas
            </label>

            <select
                name="school_class_id"
                id="school_class_id"
                class="w-full rounded-lg border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500"
            >

                <option value="">
                    Semua Kelas
                </option>

                @foreach ($classes as $class)

                    <option
                        value="{{ $class->id }}"
                        @selected(
                            request('school_class_id') == $class->id
                        )
                    >
                        {{ $class->name }}
                    </option>

                @endforeach

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
            Tampilkan
        </button>


        <a
            href="{{ route('kepala-unit.attendances.index') }}"
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
            Hari Ini
        </a>

    </div>

</form>
