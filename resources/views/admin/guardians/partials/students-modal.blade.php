<div class="modal fade" id="guardianStudentsModal" tabindex="-1" aria-labelledby="guardianStudentsModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="guardianStudentsModalLabel">
                        <i class="bi bi-people me-1"></i>
                        Kelola Anak
                    </h5>

                    <div class="text-muted small">
                        {{ $guardian->name }}
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                {{-- Tambah Anak --}}
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            Tambah Anak
                        </h3>
                    </div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('admin.guardians.students.attach', $guardian) }}">

                            @csrf

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Siswa
                                    </label>

                                    <select name="student_id" class="form-select" required>

                                        <option value="">
                                            Pilih Siswa
                                        </option>

                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}">
                                                {{ $student->name }}
                                                @if ($student->nis)
                                                    — {{ $student->nis }}
                                                @endif
                                                — {{ $student->organization?->code ?? '-' }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        Hubungan
                                    </label>

                                    <select name="relationship" class="form-select" required>

                                        <option value="">
                                            Pilih Hubungan
                                        </option>

                                        <option value="Ayah">
                                            Ayah
                                        </option>

                                        <option value="Ibu">
                                            Ibu
                                        </option>

                                        <option value="Wali">
                                            Wali
                                        </option>

                                    </select>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label d-block">
                                        Utama
                                    </label>

                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_primary" value="1" class="form-check-input"
                                            id="guardian_primary">

                                        <label class="form-check-label" for="guardian_primary">
                                            Ya
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus me-1"></i>
                                Tambahkan
                            </button>

                        </form>

                    </div>
                </div>

                {{-- Anak yang sudah terhubung --}}
                <h6 class="mb-3">
                    Anak yang Terhubung
                </h6>

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>Organisasi</th>
                                <th>Hubungan</th>
                                <th class="text-center">
                                    Utama
                                </th>
                                <th class="text-center" width="80">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($guardian->students as $student)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        {{ $student->name }}
                                    </td>

                                    <td>
                                        {{ $student->nis ?: '-' }}
                                    </td>

                                    <td>
                                        {{ $student->organization?->code ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $student->pivot->relationship ?: '-' }}
                                    </td>

                                    <td class="text-center">

                                        @if ($student->pivot->is_primary)
                                            <span class="badge bg-success">
                                                Utama
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                -
                                            </span>
                                        @endif

                                    </td>

                                    @php
                                        $accessibleOrganizationIds = \App\Models\Organization::accessibleIdsForUser();
                                    @endphp
                                    <td class="text-center">

                                        @if ($accessibleOrganizationIds->contains($student->organization_id))
                                            <form method="POST"
                                                action="{{ route('admin.guardians.students.detach', [
                                                    'guardian' => $guardian,
                                                    'student' => $student,
                                                ]) }}"
                                                class="d-inline"
                                                onsubmit="return confirm(
                                                    'Hapus hubungan Wali dengan siswa ini?'
                                                )">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus hubungan">
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </form>
                                        @else
                                            <span class="text-muted" title="Tidak memiliki akses">
                                                <i class="bi bi-lock"></i>
                                            </span>
                                        @endif

                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada anak yang terhubung.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>

            </div>

        </div>
    </div>
</div>
