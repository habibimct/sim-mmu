@extends('adminlte::page')

@section('title', 'Activity Log')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">

        <h1 class="m-0">
            <i class="bi bi-clock-history me-1"></i>
            Activity Log
        </h1>

        <div class="d-flex gap-2">

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#activityLogFilterModal">

                <i class="bi bi-funnel me-1"></i>
                Filter

            </button>

            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Kembali

            </a>

        </div>

    </div>
    
    @include('admin.activity-logs.partials.filter')
@stop

@section('content')

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Riwayat Aktivitas Sistem
            </h3>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aktivitas</th>
                            <th>Modul</th>
                            <th>Data</th>
                            <th width="80">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($activities as $activity)

                            <tr>

                                <td>
                                    {{ $activities->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    {{ $activity->created_at?->format('d/m/Y H:i:s') ?? '-' }}
                                </td>

                                <td>
                                    {{ $activity->causer?->name ?? ($activity->user_id ? 'User #' . $activity->user_id : 'Sistem') }}
                                </td>

                                <td>
                                    {{ ucfirst($activity->event ?? ($activity->description ?? '-')) }}
                                </td>

                                <td>
                                    {{ $activity->log_name ?? '-' }}
                                </td>

                                <td>
                                    @if ($activity->subject_type)
                                        {{ class_basename($activity->subject_type) }}

                                        @if ($activity->subject_id)
                                            #{{ $activity->subject_id }}
                                        @endif
                                    @else
                                        -
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada aktivitas.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if ($activities->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        {{ $activities->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif

        </div>

    </div>

    <div id="activity-detail-container"></div>

@stop


@section('js')

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                document
                    .querySelectorAll('.btn-activity-detail')
                    .forEach(function(button) {

                        button.addEventListener(
                            'click',
                            function() {

                                const url =
                                    this.dataset.url;

                                fetch(url)
                                    .then(response => {

                                        if (!response.ok) {
                                            throw new Error(
                                                'Gagal mengambil detail aktivitas.'
                                            );
                                        }

                                        return response.text();
                                    })
                                    .then(html => {

                                        document
                                            .getElementById(
                                                'activity-detail-container'
                                            )
                                            .innerHTML = html;

                                        const modalElement =
                                            document.getElementById(
                                                'activityDetailModal'
                                            );

                                        const modal =
                                            new bootstrap.Modal(
                                                modalElement
                                            );

                                        modal.show();
                                    })
                                    .catch(error => {
                                        alert(error.message);
                                    });

                            }
                        );

                    });

            }
        );
    </script>

@stop
