@extends('adminlte::page')

@section('title', 'Laporan Audit')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1 class="mb-1">
                <i class="bi bi-shield-check me-1"></i>
                Laporan Audit
            </h1>

            <div class="text-muted">
                Riwayat aktivitas dan perubahan data sistem
            </div>
        </div>

        <div class="d-flex gap-2">

            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                data-bs-target="#modalFilterAuditReport">
                <i class="bi bi-funnel me-1"></i>
                Filter
            </button>

            <a href="{{ route('admin.reports.audit.export-excel', request()->query()) }}" class="btn btn-success">

                <i class="bi bi-file-earmark-excel me-1"></i>
                Export Excel

            </a>

            <a href="{{ route('admin.reports.audit.export-pdf', request()->query()) }}" class="btn btn-danger">

                <i class="bi bi-file-earmark-pdf me-1"></i>
                Export PDF

            </a>

        </div>

    </div>
@stop


@section('content')

    {{-- Summary --}}
    @include('admin.reports.audit.partials.summary')


    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-1"></i>
                        Riwayat Aktivitas
                    </h5>

                    <small class="text-muted">
                        Menampilkan aktivitas terbaru
                    </small>
                </div>

                <span class="badge bg-secondary">
                    {{ $activities->total() }} Aktivitas
                </span>

            </div>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="text-center" width="60">
                                No
                            </th>

                            <th width="150">
                                Waktu
                            </th>

                            <th>
                                Pengguna
                            </th>

                            <th>
                                Aktivitas
                            </th>

                            <th>
                                Modul
                            </th>

                            <th>
                                Data
                            </th>

                            <th class="text-center" width="100">
                                Aksi
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($activities as $index => $activity)
                            <tr>

                                <td class="text-center">
                                    {{ $activities->firstItem() + $index }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $activity->created_at?->format('d/m/Y') }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $activity->created_at?->format('H:i:s') }}
                                    </small>
                                </td>

                                <td>
                                    @if ($activity->causer)
                                        <div class="fw-semibold">
                                            {{ $activity->causer->name }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $activity->causer->email }}
                                        </small>
                                    @else
                                        <span class="text-muted">
                                            Sistem
                                        </span>
                                    @endif
                                </td>

                                <td>

                                    @if ($activity->event === 'created')
                                        <span class="badge bg-success">
                                            Dibuat
                                        </span>
                                    @elseif ($activity->event === 'updated')
                                        <span class="badge bg-warning text-dark">
                                            Diperbarui
                                        </span>
                                    @elseif ($activity->event === 'deleted')
                                        <span class="badge bg-danger">
                                            Dihapus
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ $activity->event ?: 'Aktivitas' }}
                                        </span>
                                    @endif

                                </td>

                                <td>
                                    @if ($activity->log_name === 'finance_deposit')
                                        <span class="fw-semibold">
                                            Setoran
                                        </span>
                                    @elseif ($activity->log_name === 'finance_transaction')
                                        <span class="fw-semibold">
                                            Transaksi Keuangan
                                        </span>
                                    @else
                                        {{ $activity->log_name ?: '-' }}
                                    @endif
                                </td>

                                <td>
                                    @if ($activity->subject_type)
                                        <div class="fw-semibold">
                                            #{{ $activity->subject_id }}
                                        </div>

                                        <small class="text-muted">
                                            {{ class_basename($activity->subject_type) }}
                                        </small>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-center">

                                    <button type="button" class="btn btn-sm btn-outline-primary btn-detail-audit"
                                        data-id="{{ $activity->id }}">
                                        <i class="bi bi-eye"></i>
                                        Detail
                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>

                                        Belum ada aktivitas audit.

                                    </div>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($activities->hasPages())
        <div class="d-flex justify-content-end align-items-center p-3 border-top">
            <div>
                {{ $activities->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
            </div>
        @endif

    </div>

@stop

@include('admin.reports.audit.partials.filter')
@include('admin.reports.audit.partials.detail')



@section('js')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modalElement =
                document.getElementById('modalAuditDetail');

            const detailModal =
                new bootstrap.Modal(modalElement);

            const loading =
                document.getElementById('auditDetailLoading');

            const content =
                document.getElementById('auditDetailContent');

            document
                .querySelectorAll('.btn-detail-audit')
                .forEach(function(button) {

                    button.addEventListener('click', function() {

                        const activityId =
                            this.dataset.id;

                        loading.classList.remove('d-none');
                        content.classList.add('d-none');

                        detailModal.show();

                        fetch(
                                `{{ url('admin/laporan/audit') }}/${activityId}/detail`, {
                                    headers: {
                                        'Accept': 'application/json',

                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                }
                            )

                            .then(function(response) {

                                if (!response.ok) {
                                    throw new Error(
                                        'Gagal mengambil data audit.'
                                    );
                                }

                                return response.json();
                            })

                            .then(function(data) {

                                /*
                                |--------------------------------------------------------------------------
                                | Informasi utama
                                |--------------------------------------------------------------------------
                                */

                                document
                                    .getElementById('detailAuditDate')
                                    .textContent =
                                    data.date ?? '-';

                                document
                                    .getElementById('detailAuditUser')
                                    .textContent =
                                    data.user?.name ?? 'Sistem';

                                document
                                    .getElementById('detailAuditDescription')
                                    .textContent =
                                    data.description ?? '-';


                                /*
                                |--------------------------------------------------------------------------
                                | Event
                                |--------------------------------------------------------------------------
                                */

                                let eventHtml = '';

                                if (data.event === 'created') {

                                    eventHtml =
                                        '<span class="badge bg-success">' +
                                        'Dibuat' +
                                        '</span>';

                                } else if (data.event === 'updated') {

                                    eventHtml =
                                        '<span class="badge bg-warning text-dark">' +
                                        'Diperbarui' +
                                        '</span>';

                                } else if (data.event === 'deleted') {

                                    eventHtml =
                                        '<span class="badge bg-danger">' +
                                        'Dihapus' +
                                        '</span>';

                                } else {

                                    eventHtml =
                                        '<span class="badge bg-secondary">' +
                                        (data.event ?? '-') +
                                        '</span>';
                                }

                                document
                                    .getElementById('detailAuditEvent')
                                    .innerHTML =
                                    eventHtml;


                                /*
                                |--------------------------------------------------------------------------
                                | Modul
                                |--------------------------------------------------------------------------
                                */

                                let module = '-';

                                if (
                                    data.module ===
                                    'finance_deposit'
                                ) {
                                    module = 'Setoran';

                                } else if (
                                    data.module ===
                                    'finance_transaction'
                                ) {
                                    module =
                                        'Transaksi Keuangan';

                                } else if (data.module) {
                                    module = data.module;
                                }

                                document
                                    .getElementById('detailAuditModule')
                                    .textContent =
                                    module;


                                /*
                                |--------------------------------------------------------------------------
                                | Subject
                                |--------------------------------------------------------------------------
                                */

                                document
                                    .getElementById('detailAuditSubjectId')
                                    .textContent =
                                    data.subject?.id ?
                                    '#' + data.subject.id :
                                    '-';


                                /*
                                |--------------------------------------------------------------------------
                                | Organisasi
                                |--------------------------------------------------------------------------
                                */

                                document
                                    .getElementById('detailAuditOrganization')
                                    .textContent =
                                    data.organization?.name ?? '-';


                                /*
                                |--------------------------------------------------------------------------
                                | Informasi teknis
                                |--------------------------------------------------------------------------
                                */

                                document
                                    .getElementById('detailAuditIp')
                                    .textContent =
                                    data.ip_address ?? '-';

                                document
                                    .getElementById('detailAuditUserAgent')
                                    .textContent =
                                    data.user_agent ?? '-';


                                /*
                                |--------------------------------------------------------------------------
                                | Perubahan
                                |--------------------------------------------------------------------------
                                */

                                const changesBody =
                                    document.getElementById(
                                        'detailAuditChanges'
                                    );

                                changesBody.innerHTML = '';

                                if (
                                    Array.isArray(data.changes) &&
                                    data.changes.length
                                ) {

                                    data.changes.forEach(
                                        function(change) {

                                            const row =
                                                document.createElement('tr');

                                            const field =
                                                document.createElement('td');

                                            const oldValue =
                                                document.createElement('td');

                                            const newValue =
                                                document.createElement('td');

                                            field.className =
                                                'fw-semibold';

                                            oldValue.className =
                                                'text-danger';

                                            newValue.className =
                                                'text-success';

                                            field.textContent =
                                                change.field ?? '-';

                                            oldValue.textContent =
                                                change.old ?? '-';

                                            newValue.textContent =
                                                change.new ?? '-';

                                            row.appendChild(field);
                                            row.appendChild(oldValue);
                                            row.appendChild(newValue);

                                            changesBody.appendChild(row);
                                        }
                                    );

                                } else {

                                    changesBody.innerHTML =
                                        '<tr>' +
                                        '<td colspan="3" ' +
                                        'class="text-center text-muted py-3">' +
                                        'Tidak ada perubahan data.' +
                                        '</td>' +
                                        '</tr>';
                                }


                                loading.classList.add('d-none');
                                content.classList.remove('d-none');

                            })

                            .catch(function(error) {

                                loading.classList.add('d-none');
                                content.classList.remove('d-none');

                                document
                                    .getElementById('detailAuditDescription')
                                    .innerHTML =
                                    '<span class="text-danger">' +
                                    error.message +
                                    '</span>';
                            });

                    });

                });

        });
    </script>

@stop
