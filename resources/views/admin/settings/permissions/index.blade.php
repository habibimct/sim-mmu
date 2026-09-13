@extends('adminlte::page')

@section('title', 'Pengaturan Permission')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <h1 class="m-0">
            Pengaturan Permission
        </h1>

    </div>

@stop


@section('content')

    {{-- ========================================================= --}}
    {{-- PESAN SUKSES --}}
    {{-- ========================================================= --}}

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">

            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- PESAN ERROR --}}
    {{-- ========================================================= --}}

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- VALIDATION ERROR --}}
    {{-- ========================================================= --}}

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            <i class="bi bi-exclamation-triangle me-1"></i>

            <strong>Terdapat kesalahan:</strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- PILIH ROLE --}}
    {{-- ========================================================= --}}

    <div class="card mb-3">

        <div class="card-header">

            <h3 class="card-title">

                <i class="bi bi-person-badge me-1"></i>
                Pilih Role

            </h3>

        </div>


        <div class="card-body">

            @if ($roles->isEmpty())

                <div class="alert alert-warning mb-0">

                    <i class="bi bi-exclamation-triangle me-1"></i>

                    Belum ada role aktif yang tersedia.

                </div>

            @else

                <form method="GET" action="{{ route('admin.settings.permissions.index') }}">

                    <div class="row align-items-end">

                        <div class="col-md-8">

                            <label for="role_id" class="form-label">

                                Role
                                <span class="text-danger">*</span>

                            </label>

                            <select
                                name="role_id"
                                id="role_id"
                                class="form-select"
                                onchange="this.form.submit()"
                            >

                                @foreach ($roles as $role)

                                    <option
                                        value="{{ $role->id }}"
                                        @selected($selectedRole?->id === $role->id)
                                    >
                                        {{ $role->name }}
                                        ({{ $role->code }})
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-4">

                            <div class="text-muted small">

                                <i class="bi bi-info-circle me-1"></i>

                                Permission diatur berdasarkan role.

                            </div>

                        </div>

                    </div>

                </form>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- PERMISSION --}}
    {{-- ========================================================= --}}

    @if ($selectedRole)

        <form
            method="POST"
            action="{{ route('admin.settings.permissions.update') }}"
        >

            @csrf
            @method('PUT')

            <input
                type="hidden"
                name="role_id"
                value="{{ $selectedRole->id }}"
            >


            <div class="card">

                <div class="card-header">

                    <div class="d-flex justify-content-between align-items-center">

                        <h3 class="card-title mb-0">

                            <i class="bi bi-shield-check me-1"></i>

                            Hak Akses:
                            <strong>{{ $selectedRole->name }}</strong>

                        </h3>


                        <div class="form-check">

                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="checkAll"
                            >

                            <label
                                class="form-check-label fw-semibold"
                                for="checkAll"
                            >
                                Pilih Semua
                            </label>

                        </div>

                    </div>

                </div>


                <div class="card-body">


                    @if ($permissions->isEmpty())

                        <div class="alert alert-warning mb-0">

                            <i class="bi bi-exclamation-triangle me-1"></i>

                            Belum ada permission yang tersedia.

                        </div>

                    @else

                        <div class="row">

                            @foreach ($permissions as $module => $modulePermissions)

                                <div class="col-lg-3 mb-2">

                                    <div class="card card-outline card-primary h-100">

                                        <div class="card-header">

                                            <h3 class="card-title fw-semibold">

                                                <i class="bi bi-folder2-open me-1"></i>

                                                {{ ucfirst(str_replace('_', ' ', $module ?: 'Lainnya')) }}

                                            </h3>


                                            <div class="card-tools">

                                                <div class="form-check">

                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input module-check-all"
                                                        data-module="{{ $loop->index }}"
                                                        id="moduleCheck{{ $loop->index }}"
                                                    >

                                                    <label
                                                        class="form-check-label small"
                                                        for="moduleCheck{{ $loop->index }}"
                                                    >
                                                        Pilih Semua
                                                    </label>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="card-body">

                                            @foreach ($modulePermissions as $permission)

                                                <div class="form-check mb-3">

                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input permission-checkbox module-{{ $loop->parent->index }}"
                                                        name="permissions[]"
                                                        value="{{ $permission->id }}"
                                                        id="permission{{ $permission->id }}"
                                                        data-module="{{ $loop->parent->index }}"
                                                        @checked(in_array($permission->id, $selectedPermissionIds))
                                                    >

                                                    <label
                                                        class="form-check-label"
                                                        for="permission{{ $permission->id }}"
                                                    >

                                                        <span class="fw-semibold">
                                                            {{ $permission->code }}
                                                        </span>

                                                        <br>

                                                        <small class="text-muted">
                                                            {{ $permission->name }}
                                                        </small>

                                                    </label>

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @endif

                </div>


                @if ($permissions->isNotEmpty())

                    <div class="card-footer text-end">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <i class="bi bi-save me-1"></i>

                            Simpan Permission

                        </button>

                    </div>

                @endif

            </div>

        </form>

    @endif

@stop


@push('js')

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const checkAll = document.getElementById('checkAll');

            const permissionCheckboxes = document.querySelectorAll(
                '.permission-checkbox'
            );

            const moduleCheckAlls = document.querySelectorAll(
                '.module-check-all'
            );


            /*
             * =====================================================
             * PILIH SEMUA
             * =====================================================
             */

            if (checkAll) {

                checkAll.addEventListener('change', function () {

                    permissionCheckboxes.forEach(function (checkbox) {

                        checkbox.checked = checkAll.checked;

                    });


                    moduleCheckAlls.forEach(function (checkbox) {

                        checkbox.checked = checkAll.checked;

                    });

                });

            }


            /*
             * =====================================================
             * PILIH SEMUA PER MODULE
             * =====================================================
             */

            moduleCheckAlls.forEach(function (moduleCheckbox) {

                moduleCheckbox.addEventListener('change', function () {

                    const moduleIndex = this.dataset.module;

                    const modulePermissions = document.querySelectorAll(
                        '.module-' + moduleIndex
                    );

                    modulePermissions.forEach(function (checkbox) {

                        checkbox.checked = moduleCheckbox.checked;

                    });

                    updateMasterCheckbox();

                });

            });


            /*
             * =====================================================
             * UPDATE STATUS MASTER CHECKBOX
             * =====================================================
             */

            permissionCheckboxes.forEach(function (checkbox) {

                checkbox.addEventListener('change', function () {

                    updateModuleCheckbox(this.dataset.module);

                    updateMasterCheckbox();

                });

            });


            function updateModuleCheckbox(moduleIndex) {

                const moduleCheckbox = document.querySelector(
                    '.module-check-all[data-module="' + moduleIndex + '"]'
                );

                const modulePermissions = document.querySelectorAll(
                    '.module-' + moduleIndex
                );

                if (!moduleCheckbox || modulePermissions.length === 0) {
                    return;
                }

                const checkedCount = Array.from(modulePermissions)
                    .filter(function (checkbox) {
                        return checkbox.checked;
                    })
                    .length;

                moduleCheckbox.checked =
                    checkedCount === modulePermissions.length;

                moduleCheckbox.indeterminate =
                    checkedCount > 0 &&
                    checkedCount < modulePermissions.length;
            }


            function updateMasterCheckbox() {

                if (!checkAll || permissionCheckboxes.length === 0) {
                    return;
                }

                const checkedCount = Array.from(permissionCheckboxes)
                    .filter(function (checkbox) {
                        return checkbox.checked;
                    })
                    .length;

                checkAll.checked =
                    checkedCount === permissionCheckboxes.length;

                checkAll.indeterminate =
                    checkedCount > 0 &&
                    checkedCount < permissionCheckboxes.length;
            }


            /*
             * =====================================================
             * INITIAL STATE
             * =====================================================
             */

            moduleCheckAlls.forEach(function (moduleCheckbox) {

                updateModuleCheckbox(moduleCheckbox.dataset.module);

            });

            updateMasterCheckbox();

        });

    </script>

@endpush
