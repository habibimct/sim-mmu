@extends('adminlte::page')

@section('title', 'Laporan Setoran')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1 class="mb-1">
                Laporan Setoran
            </h1>

            <p class="text-muted mb-0">
                Laporan setoran dari unit kepada organisasi induk.
            </p>
        </div>


        <div class="d-flex gap-2">

            {{-- FILTER --}}
            <button type="button"
                    class="btn btn-outline-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalFilterDepositReport">

                <i class="bi bi-funnel"></i>

                Filter

            </button>


            {{-- EXPORT EXCEL --}}
            <a href="{{ route(
                'admin.reports.deposits.export-excel',
                request()->query()
            ) }}"
               class="btn btn-success">

                <i class="bi bi-file-earmark-excel"></i>

                Export Excel

            </a>


            {{-- EXPORT PDF --}}
            <a href="{{ route(
                'admin.reports.deposits.export-pdf',
                request()->query()
            ) }}"
               class="btn btn-danger">

                <i class="bi bi-file-earmark-pdf"></i>

                Export PDF

            </a>

        </div>

    </div>

@stop


@section('content')

    {{-- SUMMARY --}}
    @include(
        'admin.reports.deposits.partials.summary'
    )


    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <h3 class="card-title mb-0">

                    <i class="bi bi-arrow-left-right me-1"></i>

                    Daftar Setoran

                </h3>

                <span class="text-muted small">

                    {{ $deposits->total() }}
                    data

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped
                              align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="text-center"
                                style="width: 50px;">
                                No
                            </th>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                Unit Pengirim
                            </th>

                            <th>
                                Tujuan
                            </th>

                            <th class="text-end">
                                Nominal
                            </th>

                            <th>
                                Metode
                            </th>

                            <th class="text-center">
                                Status
                            </th>

                            <th class="text-center"
                                style="width: 80px;">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($deposits as $deposit)

                            <tr>

                                {{-- NO --}}
                                <td class="text-center">

                                    {{ $deposits->firstItem() + $loop->index }}

                                </td>


                                {{-- TANGGAL --}}
                                <td>

                                    {{ $deposit->deposit_date?->format('d/m/Y') }}

                                </td>


                                {{-- UNIT PENGIRIM --}}
                                <td>

                                    <div class="fw-semibold">

                                        {{ $deposit->organization?->name ?? '-' }}

                                    </div>

                                </td>


                                {{-- TUJUAN --}}
                                <td>

                                    <div class="fw-semibold">

                                        {{ $deposit->targetOrganization?->name ?? '-' }}

                                    </div>

                                </td>


                                {{-- NOMINAL --}}
                                <td class="text-end">

                                    <span class="fw-semibold">

                                        Rp
                                        {{ number_format(
                                            $deposit->amount,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </span>

                                </td>


                                {{-- METODE --}}
                                <td>

                                    @switch($deposit->payment_method)

                                        @case('cash')

                                            Tunai

                                            @break

                                        @case('bank_transfer')

                                            Transfer Bank

                                            @break

                                        @case('online')

                                            Online

                                            @break

                                        @default

                                            {{ $deposit->payment_method }}

                                    @endswitch

                                </td>


                                {{-- STATUS --}}
                                <td class="text-center">

                                    @switch($deposit->status)

                                        @case('confirmed')

                                            <span class="badge bg-success">
                                                Dikonfirmasi
                                            </span>

                                            @break

                                        @case('pending')

                                            <span class="badge bg-warning text-dark">
                                                Menunggu Konfirmasi
                                            </span>

                                            @break

                                        @case('rejected')

                                            <span class="badge bg-danger">
                                                Ditolak
                                            </span>

                                            @break

                                        @default

                                            <span class="badge bg-secondary">
                                                {{ $deposit->status }}
                                            </span>

                                    @endswitch

                                </td>


                                {{-- AKSI --}}
                                <td class="text-center">

                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary btn-detail-deposit"
                                            data-id="{{ $deposit->id }}"
                                            title="Detail">

                                        <i class="bi bi-eye"></i>

                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                        Tidak ada data setoran.

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- PAGINATION --}}
        @if ($deposits->hasPages())

            <div class="card-footer bg-white">

                {{ $deposits->onEachSide(1)->links('pagination::bootstrap-5') }}

            </div>

        @endif

    </div>


    {{-- FILTER MODAL --}}
    @include(
        'admin.reports.deposits.partials.filter'
    )


    {{-- DETAIL MODAL --}}
    @include(
        'admin.reports.deposits.partials.detail'
    )

@stop




@section('js')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const detailModal =
        new bootstrap.Modal(
            document.getElementById('modalDepositDetail')
        );

    const loading =
        document.getElementById(
            'depositDetailLoading'
        );

    const content =
        document.getElementById(
            'depositDetailContent'
        );


    document
        .querySelectorAll('.btn-detail-deposit')
        .forEach(function (button) {

            button.addEventListener(
                'click',
                function () {

                    const depositId =
                        this.dataset.id;

                    loading.classList.remove(
                        'd-none'
                    );

                    content.classList.add(
                        'd-none'
                    );

                    detailModal.show();


                    fetch(
                        `{{ url('admin/laporan/setoran') }}/${depositId}/detail`,
                        {
                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            }
                        }
                    )

                    .then(function (response) {

                        if (!response.ok) {
                            throw new Error(
                                'Gagal mengambil data setoran.'
                            );
                        }

                        return response.json();
                    })

                    .then(function (data) {

                        /*
                        |--------------------------------------------------------------------------
                        | Tanggal
                        |--------------------------------------------------------------------------
                        */

                        document
                            .getElementById(
                                'detailDepositDate'
                            )
                            .textContent =
                            data.deposit_date ?? '-';


                        /*
                        |--------------------------------------------------------------------------
                        | Organisasi
                        |--------------------------------------------------------------------------
                        */

                        document
                            .getElementById(
                                'detailDepositOrganization'
                            )
                            .textContent =
                            data.organization?.name ?? '-';


                        /*
                        |--------------------------------------------------------------------------
                        | Tujuan
                        |--------------------------------------------------------------------------
                        */

                        document
                            .getElementById(
                                'detailDepositTarget'
                            )
                            .textContent =
                            data.target_organization?.name ?? '-';


                        /*
                        |--------------------------------------------------------------------------
                        | Nominal
                        |--------------------------------------------------------------------------
                        */

                        document
                            .getElementById(
                                'detailDepositAmount'
                            )
                            .textContent =
                            'Rp ' +
                            Number(
                                data.amount ?? 0
                            ).toLocaleString(
                                'id-ID'
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Metode
                        |--------------------------------------------------------------------------
                        */

                        let method = '-';

                        if (
                            data.payment_method ===
                            'cash'
                        ) {
                            method = 'Tunai';

                        } else if (
                            data.payment_method ===
                            'bank_transfer'
                        ) {
                            method =
                                'Transfer Bank';

                        } else if (
                            data.payment_method ===
                            'online'
                        ) {
                            method = 'Online';

                        }

                        document
                            .getElementById(
                                'detailDepositMethod'
                            )
                            .textContent =
                            method;


                        /*
                        |--------------------------------------------------------------------------
                        | Status
                        |--------------------------------------------------------------------------
                        */

                        let statusHtml = '';

                        if (
                            data.status ===
                            'confirmed'
                        ) {

                            statusHtml =
                                '<span class="badge bg-success">' +
                                'Dikonfirmasi' +
                                '</span>';

                        } else if (
                            data.status ===
                            'pending'
                        ) {

                            statusHtml =
                                '<span class="badge bg-warning text-dark">' +
                                'Menunggu Konfirmasi' +
                                '</span>';

                        } else if (
                            data.status ===
                            'rejected'
                        ) {

                            statusHtml =
                                '<span class="badge bg-danger">' +
                                'Ditolak' +
                                '</span>';

                        } else {

                            statusHtml =
                                '<span class="badge bg-secondary">' +
                                (data.status ?? '-') +
                                '</span>';
                        }

                        document
                            .getElementById(
                                'detailDepositStatus'
                            )
                            .innerHTML =
                            statusHtml;


                        /*
                        |--------------------------------------------------------------------------
                        | Keterangan
                        |--------------------------------------------------------------------------
                        */

                        document
                            .getElementById(
                                'detailDepositDescription'
                            )
                            .textContent =
                            data.description ?? '-';


                        /*
                        |--------------------------------------------------------------------------
                        | Creator
                        |--------------------------------------------------------------------------
                        */

                        document
                            .getElementById(
                                'detailDepositCreator'
                            )
                            .textContent =
                            data.creator?.name ?? '-';


                        /*
                        |--------------------------------------------------------------------------
                        | Confirmer
                        |--------------------------------------------------------------------------
                        */

                        document
                            .getElementById(
                                'detailDepositConfirmer'
                            )
                            .textContent =
                            data.confirmer?.name ?? '-';


                        /*
                        |--------------------------------------------------------------------------
                        | Confirmed At
                        |--------------------------------------------------------------------------
                        */

                        document
                            .getElementById(
                                'detailDepositConfirmedAt'
                            )
                            .textContent =
                            data.confirmed_at ?? '-';


                        /*
                        |--------------------------------------------------------------------------
                        | Bukti Setoran
                        |--------------------------------------------------------------------------
                        */

                        const proofWrapper =
                            document.getElementById(
                                'detailDepositProofWrapper'
                            );

                        const proof =
                            document.getElementById(
                                'detailDepositProof'
                            );

                        if (
                            data.proof?.path
                        ) {

                            proofWrapper
                                .classList
                                .remove(
                                    'd-none'
                                );

                            proof.href =
                                `{{ asset('storage') }}/${data.proof.path}`;

                        } else {

                            proofWrapper
                                .classList
                                .add(
                                    'd-none'
                                );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Alasan penolakan
                        |--------------------------------------------------------------------------
                        */

                        const rejectionWrapper =
                            document.getElementById(
                                'detailDepositRejectionWrapper'
                            );

                        const rejectionReason =
                            document.getElementById(
                                'detailDepositRejectionReason'
                            );

                        if (
                            data.status ===
                            'rejected'
                            &&
                            data.rejection_reason
                        ) {

                            rejectionWrapper
                                .classList
                                .remove(
                                    'd-none'
                                );

                            rejectionReason
                                .textContent =
                                data.rejection_reason;

                        } else {

                            rejectionWrapper
                                .classList
                                .add(
                                    'd-none'
                                );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Tampilkan content
                        |--------------------------------------------------------------------------
                        */

                        loading.classList.add(
                            'd-none'
                        );

                        content.classList.remove(
                            'd-none'
                        );
                    })

                    .catch(function (error) {

                        loading.classList.add(
                            'd-none'
                        );

                        content.classList.remove(
                            'd-none'
                        );

                        document
                            .getElementById(
                                'detailDepositDescription'
                            )
                            .innerHTML =
                            '<span class="text-danger">' +
                            error.message +
                            '</span>';
                    });

                }
            );

        });

});

</script>

@stop
