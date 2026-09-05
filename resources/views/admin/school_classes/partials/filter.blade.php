<form method="GET"
    action="{{ route('admin.school-classes.index') }}"
    class="row g-2 mb-3">

    {{-- Pencarian --}}
    <div class="col-md-3">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="form-control"
            placeholder="Cari nama kelas...">

    </div>


    {{-- Tahun Ajaran --}}
    <div class="col-md-3">

        <select
            name="academic_year_id"
            class="form-select">

            <option value="">
                Semua Tahun Ajaran
            </option>

            @foreach ($academicYears as $academicYear)

                <option
                    value="{{ $academicYear->id }}"
                    @selected(
                        request('academic_year_id') == $academicYear->id
                    )>

                    {{ $academicYear->name }}

                    @if (!$academicYear->is_active)
                        (Ditutup)
                    @endif

                </option>

            @endforeach

        </select>

    </div>


    {{-- Unit --}}
    <div class="col-md-2">

        <select
            name="organization_id"
            class="form-select">

            <option value="">
                Semua Unit
            </option>

            @foreach ($organizations as $organization)

                <option
                    value="{{ $organization->id }}"
                    @selected(
                        request('organization_id') == $organization->id
                    )>

                    {{ $organization->name }}

                </option>

            @endforeach

        </select>

    </div>


    {{-- Status --}}
    <div class="col-md-2">

        <select
            name="status"
            class="form-select">

            <option value="">
                Semua Status
            </option>

            <option
                value="active"
                @selected(request('status') === 'active')>

                Aktif

            </option>

            <option
                value="inactive"
                @selected(request('status') === 'inactive')>

                Nonaktif

            </option>

        </select>

    </div>


    {{-- Tombol --}}
    <div class="col-md-2 d-flex gap-2">

        <button
            type="submit"
            class="btn btn-primary">

            <i class="bi bi-search"></i>
            Cari

        </button>

        <a
            href="{{ route('admin.school-classes.index') }}"
            class="btn btn-secondary">

            Reset

        </a>

    </div>

</form>
