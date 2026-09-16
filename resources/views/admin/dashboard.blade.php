@extends('adminlte::page')

@section('title', 'Dashboard Admin Sistem')

@section('content_header')
    <h1>Dashboard Admin Sistem</h1>

    <button type="button" data-pwa-install class="btn btn-primary">
        <i class="bi bi-download me-1"></i>
        Install SIM-MMU
    </button>
@stop

@section('content')

    <div class="row">

        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Pengguna</p>
                </div>
                <i class="fas fa-users small-box-icon"></i>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>{{ $totalStudents }}</h3>
                    <p>Siswa</p>
                </div>
                <i class="fas fa-user-graduate small-box-icon"></i>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h3>{{ $totalTeachers }}</h3>
                    <p>Guru</p>
                </div>
                <i class="fas fa-chalkboard-teacher small-box-icon"></i>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-danger">
                <div class="inner">
                    <h3>{{ $totalUnits }}</h3>
                    <p>Unit</p>
                </div>
                <i class="fas fa-building small-box-icon"></i>
            </div>
        </div>

    </div>

    <div class="card mb-2">
        <div class="card-header">
            <h3 class="card-title">Selamat Datang</h3>
        </div>

        <div class="card-body">
            Selamat datang di Sistem Informasi INDUK.
        </div>
    </div>


    <div id="activity-detail-container"></div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">

            <h3 class="card-title mb-0">
                <i class="bi bi-clock-history me-1"></i>
                Activity Log
            </h3>

            <div class="ms-auto">
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-primary">

                    <i class="bi bi-list-ul me-1"></i>
                    Lihat Semua

                </a>
            </div>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle mb-0">

                    <thead>
                        <tr>
                            <th width="160">Waktu</th>
                            <th>User</th>
                            <th>Aktivitas</th>
                            <th>Modul</th>
                            <th>Data</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($activities as $activity)
                            <tr>
                                <td>
                                    {{ $activity->created_at?->format('d/m/Y H:i:s') }}
                                </td>

                                <td>
                                    {{ $activity->causer?->name ?? ($activity->user_id ? $activityUsers[$activity->user_id] ?? 'Sistem' : 'Sistem') }}
                                </td>

                                <td>
                                    {{ ucfirst($activity->event ?? $activity->description) }}
                                </td>

                                <td>
                                    {{ $activity->log_name ?? '-' }}
                                </td>

                                <td>
                                    {{ class_basename($activity->subject_type ?? '') }}
                                    @if ($activity->subject_id)
                                        #{{ $activity->subject_id }}
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary btn-activity-detail"
                                        data-url="{{ route('admin.activity-logs.detail', $activity) }}" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    Belum ada aktivitas.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>
@stop


@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-activity-detail').forEach(function(button) {
                button.addEventListener('click', function() {
                    const url = this.dataset.url;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Gagal mengambil detail aktivitas.');
                            }

                            return response.text();
                        })
                        .then(html => {
                            document
                                .getElementById('activity-detail-container')
                                .innerHTML = html;

                            const modalElement = document.getElementById('activityDetailModal');

                            const modal = new bootstrap.Modal(modalElement);

                            modal.show();
                        })
                        .catch(error => {
                            alert(error.message);
                        });
                });
            });
        });
    </script>
@stop
