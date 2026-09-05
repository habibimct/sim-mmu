<div class="card mb-3">

    <div class="card-header">

        <h3 class="card-title mb-0">

            <i class="bi bi-bar-chart me-2"></i>

            Rekap Siswa Per Kelas

        </h3>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-striped mb-0">

                <thead>

                    <tr>

                        <th
                            width="60"
                            class="text-center"
                        >
                            No
                        </th>

                        <th>
                            Kelas
                        </th>

                        <th>
                            Tahun Akademik
                        </th>

                        <th class="text-center">
                            Jumlah Siswa
                        </th>

                        <th class="text-center">
                            Aktif
                        </th>

                        <th class="text-center">
                            Nonaktif
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse (
                        $schoolClassSummary
                        as $summaryItem
                    )

                        <tr>

                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>

                            <td class="fw-semibold">
                                {{ $summaryItem->name }}
                            </td>

                            <td>
                                {{ $summaryItem->academicYear?->name ?? '-' }}
                            </td>

                            <td class="text-center fw-bold">
                                {{ number_format(
                                    $summaryItem->total_students,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td class="text-center">

                                <span class="badge bg-success">

                                    {{ number_format(
                                        $summaryItem->active_students,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </span>

                            </td>

                            <td class="text-center">

                                <span class="badge bg-secondary">

                                    {{ number_format(
                                        $summaryItem->inactive_students,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-4 text-muted"
                            >

                                <i class="bi bi-info-circle me-1"></i>

                                Tidak ada data kelas
                                yang sesuai dengan filter.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
