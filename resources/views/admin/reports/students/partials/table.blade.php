<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-table me-1"></i>
            Detail Siswa
        </h3>
    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-striped mb-0">

                <thead>
                    <tr>

                        <th width="60">
                            No
                        </th>

                        <th>
                            NIS
                        </th>

                        <th>
                            Nama
                        </th>

                        <th class="text-center">
                            L/P
                        </th>

                        <th>
                            Unit
                        </th>

                        <th>
                            Kelas
                        </th>

                        <th class="text-center">
                            Status
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @forelse ($studentAcademicYears as $index => $studentAcademicYear)
                        @php
                            $student = $studentAcademicYear->student;
                        @endphp

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $student?->nis ?? '-' }}
                            </td>

                            <td>
                                <strong>
                                    {{ $student?->name ?? '-' }}
                                </strong>
                            </td>

                            <td class="text-center">
                                {{ $student?->gender ?? '-' }}
                            </td>

                            <td>
                                {{ $studentAcademicYear->organization?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $studentAcademicYear->schoolClass?->name ?? '-' }}
                            </td>

                            <td class="text-center">

                                @if ($student?->is_active)
                                    <span class="badge bg-success">
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        Tidak Aktif
                                    </span>
                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="text-center text-muted py-4">
                                Tidak ada data siswa sesuai filter.

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    @if ($studentAcademicYears->hasPages())
        <div class="d-flex justify-content-end align-items-center p-3 border-top">

            <div>
                {{ $studentAcademicYears->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    @endif

</div>
