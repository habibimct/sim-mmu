@extends('adminlte::page')

@section('title', 'Daftar Kelas')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <h1 class="m-0">
            Daftar Kelas
        </h1>

        @can('classes.manage')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">

                <i class="bi bi-plus-lg me-1"></i>
                Tambah Kelas

            </button>
            <a href="{{ route('admin.school-classes.bulk-create') }}" class="btn btn-success">
                <i class="fas fa-layer-group"></i>
                Buat Banyak Kelas
            </a>
        @endcan

    </div>

@stop


@section('content')

    {{-- Pesan sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-1"></i>

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>

        </div>
    @endif


    {{-- Pesan error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle me-1"></i>

            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>

        </div>
    @endif


    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                Data Kelas
            </h3>

        </div>


        <div class="card-body">

            {{-- Filter --}}
            @include('admin.school_classes.partials.filter')

            <div class="alert alert-info">

                <i class="bi bi-info-circle me-1"></i>

                Kelas pada tahun ajaran yang
                <strong>ditutup</strong> tetap ditampilkan sebagai
                histori dan tidak dapat diubah.

            </div>


            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="60">No.</th>
                            <th>Tahun Ajaran</th>
                            <th>Unit</th>
                            <th>Tingkat</th>
                            <th>Nama Kelas</th>
                            <th>Status Kelas</th>
                            <th>Status Periode</th>
                            <th width="180">Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($schoolClasses as $schoolClass)

                            <tr>

                                <td>
                                    {{ $schoolClasses->firstItem() + $loop->index }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $schoolClass->academicYear->name }}
                                </td>

                                <td>
                                    {{ $schoolClass->organization->name }}
                                </td>

                                <td>
                                    {{ $schoolClass->level }}
                                </td>

                                <td>
                                    {{ $schoolClass->name }}
                                </td>

                                <td>

                                    @if ($schoolClass->is_active)
                                        <span class="badge bg-success">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Nonaktif
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @if ($schoolClass->academicYear->is_active)
                                        <span class="badge bg-success">

                                            <i class="bi bi-unlock me-1"></i>
                                            Terbuka

                                        </span>
                                    @else
                                        <span class="badge bg-secondary">

                                            <i class="bi bi-lock me-1"></i>
                                            Ditutup

                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @can('classes.manage')
                                        @if ($schoolClass->academicYear->is_active)
                                            {{-- Edit --}}
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#modalEditKelas{{ $schoolClass->id }}" title="Edit Kelas">

                                                <i class="bi bi-pencil"></i>

                                            </button>


                                            {{-- Toggle --}}
                                            <form action="{{ route('admin.school-classes.toggle-status', $schoolClass) }}"
                                                method="POST" class="d-inline">

                                                @csrf
                                                @method('PATCH')

                                                @if ($schoolClass->is_active)
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        title="Nonaktifkan Kelas"
                                                        onclick="return confirm(
                                                            'Apakah Anda yakin ingin menonaktifkan kelas {{ $schoolClass->name }}?'
                                                        )">

                                                        <i class="bi bi-toggle-off"></i>

                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-success" title="Aktifkan Kelas"
                                                        onclick="return confirm(
                                                            'Apakah Anda yakin ingin mengaktifkan kelas {{ $schoolClass->name }}?'
                                                        )">

                                                        <i class="bi bi-toggle-on"></i>

                                                    </button>
                                                @endif

                                            </form>

                                            @if ($schoolClass->student_academic_years_count > 0)
                                                <button type="button" class="btn btn-sm btn-secondary" disabled
                                                    title="Kelas sudah memiliki atau pernah memiliki siswa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <form action="{{ route('admin.school-classes.destroy', $schoolClass) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus kelas {{ $schoolClass->name }}?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-muted small">

                                                <i class="bi bi-lock me-1"></i>
                                                Hanya lihat

                                            </span>
                                        @endif
                                    @endcan

                                </td>

                            </tr>


                            {{-- ================================================= --}}
                            {{-- MODAL EDIT KELAS --}}
                            {{-- ================================================= --}}

                            @if ($schoolClass->academicYear->is_active)
                                @include('admin.school_classes.partials.edit-modal', [
                                    'schoolClass' => $schoolClass,
                                ])
                            @endif

                        @empty

                            <tr>

                                <td colspan="7" class="text-center text-muted py-4">

                                    <i class="bi bi-diagram-3 fs-3 d-block mb-2"></i>

                                    Belum ada data kelas.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            @if ($schoolClasses->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        {{ $schoolClasses->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif

        </div>

    </div>


 @include('admin.school_classes.partials.create-modal')

@stop



@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modalTarget = @json(old('modal_target'));

            if (modalTarget) {

                const modalElement = document.getElementById(modalTarget);

                if (modalElement) {

                    const modal = bootstrap.Modal.getOrCreateInstance(
                        modalElement
                    );

                    modal.show();
                }
            }

        });
    </script>
@endpush
