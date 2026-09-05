<div class="modal fade"
    id="studentAttendanceDetailModal"
    tabindex="-1"
    aria-labelledby="studentAttendanceDetailModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-md modal-dialog-centered">

        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">

                <h5 class="modal-title"
                    id="studentAttendanceDetailModalLabel">

                    <i class="bi bi-calendar-check me-1"></i>
                    Detail Absensi Siswa

                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>

            {{-- Body --}}
            <div class="modal-body">

                <div class="row">

                    {{-- Siswa --}}
                    <div class="col-12 mb-3">

                        <div class="text-muted small">
                            Siswa
                        </div>

                        <div class="font-weight-bold"
                            id="detailStudentName">
                            -
                        </div>

                    </div>

                    {{-- Tanggal --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small">
                            Tanggal
                        </div>

                        <div class="font-weight-bold"
                            id="detailDate">
                            -
                        </div>

                    </div>

                    {{-- Pertemuan --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small">
                            Pertemuan
                        </div>

                        <div class="font-weight-bold"
                            id="detailMeetingNumber">
                            -
                        </div>

                    </div>

                    {{-- Mata Pelajaran --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small">
                            Mata Pelajaran
                        </div>

                        <div class="font-weight-bold"
                            id="detailSubject">
                            -
                        </div>

                    </div>

                    {{-- Guru --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small">
                            Guru
                        </div>

                        <div class="font-weight-bold"
                            id="detailTeacher">
                            -
                        </div>

                    </div>

                    {{-- Unit --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small">
                            Unit
                        </div>

                        <div class="font-weight-bold"
                            id="detailOrganization">
                            -
                        </div>

                    </div>

                    {{-- Kelas --}}
                    <div class="col-md-6 mb-3">

                        <div class="text-muted small">
                            Kelas
                        </div>

                        <div class="font-weight-bold"
                            id="detailSchoolClass">
                            -
                        </div>

                    </div>

                    {{-- Status --}}
                    <div class="col-12 mb-3">

                        <div class="text-muted small mb-1">
                            Status
                        </div>

                        <span
                            id="detailStatus"
                            class="badge bg-secondary">
                            -
                        </span>

                    </div>

                    {{-- Catatan --}}
                    <div class="col-12">

                        <div class="text-muted small">
                            Catatan
                        </div>

                        <div id="detailNotes"
                            class="mt-1">
                            -
                        </div>

                    </div>

                </div>

            </div>

            {{-- Footer --}}
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
