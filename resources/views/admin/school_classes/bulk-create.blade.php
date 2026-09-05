@extends('adminlte::page')

@section('title', 'Buat Banyak Kelas')

@section('content_header')
    <h1>Buat Banyak Kelas</h1>
@stop

@section('content')

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Buat Banyak Kelas
            </h3>
        </div>

        <form
            method="POST"
            action="{{ route('admin.school-classes.bulk-store') }}"
        >

            @csrf

            <div class="card-body">

                {{-- Error --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>
                            Periksa kembali data berikut:
                        </strong>

                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Tahun Ajaran --}}
                <div class="mb-3">

                    <label class="form-label">
                        Tahun Ajaran
                    </label>

                    <select
                        name="academic_year_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Tahun Ajaran --
                        </option>

                        @foreach ($academicYears as $academicYear)

                            <option
                                value="{{ $academicYear->id }}"
                                @selected(
                                    old('academic_year_id')
                                    == $academicYear->id
                                )
                            >
                                {{ $academicYear->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Unit --}}
                <div class="mb-3">

                    <label class="form-label">
                        Unit
                    </label>

                    <select
                        name="organization_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Unit --
                        </option>

                        @foreach ($organizations as $organization)

                            <option
                                value="{{ $organization->id }}"
                                @selected(
                                    old('organization_id')
                                    == $organization->id
                                )
                            >
                                {{ $organization->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="row">

                    {{-- Tingkat awal --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Tingkat Awal
                        </label>

                        <select
                            name="from_level"
                            id="from_level"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih --
                            </option>

                            @for ($level = 1; $level <= 12; $level++)

                                <option
                                    value="{{ $level }}"
                                    @selected(
                                        old('from_level')
                                        == $level
                                    )
                                >
                                    Tingkat {{ $level }}
                                </option>

                            @endfor

                        </select>

                    </div>

                    {{-- Tingkat akhir --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Tingkat Akhir
                        </label>

                        <select
                            name="to_level"
                            id="to_level"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih --
                            </option>

                            @for ($level = 1; $level <= 12; $level++)

                                <option
                                    value="{{ $level }}"
                                    @selected(
                                        old('to_level')
                                        == $level
                                    )
                                >
                                    Tingkat {{ $level }}
                                </option>

                            @endfor

                        </select>

                    </div>

                </div>

                {{-- Jumlah kelas --}}
                <div id="level-container"></div>

                <div
                    id="preview"
                    class="alert alert-info d-none mt-3"
                >
                    <strong>
                        Preview kelas:
                    </strong>

                    <div
                        id="preview-list"
                        class="mt-2"
                    ></div>
                </div>

            </div>

            <div class="card-footer">

                <a
                    href="{{ route('admin.school-classes.index') }}"
                    class="btn btn-secondary"
                >
                    Kembali
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="fas fa-save"></i>
                    Buat Kelas
                </button>

            </div>

        </form>

    </div>

@stop


@section('js')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const fromLevel =
            document.getElementById(
                'from_level'
            );

        const toLevel =
            document.getElementById(
                'to_level'
            );

        const container =
            document.getElementById(
                'level-container'
            );

        const preview =
            document.getElementById(
                'preview'
            );

        const previewList =
            document.getElementById(
                'preview-list'
            );

        function generateLevels() {

            container.innerHTML = '';

            preview.classList.add(
                'd-none'
            );

            previewList.innerHTML = '';

            const from =
                parseInt(
                    fromLevel.value
                );

            const to =
                parseInt(
                    toLevel.value
                );

            if (
                !from ||
                !to ||
                from > to
            ) {
                return;
            }

            for (
                let level = from;
                level <= to;
                level++
            ) {

                const row =
                    document.createElement(
                        'div'
                    );

                row.className =
                    'row align-items-end mb-3';

                row.innerHTML = `
                    <div class="col-md-6">
                        <label class="form-label">
                            Tingkat ${level}
                        </label>

                        <input
                            type="number"
                            name="class_counts[${level}]"
                            class="form-control class-count"
                            data-level="${level}"
                            min="1"
                            max="26"
                            value="1"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted preview-level">
                            Tingkat ${level}: 1A
                        </div>
                    </div>
                `;

                container.appendChild(
                    row
                );
            }

            updatePreview();
        }

        function updatePreview() {

            const inputs =
                document.querySelectorAll(
                    '.class-count'
                );

            previewList.innerHTML = '';

            let hasData = false;

            inputs.forEach(
                function (input) {

                    const level =
                        parseInt(
                            input.dataset.level
                        );

                    const count =
                        parseInt(
                            input.value
                        );

                    if (
                        !count ||
                        count < 1
                    ) {
                        return;
                    }

                    const names = [];

                    for (
                        let i = 0;
                        i < count;
                        i++
                    ) {

                        names.push(
                            level +
                            String.fromCharCode(
                                65 + i
                            )
                        );
                    }

                    const div =
                        document.createElement(
                            'div'
                        );

                    div.innerHTML =
                        `<strong>
                            Tingkat ${level}:
                        </strong>
                        ${names.join(', ')}`;

                    previewList.appendChild(
                        div
                    );

                    hasData = true;
                }
            );

            if (hasData) {
                preview.classList.remove(
                    'd-none'
                );
            }
        }

        fromLevel.addEventListener(
            'change',
            generateLevels
        );

        toLevel.addEventListener(
            'change',
            generateLevels
        );

        document.addEventListener(
            'input',
            function (event) {

                if (
                    event.target.classList.contains(
                        'class-count'
                    )
                ) {
                    updatePreview();
                }

            }
        );

        generateLevels();

    }
);

</script>

@stop
