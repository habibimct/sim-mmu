{{-- Modal Import Guru --}}
<div class="modal fade" id="modalImportGuru" tabindex="-1" aria-labelledby="modalImportGuruLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">

                <h5 class="modal-title" id="modalImportGuruLabel">
                    <i class="bi bi-upload me-1"></i>
                    Import Data Guru
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>

            </div>

            {{-- Body --}}
            <div class="modal-body">

                <div class="alert alert-info">

                    <i class="bi bi-info-circle me-1"></i>

                    Gunakan template yang telah disediakan
                    untuk melakukan import data Guru.

                </div>

                <form action="{{ route('admin.teachers.import.store') }}" method="POST"
                    enctype="multipart/form-data" id="formImportGuru">

                    @csrf

                    {{-- Unit tujuan untuk Admin Global --}}
                    @if ($isGlobalOrganizationManager)

                        <div class="mb-3">

                            <label for="organizationImportGuru" class="form-label">

                                Unit Tujuan
                                <span class="text-danger">*</span>

                            </label>

                            <select name="organization_id" id="organizationImportGuru" class="form-select" required>

                                <option value="">
                                    -- Pilih Unit Tujuan --
                                </option>

                                @foreach ($organizations as $organization)
                                    @if ($organization->parent_id)
                                        <option value="{{ $organization->id }}">
                                            {{ $organization->name }}
                                        </option>
                                    @endif
                                @endforeach

                            </select>

                            <small class="text-muted">
                                Guru yang diimport akan ditempatkan
                                pada Unit yang dipilih.
                            </small>

                        </div>
                    @else
                        {{-- Admin Unit --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Unit
                            </label>

                            <input type="text" class="form-control"
                                value="{{ $organizations->first()->name ?? '-' }}" readonly>

                            <small class="text-muted">
                                Unit ditentukan otomatis berdasarkan
                                kewenangan Anda.
                            </small>

                        </div>

                    @endif

                    {{-- File --}}
                    <div class="mb-3">

                        <label for="fileImportGuru" class="form-label">

                            File Excel
                            <span class="text-danger">*</span>

                        </label>

                        <input type="file" name="file" id="fileImportGuru" class="form-control"
                            accept=".xlsx,.xls" required>

                        <small class="text-muted">
                            Format .xlsx atau .xls, maksimal 10 MB.
                        </small>

                    </div>

                </form>

                <div class="alert alert-warning mb-0">

                    <i class="bi bi-shield-lock me-1"></i>

                    <strong>Catatan:</strong>

                    Template tidak memiliki kolom Unit.
                    Penempatan Guru ditentukan oleh sistem
                    berdasarkan kewenangan pengguna.

                </div>

            </div>

            {{-- Footer --}}
            <div class="modal-footer">

                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>

                <a href="{{ route('admin.teachers.import.template') }}" class="btn btn-outline-success">

                    <i class="bi bi-file-earmark-excel me-1"></i>
                    Template

                </a>

                <button type="submit" form="formImportGuru" class="btn btn-outline-success">

                    <i class="bi bi-upload me-1"></i>
                    Import

                </button>

            </div>

        </div>

    </div>

</div>
