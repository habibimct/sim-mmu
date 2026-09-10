<div class="card card-primary">

    <div class="card-header">

        <h3 class="card-title">

            <i class="bi bi-funnel me-1"></i>

            Filter Laporan

        </h3>

    </div>


    <div class="card-body">

        <form
            method="GET"
            action="{{ route('admin.reports.teachers.index') }}"
        >

            <div class="row">

                {{-- UNIT --}}

                <div class="col-md-4 mb-3">

                    <label for="organization_id">
                        Unit
                    </label>

                    <select
                        name="organization_id"
                        id="organization_id"
                        class="form-control"
                    >

                        <option value="">
                            Semua Unit
                        </option>

                        @foreach ($organizations as $organization)

                            <option
                                value="{{ $organization->id }}"
                                @selected(
                                    $organizationId == $organization->id
                                )
                            >
                                {{ $organization->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- STATUS --}}

                <div class="col-md-4 mb-3">

                    <label for="status">
                        Status Guru
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="form-control"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option
                            value="active"
                            @selected($status === 'active')
                        >
                            Aktif
                        </option>

                        <option
                            value="inactive"
                            @selected($status === 'inactive')
                        >
                            Tidak Aktif
                        </option>

                    </select>

                </div>


                {{-- JENIS KELAMIN --}}

                <div class="col-md-4 mb-3">

                    <label for="gender">
                        Jenis Kelamin
                    </label>

                    <select
                        name="gender"
                        id="gender"
                        class="form-control"
                    >

                        <option value="">
                            Semua Jenis Kelamin
                        </option>

                        <option
                            value="male"
                            @selected($gender === 'male')
                        >
                            Laki-laki
                        </option>

                        <option
                            value="female"
                            @selected($gender === 'female')
                        >
                            Perempuan
                        </option>

                    </select>

                </div>

            </div>


            <div class="d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="bi bi-search me-1"></i>

                    Tampilkan

                </button>


                <a
                    href="{{ route('admin.reports.teachers.index') }}"
                    class="btn btn-secondary"
                >

                    <i class="bi bi-arrow-counterclockwise me-1"></i>

                    Reset

                </a>

            </div>

        </form>

    </div>

</div>
