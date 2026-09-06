<div class="modal fade" id="classStudentsModal" tabindex="-1"
    aria-labelledby="classStudentsModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="classStudentsModalLabel">
                    <i class="bi bi-people me-2"></i>
                    Daftar Siswa
                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <div class="fw-semibold">
                        Kelas: {{ $schoolClass->name }}
                    </div>

                    <div class="text-muted">
                        Tahun Akademik:
                        {{ $schoolClass->academicYear?->name ?? '-' }}
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped mb-0">

                        <thead>
                            <tr>
                                <th width="60" class="text-center">No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">Jenis Kelamin</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($students as $item)
                                <tr>
                                    <td class="text-center">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        {{ $item->student?->nis ?? '-' }}
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $item->student?->name ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $item->student?->gender ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        @if ($item->student?->is_active)
                                            <span class="badge bg-success">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="text-center py-4 text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Belum ada siswa dalam kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
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
