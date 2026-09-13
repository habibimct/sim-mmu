@extends('adminlte::page')
@section('title', 'Tahun Ajaran')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            Tahun Ajaran
        </h1>
        @can('academic_years.manage')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahTahunAjaran">

                <i class="bi bi-plus-lg me-1"></i>
                Tambah Tahun Ajaran

            </button>
        @endcan

    </div>
@stop


@section('content')
    {{-- Pesan sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>
        </div>
    @endif


    {{-- Pesan error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>
        </div>
    @endif


    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Daftar Tahun Ajaran
            </h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-1"></i>
                Tahun ajaran <strong>Terbuka</strong>
                dapat dikelola.
                Tahun ajaran <strong>Ditutup</strong>
                hanya dapat dilihat sebagai histori.
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No.</th>
                            <th>Tahun Ajaran</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Status</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($academicYears as $academicYear)
                            <tr>
                                <td>
                                    {{ $academicYears->firstItem() + $loop->index }}
                                </td>
                                <td class="fw-semibold">
                                    {{ $academicYear->name }}
                                </td>
                                <td>
                                    {{ $academicYear->start_date?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td>
                                    {{ $academicYear->end_date?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td>
                                    @if ($academicYear->is_active)
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
                                    @can('settings.manage')
                                        {{-- Edit --}}
                                        @if ($academicYear->is_active)
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#modalEditTahunAjaran{{ $academicYear->id }}" title="Edit">

                                                <i class="bi bi-pencil"></i>

                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-warning" disabled
                                                title="Tahun akademik sudah ditutup">

                                                <i class="bi bi-pencil"></i>

                                            </button>
                                        @endif


                                        {{-- Toggle --}}
                                        <form action="{{ route('admin.academic-years.toggle-status', $academicYear) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            @if ($academicYear->is_active)
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    title="Tutup Tahun Akademik"
                                                    onclick="return confirm('Tutup tahun akademik {{ $academicYear->name }}?')">
                                                    <i class="bi bi-lock"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-success"
                                                    title="Buka Kembali Tahun Akademik"
                                                    onclick="return confirm('Buka kembali tahun akademik {{ $academicYear->name }}?')">
                                                    <i class="bi bi-unlock"></i>
                                                </button>
                                            @endif

                                        </form>
                                        {{-- Hapus --}}
                                        @if (
                                            $academicYear->is_active &&
                                                $academicYear->school_classes_count == 0 &&
                                                $academicYear->student_academic_years_count == 0)
                                            <form action="{{ route('admin.academic-years.destroy', $academicYear) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm(
                                                    'Yakin ingin menghapus tahun akademik {{ $academicYear->name }}?'
                                                )">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-secondary" disabled
                                                title="Tidak dapat dihapus karena sudah memiliki kelas/data siswa atau sudah ditutup">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    @endcan

                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-calendar3 fs-3 d-block mb-2"></i>
                                    Belum ada tahun ajaran.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            @if ($academicYears->hasPages())
                <div class="mt-3">
                    {{ $academicYears->links() }}
                </div>
            @endif

        </div>
    </div>

    @include('admin.academic_years.modals.create')
    @include('admin.academic_years.modals.edit')

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
