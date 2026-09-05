<div class="modal fade"
    id="modalEditKelas{{ $schoolClass->id }}"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form action="{{ route('admin.school-classes.update', $schoolClass) }}"
                method="POST">

                @csrf
                @method('PUT')

                <input type="hidden"
                    name="modal_target"
                    value="modalEditKelas{{ $schoolClass->id }}">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-pencil me-1"></i>
                        Edit Kelas

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

                            @foreach ($organizations as $organization)

                                <option
                                    value="{{ $organization->id }}"
                                    @selected(
                                        $schoolClass->organization_id == $organization->id
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

                            @foreach ($academicYears as $academicYear)

                                @if ($academicYear->is_active)

                                    <option
                                        value="{{ $academicYear->id }}"
                                        @selected(
                                            $schoolClass->academic_year_id == $academicYear->id
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
                            for="edit_level_{{ $schoolClass->id }}"
                            class="form-label">

                            Tingkat
                            <span class="text-danger"></span>

                        </label>

                        <input
                            type="number"
                            name="level"
                            id="edit_level_{{ $schoolClass->id }}"
                            class="form-control @error('level') is-invalid @enderror"
                            value="{{ old('level', $schoolClass->level) }}"
                            min="1"
                            max="20">

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
                            value="{{ old('name', $schoolClass->name) }}"
                            maxlength="100"
                            required>

                        @error('name')
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
                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
