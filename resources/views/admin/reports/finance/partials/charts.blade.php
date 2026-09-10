<div class="row">

    {{-- Grafik Tren --}}
    <div class="col-md-8">

        <div class="card card-outline card-primary">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    Perkembangan Keuangan
                </h3>

            </div>

            <div class="card-body">

                <div
                    style="height: 350px;"
                >
                    <canvas
                        id="financeTrendChart"
                    ></canvas>
                </div>

            </div>

        </div>

    </div>


    {{-- Informasi periode --}}
    <div class="col-md-4">

        <div class="card card-outline card-secondary">

            <div class="card-header">

                <h3 class="card-title">
                    Periode Laporan
                </h3>

            </div>

            <div class="card-body">

                <div class="mb-3">

                    <small class="text-muted">
                        Dari
                    </small>

                    <div class="font-weight-bold">
                        {{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }}
                    </div>

                </div>


                <div class="mb-3">

                    <small class="text-muted">
                        Sampai
                    </small>

                    <div class="font-weight-bold">
                        {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}
                    </div>

                </div>


                <div>

                    <small class="text-muted">
                        Periode Grafik
                    </small>

                    <div>

                        @if ($chartPeriodType === 'daily')

                            <span class="badge badge-primary">
                                Harian
                            </span>

                        @else

                            <span class="badge badge-info">
                                Bulanan
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- Grafik per Unit --}}
<div class="row">

    <div class="col-12">

        <div class="card card-outline card-primary">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Perbandingan Keuangan per Unit
                </h3>

            </div>

            <div class="card-body">

                <div style="height: 400px;">

                    <canvas
                        id="financeOrganizationChart"
                    ></canvas>

                </div>

            </div>

        </div>

    </div>

</div>
