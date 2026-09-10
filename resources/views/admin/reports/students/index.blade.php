@extends('adminlte::page')

@section('title', 'Laporan Siswa')

@section('content_header')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

        <div>
            <h1 class="m-0">
                Laporan Siswa
            </h1>

            <div class="text-muted small mt-1">
                Rekapitulasi data siswa berdasarkan tahun ajaran,
                unit, dan kelas.
            </div>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('admin.reports.students.excel', request()->query()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>
                Excel
            </a>

            <a href="{{ route('admin.reports.students.pdf', request()->query()) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>
                PDF
            </a>

        </div>

    </div>

@stop

@section('content')

    {{-- Filter --}}
    @include('admin.reports.students.partials.filter')

    {{-- Ringkasan --}}
    @include('admin.reports.students.partials.summary')

    {{-- Tabel --}}
    @include('admin.reports.students.partials.table')

@stop
