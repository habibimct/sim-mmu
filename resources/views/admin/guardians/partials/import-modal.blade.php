
<div class="modal fade" id="guardianImportModal" tabindex="-1"
    aria-labelledby="guardianImportModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <form method="POST"
            action="{{ route('admin.guardians.import') }}"
            enctype="multipart/form-data"
            class="modal-content">

            @csrf

            <div class="modal-header">
                <h5 class="modal-title" id="guardianImportModalLabel">
                    <i class="bi bi-file-earmark-excel me-1"></i>
                    Import Wali / Orang Tua
                </h5>

                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div class="alert alert-info">
                    <div class="fw-bold mb-1">
                        <i class="bi bi-info-circle me-1"></i>
                        Petunjuk Import
                    </div>

                    <ol class="mb-0 ps-3">
                        <li>Unduh template Excel terlebih dahulu.</li>
                        <li>Isi nama unit, nama wali, NIK, dan nomor HP.</li>
                        <li>Pastikan NIK terdiri dari 16 digit dan belum terdaftar.</li>
                        <li>Unggah kembali file dalam format Excel.</li>
                    </ol>
                </div>

                <div class="mb-3">
                    <a href="{{ route('admin.guardians.import-template') }}"
                        class="btn btn-outline-success">
                        <i class="bi bi-download me-1"></i>
                        Download Template Excel
                    </a>
                </div>

                <div class="mb-3">
                    <label for="guardian_import_file" class="form-label">
                        File Excel <span class="text-danger">*</span>
                    </label>

                    <input type="file"
                        name="file"
                        id="guardian_import_file"
                        class="form-control"
                        accept=".xlsx,.xls,.csv"
                        required>

                    <small class="text-muted">
                        Gunakan template yang disediakan. Format yang diterima:
                        XLSX, XLS, atau CSV.
                    </small>
                </div>

                <div class="alert alert-warning mb-0">
                    <i class="bi bi-shield-exclamation me-1"></i>
                    Data yang gagal divalidasi tidak boleh dibuatkan akun.
                    Pastikan file sudah diperiksa sebelum diunggah.
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light"
                    data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-upload me-1"></i>
                    Upload Wali
                </button>
            </div>

        </form>

    </div>
</div>
