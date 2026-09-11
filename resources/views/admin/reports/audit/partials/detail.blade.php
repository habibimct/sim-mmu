<div class="modal fade"
     id="modalAuditDetail"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    <i class="bi bi-shield-check me-1"></i>
                    Detail Audit
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>


            <div class="modal-body">

                {{-- Loading --}}
                <div id="auditDetailLoading"
                     class="text-center py-4">

                    <div class="spinner-border text-primary"
                         role="status">
                    </div>

                    <div class="mt-2 text-muted">
                        Memuat data audit...
                    </div>

                </div>


                {{-- Content --}}
                <div id="auditDetailContent"
                     class="d-none">

                    {{-- Informasi Aktivitas --}}
                    <div class="card border-0 bg-light mb-3">

                        <div class="card-body">

                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Informasi Aktivitas
                            </h6>

                            <div class="row g-3">

                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Waktu
                                    </div>

                                    <div class="fw-semibold"
                                         id="detailAuditDate">
                                        -
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Pengguna
                                    </div>

                                    <div class="fw-semibold"
                                         id="detailAuditUser">
                                        -
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Aktivitas
                                    </div>

                                    <div id="detailAuditEvent">
                                        -
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Modul
                                    </div>

                                    <div class="fw-semibold"
                                         id="detailAuditModule">
                                        -
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        ID Data
                                    </div>

                                    <div class="fw-semibold"
                                         id="detailAuditSubjectId">
                                        -
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Organisasi
                                    </div>

                                    <div class="fw-semibold"
                                         id="detailAuditOrganization">
                                        -
                                    </div>

                                </div>


                                <div class="col-12">

                                    <div class="text-muted small">
                                        Deskripsi
                                    </div>

                                    <div id="detailAuditDescription">
                                        -
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Informasi Teknis --}}
                    <div class="card border-0 bg-light mb-3">

                        <div class="card-body">

                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-pc-display me-1"></i>
                                Informasi Teknis
                            </h6>

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <div class="text-muted small">
                                        IP Address
                                    </div>

                                    <div id="detailAuditIp">
                                        -
                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <div class="text-muted small">
                                        Browser / User Agent
                                    </div>

                                    <div id="detailAuditUserAgent"
                                         class="small text-break">
                                        -
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Perubahan --}}
                    <div id="detailAuditChangesWrapper"
                         class="card border-0 bg-light">

                        <div class="card-body">

                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-arrow-left-right me-1"></i>
                                Perubahan Data
                            </h6>

                            <div class="table-responsive">

                                <table class="table table-sm table-bordered align-middle mb-0">

                                    <thead class="table-secondary">

                                        <tr>
                                            <th width="25%">
                                                Field
                                            </th>

                                            <th width="35%">
                                                Nilai Lama
                                            </th>

                                            <th width="40%">
                                                Nilai Baru
                                            </th>
                                        </tr>

                                    </thead>

                                    <tbody id="detailAuditChanges">
                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Tutup

                </button>

            </div>

        </div>

    </div>

</div>
