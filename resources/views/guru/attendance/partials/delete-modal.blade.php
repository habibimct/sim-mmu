<div
    x-data="attendanceDeleteModal()"
    x-on:open-attendance-delete.window="openModal($event.detail.id)"
    x-on:keydown.escape.window="closeModal()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[100] overflow-y-auto"
>
    <div class="flex min-h-screen items-center justify-center p-4">

        {{-- Overlay --}}
        <div
            class="fixed inset-0 bg-black/50"
            @click="closeModal()"
        ></div>

        {{-- Modal --}}
        <div
            class="relative z-10 w-full max-w-md rounded-2xl bg-white shadow-2xl"
            @click.stop
        >

            <div class="p-6">

                <div class="flex items-start gap-4">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8"
                            />
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-gray-800">
                            Hapus Absensi
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            Apakah Anda yakin ingin menghapus absensi ini?
                        </p>

                        <p class="mt-2 text-xs text-red-600">
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>

                </div>

            </div>

            <form
                method="POST"
                :action="`{{ url('/guru/attendance') }}/${attendanceId}`"
            >

                @csrf
                @method('DELETE')

                <div class="flex justify-end gap-2 border-t border-gray-200 px-6 py-4">

                    <button
                        type="button"
                        @click="closeModal()"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700"
                    >
                        Ya, Hapus
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>

<script>
    function attendanceDeleteModal() {

        return {

            open: false,
            attendanceId: null,

            openModal(id) {

                this.attendanceId = id;
                this.open = true;

            },

            closeModal() {

                this.open = false;
                this.attendanceId = null;

            }

        };
    }
</script>
