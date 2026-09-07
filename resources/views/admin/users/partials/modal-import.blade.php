<div
    class="modal fade"
    id="modalImportUser"
    tabindex="-1"
    aria-labelledby="modalImportUserLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.users.import') }}"
                enctype="multipart/form-data">

                @csrf

                <input
                    type="hidden"
                    name="_form"
                    value="import">


                {{-- HEADER --}}
                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="modalImportUserLabel">

                        <i class="bi bi-file-earmark-excel me-2"></i>
                        Import User

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                {{-- BODY --}}
                <div class="modal-body">

                    {{-- Informasi --}}
                    <div class="alert alert-info">

                        <i class="bi bi-info-circle me-1"></i>

                        Gunakan template Excel User yang telah
                        disediakan.

                    </div>


                    {{-- Aturan --}}
                    <div class="mb-3">

                        <strong>Format kolom:</strong>

                        <div class="mt-2">

                            <code>
                                Nama | Email | Password | Role |
                                Organisasi | Status
                            </code>

                        </div>

                    </div>


                    {{-- Pemisah organisasi --}}
                    <div class="mb-3">

                        <small class="text-muted">

                            Jika satu User memiliki beberapa organisasi,
                            pisahkan dengan koma.

                            Contoh:

                            <code>UNIT01, UNIT02</code>

                        </small>

                    </div>


                    {{-- File --}}
                    <div class="mb-3">

                        <label class="form-label">
                            File Excel
                        </label>

                        <input
                            type="file"
                            name="file"
                            class="form-control"
                            accept=".xlsx,.xls,.csv"
                            required>

                    </div>


                    {{-- Error validasi import --}}
                    @if (session('import_errors'))

                        <div class="alert alert-danger">

                            <div class="fw-bold mb-2">

                                <i class="bi bi-exclamation-triangle me-1"></i>

                                Import gagal

                            </div>

                            <div class="small mb-2">

                                Tidak ada User yang disimpan.
                                Perbaiki semua kesalahan berikut,
                                kemudian import kembali.

                            </div>

                            <ul class="mb-0">

                                @foreach (session('import_errors') as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif


                    {{-- Aturan penting --}}
                    <div class="alert alert-warning mb-0">

                        <strong>
                            Perhatian:
                        </strong>

                        Jika terdapat satu saja kesalahan pada
                        seluruh file, maka seluruh proses import
                        dibatalkan dan tidak ada data yang disimpan.

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="modal-footer">

                    <a
                        href="{{ route('admin.users.template') }}"
                        class="btn btn-success">

                        <i class="bi bi-download me-1"></i>
                        Download Template

                    </a>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-upload me-1"></i>
                        Import User

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- =================================================
    BUKA OTOMATIS MODAL JIKA IMPORT GAGAL
================================================= --}}

@if (session('open_import_modal'))

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const modalElement =
                document.getElementById('modalImportUser');

            if (modalElement) {

                const modal =
                    new bootstrap.Modal(modalElement);

                modal.show();
            }

        });
    </script>

@endif
