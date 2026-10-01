@extends('adminlte::page')

@section('title', 'Wali / Orang Tua')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1 class="mb-0">
                <i class="bi bi-people me-1"></i>
                Wali / Orang Tua
            </h1>

            <p class="text-muted mb-0">
                Data wali / orang tua siswa
            </p>
        </div>

        <div class="d-flex gap-2">

            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                data-bs-target="#guardianFilterModal">

                <i class="bi bi-funnel me-1"></i>
                Filter

            </button>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#guardianCreateModal">

                <i class="bi bi-person-plus me-1"></i>
                Tambah Wali

            </button>

        </div>

    </div>

@stop

@section('content')

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                Daftar Wali
            </h3>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead>

                        <tr>
                            <th width="60">No</th>
                            <th>Nama Wali</th>
                            <th>NIK</th>
                            <th>No. HP</th>
                            <th>Password</th>
                            <th>Unit</th>
                            <th class="text-center">Anak</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($guardians as $guardian)
                            <tr>

                                <td>
                                    {{ $guardians->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    {{ $guardian->name }}
                                </td>

                                <td>
                                    {{ $guardian->nik ?: '-' }}
                                </td>

                                <td>
                                    {{ $guardian->phone ?: '-' }}
                                </td>

                                <td>
                                    {{ $guardian->initial_password ?: '-' }}
                                </td>

                                <td>
                                    @php
                                        $guardianOrganizations = $guardian->students
                                            ->pluck('organization')
                                            ->filter()
                                            ->unique('id')
                                            ->sortBy('name');
                                    @endphp

                                    @forelse ($guardianOrganizations as $organization)
                                        <span class="badge bg-light text-dark border me-1 mb-1">
                                            {{ $organization->code }}
                                        </span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>

                                <td class="text-center">
                                    {{ $guardian->students_count }}
                                </td>

                                <td class="text-center">

                                    @if ($guardian->is_active)
                                        <span class="badge bg-success">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Nonaktif
                                        </span>
                                    @endif

                                </td>

                                <td class="text-center">

                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-guardian"
                                        title="Edit" data-id="{{ $guardian->id }}"
                                        data-organization-id="{{ $guardian->organization_id }}"
                                        data-name="{{ $guardian->name }}" data-nik="{{ $guardian->nik }}"
                                        data-phone="{{ $guardian->phone }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <form method="POST"
                                        action="{{ route('admin.guardians.generate-password', $guardian) }}"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                            'Generate password baru untuk wali ini?'
                                        )">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="btn btn-sm btn-outline-warning"
                                            title="Generate Password Baru">
                                            <i class="bi bi-key"></i>
                                        </button>

                                    </form>

                                    <form method="POST" action="{{ route('admin.guardians.toggle-status', $guardian) }}"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                            '{{ $guardian->is_active ? 'Nonaktifkan wali ini?' : 'Aktifkan kembali wali ini?' }}'
                                        )">

                                        @csrf
                                        @method('PATCH')

                                        @if ($guardian->is_active)
                                            <button type="submit" class="btn btn-sm btn-outline-warning"
                                                title="Nonaktifkan">
                                                <i class="bi bi-person-x"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Aktifkan">
                                                <i class="bi bi-person-check"></i>
                                            </button>
                                        @endif

                                    </form>

                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary btn-manage-guardian-students"
                                        title="Kelola Anak" data-url="{{ route('admin.guardians.students', $guardian) }}">
                                        <i class="bi bi-people"></i>
                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9" class="text-center text-muted py-4">

                                    Belum ada data wali.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">

                {{ $guardians->links() }}

            </div>

        </div>

    </div>

    <div id="guardianStudentsModalContainer"></div>

    {{-- Modal Filter --}}
    @include('admin.guardians.partials.filter')

    {{-- Modal Tambah Wali --}}
    @include('admin.guardians.partials.create-modal')

    @include('admin.guardians.partials.edit-modal')

@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.btn-edit-guardian')
                .forEach(function(button) {

                    button.addEventListener('click', function() {

                        const form =
                            document.getElementById('guardianEditForm');

                        form.action =
                            "{{ url('admin/guardians') }}/" +
                            this.dataset.id;

                        document.getElementById(
                            'guardian_edit_organization'
                        ).value = this.dataset.organizationId;

                        document.getElementById(
                            'guardian_edit_name'
                        ).value = this.dataset.name;

                        document.getElementById(
                            'guardian_edit_nik'
                        ).value = this.dataset.nik;

                        document.getElementById(
                            'guardian_edit_phone'
                        ).value = this.dataset.phone || '';

                        const modalElement =
                            document.getElementById('guardianEditModal');

                        const modal =
                            bootstrap.Modal.getOrCreateInstance(
                                modalElement
                            );

                        modal.show();
                    });

                });

        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let currentGuardianStudentsUrl = null;

            // =====================================================
            // Buka modal Kelola Anak
            // =====================================================

            document.addEventListener('click', function(event) {

                const button = event.target.closest(
                    '.btn-manage-guardian-students'
                );

                if (!button) {
                    return;
                }

                event.preventDefault();

                currentGuardianStudentsUrl =
                    button.dataset.url;

                loadGuardianStudents(true);
            });


            // =====================================================
            // Load / reload isi modal
            // =====================================================

            function loadGuardianStudents(showModal = true) {

                if (!currentGuardianStudentsUrl) {
                    return;
                }

                fetch(currentGuardianStudentsUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(response => {

                        if (!response.ok) {
                            throw new Error(
                                'Gagal memuat data anak.'
                            );
                        }

                        return response.text();
                    })
                    .then(html => {

                        const container =
                            document.getElementById(
                                'guardianStudentsModalContainer'
                            );

                        container.innerHTML = html;

                        const modalElement =
                            document.getElementById(
                                'guardianStudentsModal'
                            );

                        if (!modalElement) {
                            throw new Error(
                                'Modal Kelola Anak tidak ditemukan.'
                            );
                        }

                        const modal =
                            bootstrap.Modal.getOrCreateInstance(
                                modalElement
                            );

                        if (showModal) {
                            modal.show();
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        alert(error.message);
                    });
            }


            // =====================================================
            // Submit form di dalam modal
            // =====================================================

            document.addEventListener('submit', function(event) {

                const form = event.target;

                if (!form.closest('#guardianStudentsModal')) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                const formData = new FormData(form);

                fetch(form.action, {
                        method: form.method.toUpperCase(),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                    .then(async response => {

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(
                                data.message ||
                                'Terjadi kesalahan.'
                            );
                        }

                        return data;
                    })
                    .then(data => {

                        /*
                         * Reload isi modal.
                         *
                         * showModal = true
                         * supaya modal tetap terbuka.
                         */
                        loadGuardianStudents(true);

                    })
                    .catch(error => {

                        console.error(error);
                        alert(error.message);

                    });

            }, true);

        });
    </script>
@endsection
