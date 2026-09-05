@extends('adminlte::page')

@section('title', 'Mata Pelajaran')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1>
                <i class="bi bi-book me-1"></i>
                Mata Pelajaran
            </h1>

            <p class="text-muted mb-0">
                Kelola mata pelajaran unit.
            </p>
        </div>

        <div class="d-flex gap-2">

            {{-- Filter --}}
            <button
                type="button"
                class="btn btn-outline-secondary"
                data-bs-toggle="modal"
                data-bs-target="#modalFilterSubject"
            >
                <i class="bi bi-funnel me-1"></i>
                Filter
            </button>

            {{-- Tambah --}}
            <button
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalCreateSubject"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Mata Pelajaran
            </button>

        </div>

    </div>

@stop


@section('content')

    {{-- Pesan sukses --}}
    @if (session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-1"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- Pesan error --}}
    @if ($errors->any())

        <div class="alert alert-danger alert-dismissible fade show">

            <strong>
                Periksa kembali data yang dimasukkan.
            </strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- Tabel --}}
    @include('admin.subjects.partials.table')


    {{-- Modal Filter --}}
    @include('admin.subjects.partials.filter')


    {{-- Modal Tambah --}}
    @include('admin.subjects.partials.modal-create')


    {{-- Modal Edit --}}
    @include('admin.subjects.partials.modal-edit')

    {{-- Modal Hapus --}}
    @include('admin.subjects.partials.modal-delete')

@stop


@push('js')

    @include('admin.subjects.partials.scripts')

@endpush
