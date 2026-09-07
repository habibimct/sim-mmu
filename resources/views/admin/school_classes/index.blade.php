@extends('adminlte::page')

@section('title', 'Daftar Kelas')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <h1 class="m-0">
            Daftar Kelas
        </h1>
        <div>
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

    @if (session('bulk_delete_skipped'))

        <div class="alert alert-warning alert-dismissible fade show">

            <div class="fw-bold mb-2">

                <i class="bi bi-exclamation-triangle me-1"></i>

                Beberapa kelas tidak dihapus:

            </div>

            <ul class="mb-0">

                @foreach (session('bulk_delete_skipped') as $item)
                    <li>
                        {{ $item }}
                    </li>
                @endforeach

            </ul>

            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                Data Kelas
            </h3>


            @can('classes.manage')
                {{-- Form bulk delete --}}
                <form id="bulkDeleteForm" action="{{ route('admin.school-classes.bulk-destroy') }}" method="POST"
                    class="d-none">

                    @csrf
                    @method('DELETE')

                </form>


                <div class="card-tools">

                    <button type="submit" form="bulkDeleteForm" id="btnBulkDelete" class="btn btn-danger btn-sm" disabled>

                        <i class="bi bi-trash me-1"></i>

                        Hapus Terpilih

                        <span id="selectedClassCount" class="badge bg-light text-danger ms-1">
                            0
                        </span>

                    </button>

                </div>
            @endcan

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

            <form id="bulkDeleteForm" action="{{ route('admin.school-classes.bulk-destroy') }}" method="POST">

                @csrf
                @method('DELETE')
                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th width="45" class="text-center">
                                    <input type="checkbox" id="checkAllClasses" class="form-check-input"
                                        title="Pilih semua kelas yang dapat dihapus">
                                </th>

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
                                        @if (
                                            !$schoolClass->is_alumni &&
                                                $schoolClass->academicYear->is_active &&
                                                $schoolClass->student_academic_years_count == 0)
                                            <input type="checkbox" name="class_ids[]" value="{{ $schoolClass->id }}"
                                                class="form-check-input class-checkbox" form="bulkDeleteForm">
                                        @else
                                            <input type="checkbox" class="form-check-input" disabled
                                                title="Kelas ini tidak dapat dihapus">
                                        @endif
                                    </td>
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
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            title="Aktifkan Kelas"
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
            </form>


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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const checkAll =
                document.getElementById('checkAllClasses');

            const checkboxes =
                document.querySelectorAll('.class-checkbox');

            const deleteButton =
                document.getElementById('btnBulkDelete');

            const selectedCount =
                document.getElementById('selectedClassCount');

            const bulkDeleteForm =
                document.getElementById('bulkDeleteForm');


            /*
            |--------------------------------------------------------------------------
            | Update jumlah kelas yang dipilih
            |--------------------------------------------------------------------------
            */

            function updateSelectedCount() {

                const checked =
                    document.querySelectorAll(
                        '.class-checkbox:checked'
                    );

                const count =
                    checked.length;


                selectedCount.textContent =
                    count;


                deleteButton.disabled =
                    count === 0;


                /*
                |--------------------------------------------------------------------------
                | Update status checkbox "Pilih Semua"
                |--------------------------------------------------------------------------
                */

                if (checkAll) {

                    const enabledCheckboxes =
                        document.querySelectorAll(
                            '.class-checkbox'
                        );


                    if (
                        enabledCheckboxes.length > 0 &&
                        count === enabledCheckboxes.length
                    ) {

                        checkAll.checked = true;

                    } else {

                        checkAll.checked = false;
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Pilih semua
            |--------------------------------------------------------------------------
            */

            if (checkAll) {

                checkAll.addEventListener(
                    'change',
                    function() {

                        checkboxes.forEach(
                            function(checkbox) {

                                checkbox.checked =
                                    checkAll.checked;

                            }
                        );


                        updateSelectedCount();
                    }
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Perubahan checkbox
            |--------------------------------------------------------------------------
            */

            checkboxes.forEach(
                function(checkbox) {

                    checkbox.addEventListener(
                        'change',
                        updateSelectedCount
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Konfirmasi hapus massal
            |--------------------------------------------------------------------------
            */

            if (bulkDeleteForm) {

                bulkDeleteForm.addEventListener(
                    'submit',
                    function(event) {

                        const checked =
                            document.querySelectorAll(
                                '.class-checkbox:checked'
                            );


                        if (checked.length === 0) {

                            event.preventDefault();

                            return;
                        }


                        const count =
                            checked.length;


                        const confirmed =
                            confirm(
                                'Anda memilih ' +
                                count +
                                ' kelas untuk dihapus.\n\n' +
                                'Kelas yang memiliki atau pernah memiliki siswa akan dilewati secara otomatis.\n\n' +
                                'Apakah Anda yakin ingin melanjutkan?'
                            );


                        if (!confirmed) {

                            event.preventDefault();

                        }

                    }
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Kondisi awal
            |--------------------------------------------------------------------------
            */

            updateSelectedCount();

        });
    </script>
@endpush
