@extends('adminlte::page')

@section('title', 'Daftar Siswa')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

        <h1 class="m-0">Daftar Siswa</h1>

        <div class="d-flex flex-wrap gap-2">

            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCreateStudent">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Siswa
            </button>

            <a href="{{ route('admin.students.export', request()->query()) }}" class="btn btn-outline-success">
                <i class="fas fa-file-excel me-1"></i>
                Download Excel
            </a>

            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                data-bs-target="#importStudentModal">
                <i class="fas fa-file-upload me-1"></i>
                Import Excel
            </button>

            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#studentFilterModal">
                <i class="bi bi-funnel me-1"></i>
                Filter
            </button>

        </div>

    </div>
@stop

@push('css')
    <style>
        .card-footer .pagination {
            margin-bottom: 0;
        }
    </style>
@endpush

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Summary --}}
    @include('admin.students.partials.summary')

    {{-- Tabel --}}
    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Data Siswa
            </h3>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat Lahir</th>
                            <th>Tanggal Lahir</th>
                            <th>Tahun Akademik</th>
                            <th>Kelas</th>
                            <th>Organisasi</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($students as $student)
                            @php
                                $academicPlacement = $student->studentAcademicYears->first();
                            @endphp

                            <tr>

                                <td>
                                    {{ $students->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    {{ $student->nis }}
                                </td>

                                <td>
                                    {{ $student->name }}
                                </td>

                                <td>
                                    {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </td>

                                <td>
                                    {{ $student->birth_place ?? '-' }}
                                </td>

                                <td>
                                    {{ $student->birth_date ? $student->birth_date->format('d-m-Y') : '-' }}
                                </td>

                                {{-- Tahun Akademik --}}
                                <td>
                                    {{ $academicPlacement?->academicYear?->name ?? '-' }}
                                </td>

                                {{-- Kelas --}}
                                <td>
                                    {{ $academicPlacement?->schoolClass?->name ?? '-' }}
                                </td>

                                {{-- Organisasi --}}
                                <td>
                                    {{ $academicPlacement?->organization?->name ?? ($student->organization?->name ?? '-') }}
                                </td>

                                {{-- Status --}}
                                <td>

                                    @if ($student->is_active)
                                        <span class="badge bg-success">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Nonaktif
                                        </span>
                                    @endif

                                </td>

                                {{-- Aksi --}}
                                <td>

                                    <button type="button" class="btn btn-warning btn-sm btn-edit-student"
                                        data-bs-toggle="modal" data-bs-target="#modalEditStudent"
                                        data-student-id="{{ $student->id }}" data-student-nis="{{ $student->nis }}"
                                        data-student-name="{{ $student->name }}"
                                        data-student-gender="{{ $student->gender }}"
                                        data-student-birth-place="{{ $student->birth_place ?? '' }}"
                                        data-student-birth-date="{{ $student->birth_date?->format('Y-m-d') }}"
                                        data-student-active="{{ $student->is_active ? '1' : '0' }}"
                                        data-organization-id="{{ $academicPlacement?->organization_id ?? $student->organization_id }}"
                                        data-academic-year-id="{{ $academicPlacement?->academic_year_id ?? '' }}"
                                        data-school-class-id="{{ $academicPlacement?->school_class_id ?? '' }}"
                                        title="Edit siswa">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>


                                    <button type="button"
                                        class="btn btn-sm {{ $student->is_active ? 'btn-danger' : 'btn-success' }}"
                                        data-bs-toggle="modal" data-bs-target="#toggleStudentModal"
                                        data-student-id="{{ $student->id }}" data-student-name="{{ $student->name }}"
                                        data-student-active="{{ $student->is_active ? '1' : '0' }}"
                                        title="{{ $student->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">

                                        @if ($student->is_active)
                                            <i class="bi bi-person-x"></i>
                                        @else
                                            <i class="bi bi-person-check"></i>
                                        @endif

                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="11" class="text-center py-4">

                                    <i class="bi bi-info-circle"></i>

                                    Belum ada data siswa.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
        {{-- =====================================================
            PAGINATION
            ====================================================== --}}
        @if ($students->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    {{ $students->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

    </div>
@stop


@include('admin.students.partials.modal-filter')
@include('admin.students.partials.modal-edit')
@include('admin.students.partials.modal-create')
@include('admin.students.partials.modal-import')
@include('admin.students.partials.modal-toggle')

<div id="classStudentsModalContainer"></div>



@section('js')
    <script>
        document.addEventListener('click', function(event) {
            const button = event.target.closest('.btn-class-students');

            if (!button) {
                return;
            }

            const url = button.dataset.url;
            const container = document.getElementById(
                'classStudentsModalContainer'
            );

            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2 text-muted">
                        Memuat daftar siswa...
                    </div>
                </div>
            `;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memuat daftar siswa.');
                }

                return response.text();
            })
            .then(html => {
                container.innerHTML = html;

                const modalElement =
                    document.getElementById('classStudentsModal');

                const modal =
                    new bootstrap.Modal(modalElement);

                modal.show();
            })
            .catch(error => {
                container.innerHTML = '';

                alert(error.message);
            });
        });

        console.log('SCRIPT SISWA AKTIF');
    </script>
@stop
