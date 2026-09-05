{{-- Modal Filter --}}
<div x-data="{ open: false }" x-on:open-attendance-filter.window="open = true" x-on:keydown.escape.window="open = false"
    x-show="open" x-cloak class="fixed inset-0 z-[60] overflow-y-auto">
    <div class="fixed inset-0 bg-black/50" x-show="open" x-transition.opacity @click="open = false"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">

        <div class="relative w-full max-w-lg
                   bg-white
                   rounded-2xl
                   shadow-2xl
                   overflow-visible"
            x-show="open" x-transition @click.stop>

            {{-- Header Modal --}}
            <div class="px-6 py-5 border-b border-gray-100">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="text-lg font-bold text-gray-800">
                            Filter Absensi
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Pilih data yang ingin ditampilkan.
                        </p>
                    </div>

                    <button type="button" @click="open = false"
                        class="w-9 h-9
                               flex items-center justify-center
                               rounded-lg
                               text-gray-400
                               hover:bg-gray-100
                               hover:text-gray-600
                               transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>

                </div>

            </div>


            {{-- Form Filter --}}
            <form method="GET" action="{{ route('guru.attendance.index') }}">

                <div class="p-6 space-y-5">

                    {{-- Tahun Akademik --}}
                    <div>
                        <x-tailwind-select name="academic_year_id" label="Tahun Akademik" :options="$academicYears->pluck('name', 'id')"
                            :selected="$academicYearId" placeholder="Semua Tahun Akademik" placeholder-value="all" />
                    </div>


                    {{-- Kelas --}}
                    <div>
                        <x-tailwind-select name="school_class_id" label="Kelas" :options="$filterClasses->pluck('name', 'id')" :selected="$schoolClassId"
                            placeholder="Semua Kelas" placeholder-value="all" />
                    </div>


                    {{-- Mata Pelajaran --}}
                    <div>
                        <x-tailwind-select name="subject_id" label="Mata Pelajaran" :options="$filterSubjects->pluck('name', 'id')" :selected="$subjectId"
                            placeholder="Semua Mata Pelajaran" placeholder-value="all" />
                    </div>
                </div>


                {{-- Footer Modal --}}
                <div
                    class="px-6 py-4
                           bg-gray-50
                           border-t border-gray-100
                           flex items-center justify-end gap-3">

                    <button type="button" @click="open = false"
                        class="px-4 py-2.5
                               rounded-lg
                               text-sm font-semibold
                               text-gray-600
                               hover:bg-gray-200
                               transition">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-4 py-2.5
                               rounded-lg
                               bg-blue-600
                               text-white
                               text-sm font-semibold
                               hover:bg-blue-700
                               transition">
                        Terapkan Filter
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const academicYearSelect = document.getElementById('academic_year_id');
        const classSelect = document.getElementById('school_class_id');
        const subjectSelect = document.getElementById('subject_id');

        if (!academicYearSelect || !classSelect || !subjectSelect) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Memuat Kelas & Mata Pelajaran
        |--------------------------------------------------------------------------
        */

        async function loadFilterOptions(academicYearId) {

            classSelect.disabled = true;
            subjectSelect.disabled = true;

            classSelect.innerHTML = `
                <option value="all">
                    Memuat kelas...
                </option>
            `;

            subjectSelect.innerHTML = `
                <option value="all">
                    Memuat mata pelajaran...
                </option>
            `;


            try {

                const url =
                    `{{ route('guru.attendance.filter-options') }}?academic_year_id=${encodeURIComponent(academicYearId)}`;

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil data filter.');
                }

                const data = await response.json();


                /*
                |--------------------------------------------------------------------------
                | Isi Kelas
                |--------------------------------------------------------------------------
                */

                classSelect.innerHTML = `
                    <option value="all">
                        Semua Kelas
                    </option>
                `;

                data.classes.forEach(function(item) {

                    const option = document.createElement('option');

                    option.value = item.id;
                    option.textContent = item.name;

                    classSelect.appendChild(option);

                });


                /*
                |--------------------------------------------------------------------------
                | Isi Mata Pelajaran
                |--------------------------------------------------------------------------
                */

                subjectSelect.innerHTML = `
                    <option value="all">
                        Semua Mata Pelajaran
                    </option>
                `;

                data.subjects.forEach(function(item) {

                    const option = document.createElement('option');

                    option.value = item.id;
                    option.textContent = item.name;

                    subjectSelect.appendChild(option);

                });

            } catch (error) {

                console.error(error);

                classSelect.innerHTML = `
                    <option value="all">
                        Gagal memuat kelas
                    </option>
                `;

                subjectSelect.innerHTML = `
                    <option value="all">
                        Gagal memuat mata pelajaran
                    </option>
                `;

            } finally {

                classSelect.disabled = false;
                subjectSelect.disabled = false;

            }
        }


        /*
        |--------------------------------------------------------------------------
        | Tahun Akademik berubah
        |--------------------------------------------------------------------------
        */

        academicYearSelect.addEventListener('change', function() {

            loadFilterOptions(this.value);

        });

    });
</script>
