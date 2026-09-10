@extends('adminlte::page')

@section('title', 'Laporan Guru')

@section('content_header')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

        <div>

            <h1 class="m-0">
                Laporan Guru
            </h1>

            <div class="text-muted small mt-1">
                Rekapitulasi data guru berdasarkan unit,
                status, dan jenis kelamin.
            </div>

        </div>


        <div class="d-flex gap-2">

            <a href="{{ route('admin.reports.teachers.excel', request()->query()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>
                Excel
            </a>


            <a href="{{ route('admin.reports.teachers.pdf', request()->query()) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>
                PDF
            </a>

        </div>

    </div>

@stop


@section('content')

    {{-- FILTER --}}

    @include('admin.reports.teachers.partials.filter')


    {{-- SUMMARY --}}

    @include('admin.reports.teachers.partials.summary')


    {{-- DETAIL GURU --}}

    <div class="card shadow-sm mt-3">

        <div class="card-header bg-white">

            <h3 class="card-title fw-semibold mb-0">

                <i class="bi bi-people me-1"></i>

                Detail Guru

            </h3>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th width="60" class="text-center">
                                No
                            </th>

                            <th>
                                NIK
                            </th>

                            <th>
                                Nama
                            </th>

                            <th width="100" class="text-center">
                                L/P
                            </th>

                            <th>
                                Unit
                            </th>

                            <th width="120" class="text-center">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($teachers as $index => $teacher)

                            <tr>

                                <td class="text-center text-muted">

                                    {{ $teachers->firstItem() + $index }}

                                </td>


                                <td>

                                    {{ $teacher->nik ?? '-' }}

                                </td>


                                <td>

                                    <span class="fw-semibold">

                                        {{ $teacher->name }}

                                    </span>

                                </td>


                                <td class="text-center">

                                    @if ($teacher->gender === 'male')
                                        L
                                    @elseif ($teacher->gender === 'female')
                                        P
                                    @else
                                        -
                                    @endif

                                </td>


                                <td>

                                    @forelse ($teacher->organizations
                                                as $organization)
                                        <span class="badge bg-secondary me-1">
                                            {{ $organization->name }}
                                        </span>

                                    @empty

                                        -
                                    @endforelse

                                </td>


                                <td class="text-center">

                                    @if ($teacher->is_active)
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

                                <td colspan="6"
                                    class="text-center
                                           text-muted py-4">

                                    <i
                                        class="bi bi-inbox fs-4
                                               d-block mb-2"></i>

                                    Belum ada data guru.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}

            @if ($teachers->hasPages())
                <div class="d-flex justify-content-end align-items-center p-3 border-top">

                    <div>

                        {{ $teachers->onEachSide(1)->links('pagination::bootstrap-5') }}

                    </div>

                </div>
            @endif

        </div>

    </div>

@stop
