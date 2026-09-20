<div x-data="attendanceEditModal()" x-on:open-attendance-edit.window="openModal($event.detail.id)"
    x-on:keydown.escape.window="closeModal()" x-show="open" x-cloak class="fixed inset-0 z-[90] overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">

        {{-- Overlay --}}
        <div class="fixed inset-0 bg-black/50" @click="closeModal()"></div>

        {{-- Modal --}}
        <div class="relative z-10 w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl" @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">

                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        Edit Absensi Siswa
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Perubahan hanya dapat dilakukan dalam 6 jam setelah absensi disimpan.
                    </p>
                </div>

                <button type="button" @click="closeModal()" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100">
                    &times;
                </button>

            </div>

            {{-- Loading --}}
            <div x-show="loading" class="px-6 py-10 text-center text-sm text-gray-500">
                Memuat data absensi...
            </div>

            {{-- Form --}}
            <form x-show="!loading" @submit.prevent="save()">

                <div class="space-y-5 px-6 py-6">

                    {{-- Identitas --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                        <div>
                            <p class="text-xs text-gray-500">
                                Unit
                            </p>

                            <p class="mt-1 font-medium text-gray-800" x-text="attendance.organization_name"></p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">
                                Kelas
                            </p>

                            <p class="mt-1 font-medium text-gray-800" x-text="attendance.class_name"></p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">
                                Mata Pelajaran
                            </p>

                            <p class="mt-1 font-medium text-gray-800" x-text="attendance.subject_name"></p>
                        </div>

                    </div>

                    {{-- Tanggal + Pertemuan --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                Tanggal
                            </label>

                            <input type="date" x-model="attendance.date"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                Pertemuan
                            </label>

                            <input type="number" min="1" x-model="attendance.meeting_number"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        </div>

                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                            Catatan Pertemuan
                        </label>

                        <textarea x-model="attendance.notes" rows="2"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"></textarea>
                    </div>

                    {{-- Siswa --}}
                    <div>

                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-800">
                                Daftar Kehadiran Siswa
                            </h3>

                            <span class="text-xs text-gray-500" x-text="attendance.students.length + ' siswa'"></span>
                        </div>

                        <div class="overflow-hidden rounded-xl border border-gray-200">

                            <div class="max-h-[50vh] overflow-y-auto">

                                <table class="min-w-full text-sm">

                                    <thead class="sticky top-0 bg-gray-50">
                                        <tr class="border-b border-gray-200">

                                            <th class="px-4 py-3 text-left">
                                                No
                                            </th>

                                            <th class="px-4 py-3 text-left">
                                                NIS
                                            </th>

                                            <th class="px-4 py-3 text-left">
                                                Nama
                                            </th>

                                            <th class="px-4 py-3 text-left">
                                                Status
                                            </th>

                                            <th class="px-4 py-3 text-left">
                                                Catatan
                                            </th>

                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-gray-100">

                                        <template x-for="(student, index) in attendance.students"
                                            :key="student.id">

                                            <tr>

                                                <td class="px-4 py-3 text-gray-500" x-text="index + 1"></td>

                                                <td class="px-4 py-3" x-text="student.nis"></td>

                                                <td class="px-4 py-3 font-medium" x-text="student.name"></td>

                                                <td class="px-4 py-3">

                                                    <select x-model="student.status"
                                                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                                        <option value="present">
                                                            Hadir
                                                        </option>

                                                        <option value="sick">
                                                            Sakit
                                                        </option>

                                                        <option value="permission">
                                                            Izin
                                                        </option>

                                                        <option value="absent">
                                                            Alfa
                                                        </option>
                                                    </select>

                                                </td>

                                                <td class="px-4 py-3">

                                                    <input type="text" x-model="student.notes"
                                                        class="w-full min-w-[160px] rounded-lg border border-gray-300 px-3 py-2 text-sm">

                                                </td>

                                            </tr>

                                        </template>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                    {{-- Error --}}
                    <div x-show="error" x-text="error"
                        class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-2 border-t border-gray-200 px-6 py-4">

                    <button type="button" @click="closeModal()"
                        class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>

                    <button type="submit" :disabled="saving"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
                        <span x-show="!saving">
                            Simpan Perubahan
                        </span>

                        <span x-show="saving">
                            Menyimpan...
                        </span>
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>

<script>
    function attendanceEditModal() {

        return {

            open: false,
            loading: false,
            saving: false,
            error: '',

            attendanceId: null,

            attendance: {
                organization_name: '',
                class_name: '',
                subject_name: '',
                date: '',
                meeting_number: '',
                notes: '',
                students: [],
            },

            async openModal(id) {

                this.open = true;
                this.loading = true;
                this.error = '';
                this.attendanceId = id;

                try {

                    const response = await fetch(
                        `{{ url('/guru/attendance') }}/${id}/edit`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message ??
                            'Data absensi gagal dimuat.'
                        );
                    }

                    this.attendance = data.attendance;

                } catch (error) {

                    console.error(error);

                    this.error =
                        error.message ??
                        'Data absensi gagal dimuat.';

                } finally {

                    this.loading = false;

                }
            },

            closeModal() {

                if (this.saving) {
                    return;
                }

                this.open = false;
            },

            async save() {

                if (this.saving) {
                    return;
                }

                this.saving = true;
                this.error = '';

                const payload = {
                    date: this.attendance.date,

                    meeting_number: this.attendance.meeting_number,

                    notes: this.attendance.notes,

                    students: this.attendance.students.map(student => ({
                        student_academic_year_id: student.id,

                        status: student.status,

                        notes: student.notes || null
                    }))
                };

                try {

                    const response = await fetch(
                        `{{ url('/guru/attendance') }}/${this.attendanceId}`, {
                            method: 'PUT',

                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .getAttribute('content')
                            },

                            body: JSON.stringify(payload)
                        }
                    );

                    const data = await response.json();

                    if (!response.ok) {

                        throw new Error(
                            data.message ??
                            'Absensi gagal diperbarui.'
                        );
                    }

                    window.location.reload();

                } catch (error) {

                    console.error(error);

                    /*
                    |--------------------------------------------------------------------------
                    | Offline fallback
                    |--------------------------------------------------------------------------
                    */

                    if (
                        error instanceof TypeError ||
                        !navigator.onLine
                    ) {

                        try {

                            const syncId = crypto.randomUUID();

                            await SimMmuAttendanceOffline.addToQueue({
                                sync_id: syncId,

                                operation: 'update',

                                status: 'pending_sync',

                                created_at: new Date().toISOString(),

                                attendance_id: this.attendanceId,

                                ...payload
                            });

                            this.error =
                                'Koneksi tidak tersedia. Perubahan absensi berhasil disimpan di perangkat dan akan disinkronkan saat koneksi kembali.';

                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);

                            return;

                        } catch (queueError) {

                            console.error(queueError);

                            this.error =
                                'Perubahan gagal disimpan secara offline.';
                        }

                    } else {

                        this.error =
                            error.message ??
                            'Terjadi kesalahan saat menyimpan perubahan.';
                    }

                } finally {

                    this.saving = false;

                }
            }
        };
    }
</script>
