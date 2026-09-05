@can('classes.manage')

    <div class="modal fade"
        id="modalTambahKelas"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form
                    action="{{ route('admin.school-classes.store') }}"
                    method="POST">

                    @csrf

                    <input type="hidden"
                        name="modal_target"
                        value="modalTambahKelas">

                    <div class="modal-header">

                        <h5 class="modal-title">

                            <i class="bi bi-plus-lg me-1"></i>
                            Tambah Kelas

                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body">

                        {{-- Unit --}}
                        <div class="mb-3">

                            <label class="form-label">

                                Unit
                                <span class="text-danger">*</span>

                            </label>

                            <select
                                name="organization_id"
                                class="form-select @error('organization_id') is-invalid @enderror"
                                required>

                                <option value="">
                                    -- Pilih Unit --
                                </option>

                                @foreach ($organizations as $organization)

                                    <option
                                        value="{{ $organization->id }}"
                                        @selected(
                                            old('organization_id') == $organization->id
                                        )>

                                        {{ $organization->name }}

                                    </option>

                                @endforeach

                            </select>

                            @error('organization_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Tahun Ajaran --}}
                        <div class="mb-3">

                            <label class="form-label">

                                Tahun Ajaran
                                <span class="text-danger">*</span>

                            </label>

                            <select
                                name="academic_year_id"
                                class="form-select @error('academic_year_id') is-invalid @enderror"
                                required>

                                <option value="">
                                    -- Pilih Tahun Ajaran --
                                </option>

                                @foreach ($academicYears as $academicYear)

                                    @if ($academicYear->is_active)

                                        <option
                                            value="{{ $academicYear->id }}"
                                            @selected(
                                                old('academic_year_id') == $academicYear->id
                                            )>

                                            {{ $academicYear->name }}

                                        </option>

                                    @endif

                                @endforeach

                            </select>

                            @error('academic_year_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Tingkat --}}
                        <div class="mb-3">

                            <label
                                for="level"
                                class="form-label">

                                Tingkat
                                <span class="text-danger"></span>

                            </label>

                            <input
                                type="number"
                                name="level"
                                id="level"
                                class="form-control @error('level') is-invalid @enderror"
                                value="{{ old('level') }}"
                                min="1"
                                max="20"
                                placeholder="Contoh: 1">

                            @error('level')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Nama --}}
                        <div class="mb-3">

                            <label class="form-label">

                                Nama Kelas
                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                placeholder="Contoh: 1A"
                                maxlength="100"
                                required>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Status --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Status Kelas
                            </label>

                            <select
                                name="is_active"
                                class="form-select @error('is_active') is-invalid @enderror">

                                <option
                                    value="1"
                                    @selected(old('is_active', '1') == '1')>

                                    Aktif

                                </option>

                                <option
                                    value="0"
                                    @selected(old('is_active') === '0')>

                                    Nonaktif

                                </option>

                            </select>

                            @error('is_active')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Batal

                        </button>

                        <button type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-save me-1"></i>
                            Simpan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endcan
