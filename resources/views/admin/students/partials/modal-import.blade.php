<div
    class="modal fade"
    id="importStudentModal"
    tabindex="-1"
    aria-labelledby="importStudentModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            {{-- =========================================================
            HEADER
            ========================================================== --}}

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="importStudentModalLabel"
                    >
                        <i class="bi bi-file-earmark-excel me-2"></i>
                        Import Peserta dari Excel
                    </h5>

                    <small class="text-muted">
                        Tambahkan data siswa secara massal melalui Excel.
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>

            </div>


            {{-- =========================================================
            FORM
            ========================================================== --}}

            <form
                action="{{ route('admin.students.import') }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf


                <div class="modal-body">

                    {{-- =================================================
                    SUCCESS
                    ================================================== --}}

                    @if (session('import_success'))

                        <div
                            class="alert alert-success alert-dismissible fade show"
                            role="alert"
                        >

                            <i class="bi bi-check-circle me-1"></i>

                            {{ session('import_success') }}

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                            ></button>

                        </div>

                    @endif


                    {{-- =================================================
                    ERROR UMUM
                    ================================================== --}}

                    @if (session('import_error'))

                        <div
                            class="alert alert-danger alert-dismissible fade show"
                            role="alert"
                        >

                            <i class="bi bi-exclamation-circle me-1"></i>

                            {{ session('import_error') }}

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                            ></button>

                        </div>

                    @endif


                    {{-- =================================================
                    VALIDATION ERROR
                    ================================================== --}}

                    @if ($errors->any())

                        <div class="alert alert-danger">

                            <div class="fw-bold mb-2">

                                <i class="bi bi-exclamation-triangle me-1"></i>

                                Data Excel tidak valid

                            </div>

                            <ul class="mb-0 ps-4">

                                @foreach ($errors->all() as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif


                    {{-- =================================================
                    ORGANISASI
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="import_organization_id"
                            class="form-label"
                        >
                            Organisasi
                        </label>

                        <select
                            name="organization_id"
                            id="import_organization_id"
                            class="form-select @error('organization_id') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                -- Pilih Organisasi --
                            </option>

                            @foreach ($organizations as $organization)

                                <option
                                    value="{{ $organization->id }}"
                                    @selected(
                                        old('organization_id')
                                        == $organization->id
                                    )
                                >
                                    {{ $organization->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('organization_id')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- =================================================
                    FILE EXCEL
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="import_file"
                            class="form-label"
                        >
                            File Excel
                        </label>

                        <input
                            type="file"
                            name="file"
                            id="import_file"
                            class="form-control @error('file') is-invalid @enderror"
                            accept=".xlsx,.xls"
                            required
                        >

                        @error('file')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- =================================================
                    PETUNJUK
                    ================================================== --}}

                    <div class="alert alert-info mb-0">

                        <div class="fw-bold mb-2">

                            <i class="bi bi-info-circle me-1"></i>

                            Petunjuk Import

                        </div>

                        <ul class="mb-0 ps-4">

                            <li>
                                Gunakan template Excel yang disediakan.
                            </li>

                            <li>
                                Heading harus sesuai dengan template.
                            </li>

                            <li>
                                Pastikan NIS dan data peserta sudah benar.
                            </li>

                            <li>
                                Organisasi pada Excel harus sesuai dengan organisasi
                                yang dipilih.
                            </li>

                            <li>
                                Tahun akademik dan kelas harus sudah tersedia
                                di sistem.
                            </li>

                            <li>
                                Jika terdapat data tidak valid,
                                seluruh proses akan dibatalkan.
                            </li>

                        </ul>

                    </div>

                </div>


                {{-- =====================================================
                FOOTER
                ====================================================== --}}

                <div class="modal-footer">

                    <a
                        href="{{ route('admin.students.import.template') }}"
                        class="btn btn-success"
                    >
                        <i class="bi bi-download me-1"></i>
                        Download Template
                    </a>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        <i class="bi bi-x-lg me-1"></i>
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-upload me-1"></i>
                        Import Peserta
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>


{{-- =============================================================
AUTO OPEN MODAL
============================================================== --}}

@push('js')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const modalElement =
            document.getElementById(
                'importStudentModal'
            );

        if (!modalElement) {
            return;
        }


        @if (
            session('import_success')
            ||
            session('import_error')
            ||
            $errors->has('organization_id')
            ||
            $errors->has('file')
        )

            const importModal =
                new bootstrap.Modal(
                    modalElement
                );

            importModal.show();

        @endif

    }
);

</script>

@endpush
