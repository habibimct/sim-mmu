@extends('adminlte::page')

@section('title', 'Wali / Orang Tua')

@section('content_header')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

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

            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#guardianImportModal">
                <i class="bi bi-file-earmark-excel me-1"></i>
                Import Excel
            </button>

            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                data-bs-target="#guardianFilterModal">
                <i class="bi bi-funnel me-1"></i>
                Filter
            </button>

            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#guardianCreateModal">
                <i class="bi bi-person-plus me-1"></i>
                Tambah Wali
            </button>

        </div>

    </div>

@stop

@section('content')

    @if (session('import_result'))
        @php
            $result = session('import_result');
        @endphp

        <div class="alert alert-info alert-dismissible fade show">
            <h5>
                <i class="bi bi-file-earmark-check me-1"></i>
                Hasil Import Wali
            </h5>

            <p class="mb-2">
                Berhasil: <strong>{{ $result['success'] }}</strong> data.
                Gagal: <strong>{{ count($result['errors']) }}</strong> data.
            </p>

            @if (count($result['errors']))
                <div class="table-responsive">
                    <table class="table table-sm table-bordered bg-white mb-0">
                        <thead>
                            <tr>
                                <th>Baris Excel</th>
                                <th>Alasan Gagal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result['errors'] as $error)
                                <tr>
                                    <td>{{ $error['row'] }}</td>
                                    <td>{{ $error['message'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                                    <span class="guardian-students-count"
                                        data-guardian-id="{{ $guardian->id }}">{{ $guardian->students_count }}</span>
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

    {{-- Modal Import Wali --}}
    @include('admin.guardians.partials.import-modal')

    {{-- Modal Filter --}}
    @include('admin.guardians.partials.filter')

    {{-- Modal Tambah Wali --}}
    @include('admin.guardians.partials.create-modal')

    @include('admin.guardians.partials.edit-modal')

@stop


@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Edit wali
            document.querySelectorAll('.btn-edit-guardian')
                .forEach(function(button) {
                    button.addEventListener('click', function() {
                        const form = document.getElementById('guardianEditForm');

                        form.action = "{{ url('admin/guardians') }}/" + this.dataset.id;

                        document.getElementById('guardian_edit_organization').value =
                            this.dataset.organizationId;
                        document.getElementById('guardian_edit_name').value =
                            this.dataset.name;
                        document.getElementById('guardian_edit_nik').value =
                            this.dataset.nik;
                        document.getElementById('guardian_edit_phone').value =
                            this.dataset.phone || '';

                        bootstrap.Modal.getOrCreateInstance(
                            document.getElementById('guardianEditModal')
                        ).show();
                    });
                });

            let currentGuardianStudentsUrl = null;
            let isLoadingGuardianStudents = false;

            const container = document.getElementById(
                'guardianStudentsModalContainer'
            );

            function cleanupModalBackdrop() {
                // Jangan mengganggu modal lain yang masih terbuka.
                if (document.querySelector('.modal.show')) {
                    return;
                }

                document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                    backdrop.remove();
                });

                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }

            function closeExistingGuardianModal() {
                return new Promise(resolve => {
                    const modalElement = document.getElementById(
                        'guardianStudentsModal'
                    );

                    if (!modalElement || !modalElement.classList.contains('show')) {
                        cleanupModalBackdrop();
                        resolve();
                        return;
                    }

                    const modal = bootstrap.Modal.getInstance(modalElement);

                    modalElement.addEventListener('hidden.bs.modal', function() {
                        modal?.dispose();
                        cleanupModalBackdrop();
                        resolve();
                    }, {
                        once: true
                    });

                    if (modal) {
                        modal.hide();
                    } else {
                        bootstrap.Modal.getOrCreateInstance(modalElement).hide();
                    }
                });
            }

            async function loadGuardianStudents(showModal = true) {
                if (!currentGuardianStudentsUrl || isLoadingGuardianStudents) {
                    return;
                }

                isLoadingGuardianStudents = true;

                try {
                    const response = await fetch(currentGuardianStudentsUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        throw new Error(
                            response.status === 403 ?
                            'Anda tidak memiliki akses ke data wali ini.' :
                            'Gagal memuat data anak. Status: ' + response.status
                        );
                    }

                    const html = await response.text();

                    await closeExistingGuardianModal();

                    container.innerHTML = html;

                    const modalElement = document.getElementById(
                        'guardianStudentsModal'
                    );

                    if (!modalElement) {
                        throw new Error('Modal Kelola Anak tidak ditemukan.');
                    }

                    if (showModal) {
                        bootstrap.Modal.getOrCreateInstance(modalElement).show();
                    }
                } catch (error) {
                    console.error(error);
                    alert(error.message || 'Terjadi kesalahan saat memuat data anak.');
                } finally {
                    isLoadingGuardianStudents = false;
                }
            }

            // Buka modal Kelola Anak
            document.addEventListener('click', function(event) {
                const button = event.target.closest(
                    '.btn-manage-guardian-students'
                );

                if (!button) {
                    return;
                }

                event.preventDefault();

                currentGuardianStudentsUrl = button.dataset.url;
                loadGuardianStudents(true);
            });

            // Submit form tambah/lepas anak melalui AJAX
            document.addEventListener('submit', async function(event) {
                const form = event.target;

                if (!form.closest('#guardianStudentsModal')) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                if (form.dataset.submitting === 'true') {
                    return;
                }

                form.dataset.submitting = 'true';

                const submitButtons = form.querySelectorAll(
                    'button[type="submit"], input[type="submit"]'
                );

                submitButtons.forEach(button => button.disabled = true);

                try {
                    const response = await fetch(form.action, {
                        method: (form.method || 'POST').toUpperCase(),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: new FormData(form),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message || 'Gagal menyimpan hubungan wali dan anak.'
                        );
                    }

                    // Muat ulang daftar anak dan perbarui angka pada tabel utama.
                    await loadGuardianStudents(true);

                    try {
                        const response = await fetch(currentGuardianStudentsUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (response.ok) {
                            // Jumlah anak diperbarui melalui data yang dikirim server.
                            const html = await response.text();
                            const doc = new DOMParser().parseFromString(html, 'text/html');

                            const countElement = doc.querySelector(
                                '[data-linked-students-count]'
                            );

                            if (countElement) {
                                const guardianId = currentGuardianStudentsUrl.match(/guardians\/(\d+)/)
                                    ?.[1];

                                const tableCount = document.querySelector(
                                    `.guardian-students-count[data-guardian-id="${guardianId}"]`
                                );

                                if (tableCount) {
                                    tableCount.textContent = countElement.dataset.linkedStudentsCount;
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Gagal memperbarui jumlah anak:', error);
                    }
                } catch (error) {
                    console.error(error);
                    alert(error.message || 'Terjadi kesalahan saat menyimpan data.');
                } finally {
                    form.dataset.submitting = 'false';
                    submitButtons.forEach(button => button.disabled = false);
                }
            }, true);

            // Tangani penutupan modal Kelola Anak saja.
            document.addEventListener('hidden.bs.modal', function(event) {
                if (event.target.id !== 'guardianStudentsModal') {
                    return;
                }

                cleanupModalBackdrop();
            });
        });
    </script>
@endsection
