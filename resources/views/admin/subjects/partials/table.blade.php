<div class="card">

    <div class="card-header">

        <h3 class="card-title">
            Daftar Mata Pelajaran
        </h3>

        <div class="card-tools">

            <span class="text-muted small">

                {{ $subjects->total() }} mata pelajaran

            </span>

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-striped mb-0">

                <thead>

                    <tr>

                        <th style="width: 70px;">
                            ID
                        </th>

                        <th style="width: 140px;">
                            Kode
                        </th>

                        <th>
                            Mata Pelajaran
                        </th>

                        <th style="width: 120px;">
                            Status
                        </th>

                        <th
                            class="text-end"
                            style="width: 100px;"
                        >
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($subjects as $subject)

                        <tr>

                            <td>
                                {{ $subject->id }}
                            </td>


                            <td>

                                <span class="fw-semibold">
                                    {{ $subject->code }}
                                </span>

                            </td>


                            <td>
                                {{ $subject->name }}
                            </td>


                            <td>

                                @if ($subject->is_active)

                                    <span class="badge text-bg-success">
                                        Aktif
                                    </span>

                                @else

                                    <span class="badge text-bg-secondary">
                                        Nonaktif
                                    </span>

                                @endif

                            </td>


                            <td class="text-end">

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditSubject"
                                    data-id="{{ $subject->id }}"
                                    data-code="{{ $subject->code }}"
                                    data-name="{{ $subject->name }}"
                                    data-active="{{ $subject->is_active ? '1' : '0' }}"
                                >

                                    <i class="bi bi-pencil"></i>

                                </button>


                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDeleteSubject"
                                    data-id="{{ $subject->id }}"
                                    data-name="{{ $subject->name }}"
                                >

                                    <i class="bi bi-trash"></i>

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center text-muted py-5"
                            >

                                <i class="bi bi-book fs-2 d-block mb-2"></i>

                                Belum ada mata pelajaran.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    @if ($subjects->hasPages())

        <div class="card-footer">

            {{ $subjects->links() }}

        </div>

    @endif

</div>
