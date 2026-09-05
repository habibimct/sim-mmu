<div
    x-data="teacherAttendanceFilter()"
    x-on:open-teacher-attendance-filter.window="openModal()"
    x-on:keydown.escape.window="closeModal()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[80] overflow-y-auto"
>
    <div class="flex min-h-screen items-center justify-center p-4">

        {{-- Overlay --}}
        <div
            class="fixed inset-0 bg-black/50"
            @click="closeModal()"
        ></div>

        {{-- Modal --}}
        <div
            class="relative z-10 w-full max-w-2xl rounded-2xl bg-white shadow-2xl"
            @click.stop
        >

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        Filter Absensi Guru
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Pilih kriteria absensi yang ingin ditampilkan.
                    </p>
                </div>

                <button
                    type="button"
                    @click="closeModal()"
                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                >
                    &times;
                </button>
            </div>

            {{-- Form --}}
            <form
                method="GET"
                action="{{ route('guru.teacher-attendance.index') }}"
            >
                <div class="space-y-5 px-6 py-6">

                    {{-- Tahun Ajaran --}}
                    <div>
                        <label
                            for="teacher_attendance_academic_year_id"
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                        >
                            Tahun Ajaran
                        </label>

                        <select
                            id="teacher_attendance_academic_year_id"
                            name="academic_year_id"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            @change="loadOptions()"
                        >
                            <option value="">
                                Semua Tahun Ajaran
                            </option>

                            @foreach ($academicYears as $academicYear)
                                <option
                                    value="{{ $academicYear->id }}"
                                    @selected(
                                        (string) $academicYearId ===
                                        (string) $academicYear->id
                                    )
                                >
                                    {{ $academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Unit --}}
                    <div>
                        <label
                            for="teacher_attendance_organization_id"
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                        >
                            Unit
                        </label>

                        <select
                            id="teacher_attendance_organization_id"
                            name="organization_id"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            @change="loadOptions()"
                        >
                            <option value="">
                                Semua Unit
                            </option>

                            @foreach ($organizationOptions as $id => $name)
                                <option
                                    value="{{ $id }}"
                                    @selected(
                                        (string) $organizationId ===
                                        (string) $id
                                    )
                                >
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Kelas --}}
                    <div>
                        <label
                            for="teacher_attendance_school_class_id"
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                        >
                            Kelas
                        </label>

                        <select
                            id="teacher_attendance_school_class_id"
                            name="school_class_id"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">
                                Semua Kelas
                            </option>

                            @foreach ($classOptions as $id => $name)
                                <option
                                    value="{{ $id }}"
                                    @selected(
                                        (string) $schoolClassId ===
                                        (string) $id
                                    )
                                >
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Mata Pelajaran --}}
                    <div>
                        <label
                            for="teacher_attendance_subject_id"
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                        >
                            Mata Pelajaran
                        </label>

                        <select
                            id="teacher_attendance_subject_id"
                            name="subject_id"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">
                                Semua Mata Pelajaran
                            </option>

                            @foreach ($subjectOptions as $id => $name)
                                <option
                                    value="{{ $id }}"
                                    @selected(
                                        (string) $subjectId ===
                                        (string) $id
                                    )
                                >
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Tanggal --}}
                    <div>
                        <label
                            for="teacher_attendance_date"
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                        >
                            Tanggal
                        </label>

                        <input
                            type="date"
                            id="teacher_attendance_date"
                            name="date"
                            value="{{ $date }}"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                </div>


                {{-- Footer --}}
                <div class="flex items-center justify-end gap-2 border-t border-gray-200 px-6 py-4">

                    <a
                        href="{{ route('guru.teacher-attendance.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Reset
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Terapkan Filter
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>


<script>
    function teacherAttendanceFilter() {
        return {
            open: false,

            openModal() {
                this.open = true;

                /*
                 * Saat modal dibuka, sinkronkan pilihan
                 * Kelas dan Mata Pelajaran dengan
                 * Tahun Ajaran + Unit yang sedang dipilih.
                 */
                this.loadOptions();
            },

            closeModal() {
                this.open = false;
            },

            async loadOptions() {

                const academicYearId =
                    document.getElementById(
                        'teacher_attendance_academic_year_id'
                    )?.value ?? '';

                const organizationId =
                    document.getElementById(
                        'teacher_attendance_organization_id'
                    )?.value ?? '';

                const classSelect =
                    document.getElementById(
                        'teacher_attendance_school_class_id'
                    );

                const subjectSelect =
                    document.getElementById(
                        'teacher_attendance_subject_id'
                    );

                try {

                    const params = new URLSearchParams();

                    /*
                     * Tahun Ajaran
                     */
                    if (academicYearId !== '') {
                        params.set(
                            'academic_year_id',
                            academicYearId
                        );
                    }

                    /*
                     * Unit
                     */
                    if (organizationId !== '') {
                        params.set(
                            'organization_id',
                            organizationId
                        );
                    }


                    const response = await fetch(
                        `{{ route('guru.teacher-attendance.filter-options') }}?${params.toString()}`,
                        {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );


                    if (!response.ok) {
                        throw new Error(
                            'Gagal mengambil pilihan filter.'
                        );
                    }


                    const data = await response.json();


                    /*
                     * =====================================================
                     * KELAS
                     * =====================================================
                     */

                    const currentClassId =
                        classSelect?.value ?? '';

                    if (classSelect) {

                        classSelect.innerHTML =
                            '<option value="">Semua Kelas</option>';

                        data.classes.forEach(function (item) {

                            const option =
                                document.createElement('option');

                            option.value = item.id;
                            option.textContent = item.name;

                            classSelect.appendChild(option);

                        });

                        /*
                         * Pertahankan pilihan Kelas jika
                         * masih tersedia.
                         */
                        const classExists =
                            Array.from(
                                classSelect.options
                            ).some(function (option) {
                                return String(option.value) ===
                                    String(currentClassId);
                            });

                        if (classExists) {
                            classSelect.value =
                                currentClassId;
                        } else {
                            classSelect.value = '';
                        }
                    }


                    /*
                     * =====================================================
                     * MATA PELAJARAN
                     * =====================================================
                     */

                    const currentSubjectId =
                        subjectSelect?.value ?? '';

                    if (subjectSelect) {

                        subjectSelect.innerHTML =
                            '<option value="">Semua Mata Pelajaran</option>';

                        data.subjects.forEach(function (item) {

                            const option =
                                document.createElement('option');

                            option.value = item.id;
                            option.textContent = item.name;

                            subjectSelect.appendChild(option);

                        });

                        /*
                         * Pertahankan pilihan Mata Pelajaran
                         * jika masih tersedia.
                         */
                        const subjectExists =
                            Array.from(
                                subjectSelect.options
                            ).some(function (option) {
                                return String(option.value) ===
                                    String(currentSubjectId);
                            });

                        if (subjectExists) {
                            subjectSelect.value =
                                currentSubjectId;
                        } else {
                            subjectSelect.value = '';
                        }
                    }

                } catch (error) {

                    console.error(
                        'Teacher attendance filter:',
                        error
                    );

                }
            }
        };
    }
</script>
