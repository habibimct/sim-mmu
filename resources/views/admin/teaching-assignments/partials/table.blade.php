<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            Daftar Penugasan Mengajar

        </h3>

        <div class="card-tools">

            <span class="badge text-bg-secondary">

                {{ $teachingAssignments->total() }} Penugasan

            </span>

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-striped mb-0">

                <thead>

                    <tr>

                        <th width="60">
                            #
                        </th>

                        <th>
                            Guru
                        </th>

                        <th>
                            Kelas
                        </th>

                        <th>
                            Mata Pelajaran
                        </th>

                        <th width="120">
                            Status
                        </th>

                        <th
                            width="150"
                            class="text-center"
                        >
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse (
                        $teachingAssignments
                        as $assignment
                    )

                        <tr>

                            <td>

                                {{ $teachingAssignments->firstItem() + $loop->index }}

                            </td>


                            {{-- Guru --}}

                            <td>

                                <div class="fw-semibold">

                                    {{ $assignment->teacher->name }}

                                </div>

                                @if ($assignment->teacher->nik)

                                    <small class="text-muted">

                                        NIK:
                                        {{ $assignment->teacher->nik }}

                                    </small>

                                @endif

                            </td>


                            {{-- Kelas --}}

                            <td>

                                <div class="fw-semibold">

                                    {{ $assignment->schoolClass->name }}

                                </div>

                                @if ($assignment->schoolClass->academicYear)

                                    <small class="text-muted">

                                        {{ $assignment->schoolClass->academicYear->name }}

                                    </small>

                                @endif

                            </td>


                            {{-- Mata Pelajaran --}}

                            <td>

                                <div class="fw-semibold">

                                    {{ $assignment->subject->name }}

                                </div>

                                @if (!empty($assignment->subject->code))

                                    <small class="text-muted">

                                        {{ $assignment->subject->code }}

                                    </small>

                                @endif

                            </td>


                            {{-- Status --}}

                            <td>

                                @if ($assignment->is_active)

                                    <span class="badge text-bg-success">

                                        Aktif

                                    </span>

                                @else

                                    <span class="badge text-bg-secondary">

                                        Tidak Aktif

                                    </span>

                                @endif

                            </td>


                            {{-- Aksi --}}

                            <td class="text-center">

                                <div class="btn-group">

                                    {{-- Edit --}}

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditTeachingAssignment"
                                        data-id="{{ $assignment->id }}"
                                        data-teacher="{{ $assignment->teacher_id }}"
                                        data-class="{{ $assignment->school_class_id }}"
                                        data-subject="{{ $assignment->subject_id }}"
                                        data-active="{{ $assignment->is_active ? '1' : '0' }}"
                                        title="Edit"
                                    >

                                        <i class="bi bi-pencil"></i>

                                    </button>


                                    {{-- Hapus --}}

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDeleteTeachingAssignment"
                                        data-id="{{ $assignment->id }}"
                                        data-label="{{ $assignment->teacher->name }} - {{ $assignment->schoolClass->name }} - {{ $assignment->subject->name }}"
                                        title="Hapus"
                                    >

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center text-muted py-5"
                            >

                                <i class="bi bi-person-workspace fs-2"></i>

                                <div class="mt-2">

                                    Belum ada penugasan mengajar.

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    @if ($teachingAssignments->hasPages())
        <div class="card-footer d-flex justify-content-end align-items-center">

            <div>
                {{ $teachingAssignments->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    @endif

</div>
