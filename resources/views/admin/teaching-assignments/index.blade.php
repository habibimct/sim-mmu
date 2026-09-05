@extends('adminlte::page')

@section('title', 'Penugasan Mengajar')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1>
                <i class="bi bi-person-workspace me-1"></i>
                Penugasan Mengajar
            </h1>

            <p class="text-muted mb-0">
                Mengatur guru, kelas, dan mata pelajaran yang diajarkan.
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#modalCreateTeachingAssignment"
        >
            <i class="bi bi-plus-lg me-1"></i>
            Penugasan Baru
        </button>

    </div>

@stop


@section('content')

    {{-- Filter --}}
    @include(
        'admin.teaching-assignments.partials.filter'
    )


    {{-- Tabel --}}
    @include(
        'admin.teaching-assignments.partials.table'
    )


    {{-- Modal Tambah --}}
    @include(
        'admin.teaching-assignments.partials.modal-create'
    )


    {{-- Modal Edit --}}
    @include(
        'admin.teaching-assignments.partials.modal-edit'
    )


    {{-- Modal Hapus --}}
    @include(
        'admin.teaching-assignments.partials.modal-delete'
    )

@stop


@push('js')

    @include(
        'admin.teaching-assignments.partials.scripts'
    )

@endpush
