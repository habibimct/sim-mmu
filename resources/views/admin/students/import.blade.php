@extends('adminlte::page')

@section('title', 'Upload Siswa')

@section('content_header')
    <h1>Upload Siswa</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if (session('import_failures'))
        <div class="alert alert-danger">
            <strong>Data Excel tidak valid:</strong>

            <ul class="mb-0 mt-2">
                @foreach (session('import_failures') as $failure)
                    @php
                        $excelRow = $failure['row'] ?? null;
                        $dataRow = $excelRow ? $excelRow - 1 : '-';
                        $nis = $failure['values']['nis'] ?? '-';
                        $errors = $failure['errors'] ?? [];
                    @endphp

                    <li>
                        <strong>Data ke-{{ $dataRow }}</strong>
                        (NIS: {{ $nis }})
                        :
                        {{ implode(', ', $errors) }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Upload Data Siswa</h3>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.students.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="organization_id">Organisasi</label>

                    <select id="organization_id" name="organization_id" class="form-control" required>
                        <option value="">-- Pilih Organisasi --</option>

                        @foreach ($organizations as $organization)
                            <option value="{{ $organization->id }}">
                                {{ $organization->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="file">File Excel</label>

                    <input type="file" id="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    Upload
                </button>

                <a href="{{ route('admin.students.import.template') }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i>
                    Download Template Excel
                </a>
            </form>
        </div>
    </div>

@stop
