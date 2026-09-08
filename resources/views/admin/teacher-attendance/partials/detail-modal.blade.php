<div class="modal fade"
    id="teacherAttendanceDetailModal"
    tabindex="-1"
    aria-labelledby="teacherAttendanceDetailModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">

                <div>

                    <h5 class="modal-title"
                        id="teacherAttendanceDetailModalLabel">

                        <i class="bi bi-calendar-check me-1"></i>
                        Detail Absensi Pertemuan

                    </h5>

                    <div class="small text-muted mt-1"
                        id="teacherAttendanceDetailSubtitle">

                        -

                    </div>

                </div>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>


            {{-- Body --}}
            <div class="modal-body">

                {{-- Informasi Pertemuan --}}
                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <div class="text-muted small">
                            Guru
                        </div>

                        <div class="fw-bold"
                            id="detailTeacherName">
                            -
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="text-muted small">
                            Unit
                        </div>

                        <div class="fw-bold"
                            id="detailOrganizationName">
                            -
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="text-muted small">
                            Kelas
                        </div>

                        <div class="fw-bold"
                            id="detailClassName">
                            -
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="text-muted small">
                            Mata Pelajaran
                        </div>

                        <div class="fw-bold"
                            id="detailSubjectName">
                            -
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="text-muted small">
                            Pertemuan
                        </div>

                        <div class="fw-bold"
                            id="detailMeetingNumber">
                            -
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="text-muted small">
                            Tanggal
                        </div>

                        <div class="fw-bold"
                            id="detailAttendanceDate">
                            -
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="text-muted small">
                            Jam Isi
                        </div>

                        <div class="fw-bold"
                            id="detailAttendanceTime">
                            -
                        </div>
                    </div>

                </div>


                {{-- Ringkasan --}}
                <div class="row g-2 mb-4">

                    <div class="col-6 col-md-3">

                        <div class="border rounded p-3 text-center">

                            <div class="text-muted small">
                                Hadir
                            </div>

                            <div class="fs-4 fw-bold text-success"
                                id="detailCountPresent">
                                0
                            </div>

                        </div>

                    </div>


                    <div class="col-6 col-md-3">

                        <div class="border rounded p-3 text-center">

                            <div class="text-muted small">
                                Sakit
                            </div>

                            <div class="fs-4 fw-bold text-info"
                                id="detailCountSick">
                                0
                            </div>

                        </div>

                    </div>


                    <div class="col-6 col-md-3">

                        <div class="border rounded p-3 text-center">

                            <div class="text-muted small">
                                Izin
                            </div>

                            <div class="fs-4 fw-bold text-warning"
                                id="detailCountPermission">
                                0
                            </div>

                        </div>

                    </div>


                    <div class="col-6 col-md-3">

                        <div class="border rounded p-3 text-center">

                            <div class="text-muted small">
                                Alpa
                            </div>

                            <div class="fs-4 fw-bold text-danger"
                                id="detailCountAbsent">
                                0
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Daftar Siswa --}}
                <div class="card border">

                    <div class="card-header">

                        <strong>
                            <i class="bi bi-people me-1"></i>
                            Kehadiran Siswa
                        </strong>

                    </div>


                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th class="text-center"
                                            style="width: 60px;">
                                            No
                                        </th>

                                        <th style="width: 140px;">
                                            NIS
                                        </th>

                                        <th>
                                            Nama Siswa
                                        </th>

                                        <th class="text-center"
                                            style="width: 120px;">
                                            Status
                                        </th>

                                        <th>
                                            Catatan
                                        </th>

                                    </tr>

                                </thead>


                                <tbody id="teacherAttendanceStudentRows">

                                    <tr>

                                        <td colspan="5"
                                            class="text-center text-muted py-4">

                                            Belum ada data.

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

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
