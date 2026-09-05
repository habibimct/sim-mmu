<div class="card mb-3">

    <div class="card-body">

        @if ($studentId !== 'all')

            @php
                $student = \App\Models\Student::find($studentId);
            @endphp

            @if ($student)

                <div class="row align-items-center">

                    <div class="col-md-8">

                        <h5 class="mb-1">
                            {{ $student->name }}
                        </h5>

                        <div class="text-muted small">
                            NIS : {{ $student->nis }}
                        </div>

                    </div>

                    <div class="col-md-4 text-md-end mt-3 mt-md-0">

                        <span class="badge bg-primary">
                            {{ $weeklyAttendances->count() }} Pertemuan
                        </span>

                    </div>

                </div>

            @endif

        @else

            <div class="text-center text-muted py-2">

                Pilih siswa melalui tombol **Filter** untuk melihat rekap absensi.

            </div>

        @endif

    </div>

</div>
