{{-- Modal Input Absensi --}}
<div x-data="attendanceModal()" x-on:open-attendance-modal.window="openModal($event.detail)"
    x-on:keydown.escape.window="closeModal()" x-show="open" x-cloak class="fixed inset-0 z-[70] overflow-y-auto">


    {{-- Overlay --}}
    <div class="fixed inset-0 bg-black/50" x-show="open" x-transition.opacity @click="closeModal()"></div>


    {{-- Container --}}
    <div class="relative min-h-screen flex items-center justify-center p-4">

        <div class="relative w-full max-w-3xl
                   bg-white
                   rounded-2xl
                   shadow-2xl
                   overflow-hidden"
            x-show="open" x-transition @click.stop>

            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-100">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <h2 class="text-lg font-bold text-gray-800">
                            Input Absensi
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Catat kehadiran siswa.
                        </p>

                    </div>

                    <button type="button" @click="closeModal()"
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


            {{-- Informasi Penugasan --}}
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <div>
                        <p class="text-xs text-gray-400">
                            Kelas
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-800" x-text="assignmentName"></p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">
                            Mata Pelajaran
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-800" x-text="subjectName"></p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">
                            Tahun Akademik
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-800" x-text="academicYearName"></p>
                    </div>

                </div>

            </div>


            {{-- Form --}}
            <div class="p-6">

                {{-- Tanggal & Pertemuan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

                    <div>
                        <label for="attendance_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal
                        </label>

                        <input type="date" id="attendance_date" name="date" value="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-lg border-gray-300
                                   text-sm
                                   focus:border-blue-500
                                   focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="meeting_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            Pertemuan Ke-
                        </label>

                        <input type="number" id="meeting_number" name="meeting_number" min="1"
                            class="w-full rounded-lg border-gray-300
                                   text-sm text-center
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                            placeholder="Contoh: 1">
                    </div>

                </div>


                {{-- Loading --}}
                <div x-show="loading" class="py-10 text-center">
                    <p class="text-sm text-gray-500">
                        Memuat daftar siswa...
                    </p>
                </div>


                {{-- Error --}}
                <div x-show="error" x-text="error"
                    class="mb-4 rounded-lg bg-red-50 border border-red-100
                           px-4 py-3 text-sm text-red-700">
                </div>


                {{-- Daftar Siswa --}}
                <div x-show="!loading && !error" class="border border-gray-200 rounded-xl overflow-hidden">

                    <div
                        class="px-4 py-3
                               bg-gray-50
                               border-b border-gray-200
                               flex items-center justify-between">

                        <h3 class="text-sm font-bold text-gray-700">
                            Daftar Siswa
                        </h3>

                        <span class="text-xs text-gray-500" x-text="students.length + ' siswa'"></span>

                    </div>


                    <div class="max-h-80 overflow-y-auto">

                        <template x-for="(student, index) in students" :key="student.id">

                            <div
                                class="px-4 py-3
                                    border-b border-gray-100
                                    last:border-b-0
                                    hover:bg-gray-50">

                                <div
                                    class="flex items-center
                                        justify-between
                                        gap-3">

                                    <div class="min-w-0 flex-1">

                                        <p class="text-sm font-semibold text-gray-800 truncate" x-text="student.name">
                                        </p>

                                        <p class="mt-0.5 text-xs text-gray-500" x-text="'NIS: ' + student.nis"></p>

                                    </div>

                                    <div class="flex-shrink-0">

                                        <select x-model="student.status"
                                            class="rounded-lg border-gray-300
                                                text-sm
                                                focus:border-blue-500
                                                focus:ring-blue-500">
                                            <option value="present">
                                                Hadir
                                            </option>

                                            <option value="permission">
                                                Izin
                                            </option>

                                            <option value="sick">
                                                Sakit
                                            </option>

                                            <option value="absent">
                                                Alpa
                                            </option>
                                        </select>

                                    </div>

                                </div>


                                {{-- Catatan Siswa --}}
                                <div class="mt-3">

                                    <input type="text" x-model="student.notes" maxlength="500"
                                        placeholder="Catatan siswa (opsional)"
                                        class="w-full
                                            rounded-lg
                                            border border-gray-300
                                            bg-white
                                            px-3 py-2
                                            text-sm
                                            text-gray-700
                                            placeholder-gray-400
                                            focus:border-blue-500
                                            focus:outline-none
                                            focus:ring-2 focus:ring-blue-500/20">

                                </div>

                            </div>

                    </div>

                    </template>

                </div>

            </div>


            {{-- Catatan --}}
            <div class="mt-5">

                <label for="attendance_notes" class="block text-sm font-semibold text-gray-700 mb-2">
                    Catatan
                    <span class="font-normal text-gray-400">
                        (opsional)
                    </span>
                </label>

                <textarea id="attendance_notes" name="notes" rows="3"
                    class="w-full rounded-lg border-gray-300
                               text-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                    placeholder="Catatan absensi..."></textarea>

            </div>

        </div>

        {{-- Pesan sukses --}}
        <div x-show="successMessage" x-transition
            class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <span x-text="successMessage"></span>
        </div>

        {{-- Pesan offline --}}

        <div x-show="offlineMessage" x-transition
            class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
            <span x-text="offlineMessage"></span>
        </div>

        {{-- Pesan gagal --}}
        <div x-show="saveError" x-transition
            class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <span x-text="saveError"></span>
        </div>


        <div x-show="successMessage" x-transition
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 px-4">
            <div @click.stop class="w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h3 class="mt-4 text-lg font-bold text-gray-800">
                    Berhasil
                </h3>

                <p class="mt-2 text-sm text-gray-600" x-text="successMessage"></p>

            </div>
        </div>


        {{-- Footer --}}
        <div
            class="px-6 py-4
                       bg-gray-50
                       border-t border-gray-100
                       flex items-center justify-end gap-3">

            <button type="button" @click="closeModal()"
                class="px-4 py-2.5
                           rounded-lg
                           text-sm font-semibold
                           text-gray-600
                           hover:bg-gray-200
                           transition">
                Batal
            </button>

            <button type="button" @click="saveAttendance()" :disabled="saving || loading || students.length === 0"
                class="px-4 py-2.5
                            rounded-lg
                            bg-blue-600
                            text-white
                            text-sm font-semibold
                            hover:bg-blue-700
                            disabled:opacity-50
                            disabled:cursor-not-allowed
                            transition">
                <span x-show="!saving">
                    Simpan Absensi
                </span>

                <span x-show="saving">
                    Menyimpan...
                </span>
            </button>

        </div>

    </div>

</div>

</div>


<script>
    function attendanceModal() {
        return {
            open: false,
            loading: false,
            saving: false,

            saveError: '',
            successMessage: '',
            offlineMessage: '',

            assignmentId: null,
            assignmentName: '',
            subjectName: '',
            academicYearName: '',

            students: [],
            error: '',

            async openModal(assignment) {
                this.open = true;
                this.loading = true;
                this.error = '';
                this.saveError = '';
                this.successMessage = '';
                this.offlineMessage = '';

                this.assignmentId = assignment.id;
                this.assignmentName = assignment.class_name;
                this.subjectName = assignment.subject_name;
                this.academicYearName = assignment.academic_year_name;
                this.students = [];

                try {
                    const response = await fetch(
                        `{{ url('/guru/attendance') }}/${assignment.id}/students`, {
                            headers: {
                                'Accept': 'application/json',
                            },
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Gagal mengambil daftar siswa.'
                        );
                    }

                    const data = await response.json();

                    const students = data.students.map(student => ({
                        id: student.id,
                        nis: student.nis,
                        name: student.name,
                        status: 'present',
                        notes: '',
                    }));

                    this.students = students;

                    /*
                    |--------------------------------------------------------------------------
                    | Simpan daftar siswa ke cache offline
                    |--------------------------------------------------------------------------
                    */

                    if (
                        window.SimMmuAttendanceOffline &&
                        typeof window.SimMmuAttendanceOffline.cacheAssignment === 'function'
                    ) {
                        await window.SimMmuAttendanceOffline.cacheAssignment({
                            assignment_id: Number(assignment.id),
                            class_name: assignment.class_name,
                            subject_name: assignment.subject_name,
                            academic_year_name: assignment.academic_year_name,
                            students: students.map(student => ({
                                id: student.id,
                                nis: student.nis,
                                name: student.name,
                            })),
                        });

                        console.log(
                            '[SIM-MMU Attendance Offline] Cache siswa berhasil disimpan:',
                            assignment.id
                        );
                    }
                } catch (error) {
                    console.warn(
                        '[SIM-MMU Attendance Offline] Server tidak dapat diakses. Mencoba cache...',
                        error
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | FALLBACK KE CACHE OFFLINE
                    |--------------------------------------------------------------------------
                    */

                    try {
                        if (
                            window.SimMmuAttendanceOffline &&
                            typeof window.SimMmuAttendanceOffline.getCachedAssignment === 'function'
                        ) {
                            const cached =
                                await window.SimMmuAttendanceOffline.getCachedAssignment(
                                    assignment.id
                                );

                            if (
                                cached &&
                                Array.isArray(cached.students) &&
                                cached.students.length > 0
                            ) {
                                this.assignmentName =
                                    cached.class_name || this.assignmentName;

                                this.subjectName =
                                    cached.subject_name || this.subjectName;

                                this.academicYearName =
                                    cached.academic_year_name ||
                                    this.academicYearName;

                                this.students = cached.students.map(student => ({
                                    id: student.id,
                                    nis: student.nis,
                                    name: student.name,
                                    status: 'present',
                                    notes: '',
                                }));

                                this.offlineMessage =
                                    'Mode offline aktif. Daftar siswa dimuat dari cache.';

                                console.log(
                                    '[SIM-MMU Attendance Offline] Siswa berhasil dimuat dari cache:',
                                    assignment.id
                                );

                                return;
                            }
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Cache tidak tersedia
                        |--------------------------------------------------------------------------
                        */

                        this.error =
                            'Server tidak dapat diakses dan data siswa belum tersedia di cache offline.';
                    } catch (cacheError) {
                        console.error(
                            '[SIM-MMU Attendance Offline] Gagal membaca cache:',
                            cacheError
                        );

                        this.error =
                            'Server tidak dapat diakses dan data offline gagal dimuat.';
                    }
                } finally {
                    this.loading = false;
                }
            },

            closeModal() {
                this.open = false;
                this.students = [];
                this.error = '';
                this.saveError = '';
                this.offlineMessage = '';
            },

            async saveAttendance() {

                // Cegah double click
                if (this.saving) {
                    return;
                }

                this.saving = true;
                this.saveError = '';
                this.successMessage = '';

                const attendanceData = {
                    teaching_assignment_id: Number(this.assignmentId),

                    date: document
                        .getElementById('attendance_date')
                        .value,

                    meeting_number: Number(
                        document.getElementById('meeting_number').value
                    ),

                    notes: document
                        .getElementById('attendance_notes')
                        .value,

                    students: this.students.map(student => ({
                        student_academic_year_id: student.id,
                        status: student.status,
                        notes: student.notes || null,
                    })),
                };

                try {

                    /*
                    |--------------------------------------------------------------------------
                    | Coba simpan ke server terlebih dahulu
                    |--------------------------------------------------------------------------
                    */

                    const response = await fetch(
                        '{{ route('guru.attendance.store') }}', {
                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',

                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                            },

                            body: JSON.stringify(attendanceData),
                        }
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Berhasil online
                    |--------------------------------------------------------------------------
                    */

                    if (response.ok && response.redirected) {

                        this.successMessage =
                            'Absensi siswa berhasil disimpan.';

                        setTimeout(() => {

                            const filterQuery =
                                window.location.search;

                            window.location.href =
                                '{{ route('guru.attendance.index') }}' +
                                filterQuery;

                        }, 1000);

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Server merespons error
                    |
                    | Untuk tahap ini, response HTTP error TIDAK langsung
                    | dimasukkan ke queue. Kita hanya queue ketika komunikasi
                    | dengan server benar-benar gagal.
                    |--------------------------------------------------------------------------
                    */

                    let data = {};

                    try {
                        data = await response.json();
                    } catch (error) {
                        data = {};
                    }

                    this.saveError =
                        data.message ??
                        'Absensi gagal disimpan. Silakan periksa kembali data.';

                } catch (error) {

                    /*
                    |--------------------------------------------------------------------------
                    | SERVER / INTERNET TIDAK TERSEDIA
                    |--------------------------------------------------------------------------
                    */

                    console.warn(
                        '[SIM-MMU Attendance Offline] Server tidak dapat diakses. Menyimpan ke queue offline...',
                        error
                    );

                    try {

                        if (
                            !window.SimMmuAttendanceOffline ||
                            typeof window.SimMmuAttendanceOffline.addToQueue !== 'function'
                        ) {
                            throw new Error(
                                'Modul attendance offline belum tersedia.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Buat sync_id unik untuk transaksi offline
                        |--------------------------------------------------------------------------
                        */

                        const syncId =
                            crypto.randomUUID();

                        const queueData = {
                            sync_id: syncId,

                            operation: 'create',

                            status: 'pending_sync',

                            created_at: new Date().toISOString(),

                            attendance_id: null,

                            ...attendanceData,
                        };

                        /*
                        |--------------------------------------------------------------------------
                        | Simpan ke IndexedDB
                        |--------------------------------------------------------------------------
                        */

                        await window.SimMmuAttendanceOffline.addToQueue(
                            queueData
                        );

                        console.log(
                            '[SIM-MMU Attendance Offline] Absensi berhasil masuk queue:',
                            queueData
                        );

                        this.offlineMessage =
                            'Server tidak tersedia. Absensi berhasil disimpan di perangkat dan akan disinkronkan saat koneksi kembali.';

                        setTimeout(() => {
                            this.closeModal();
                        }, 1500);

                    } catch (offlineError) {

                        console.error(
                            '[SIM-MMU Attendance Offline] Gagal menyimpan queue:',
                            offlineError
                        );

                        this.saveError =
                            'Server tidak tersedia dan absensi gagal disimpan secara offline.';
                    }

                } finally {

                    this.saving = false;
                }
            },
        };
    }
</script>
