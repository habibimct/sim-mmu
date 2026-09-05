<div
    class="modal fade"
    id="modalFilterSubject"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog">

        <form
            method="GET"
            action="{{ route('admin.subjects.index') }}"
            class="modal-content"
        >

            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="bi bi-funnel me-1"></i>

                    Filter Mata Pelajaran

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">
                        Pencarian
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Kode atau nama mata pelajaran..."
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option
                            value="active"
                            @selected(request('status') === 'active')
                        >
                            Aktif
                        </option>

                        <option
                            value="inactive"
                            @selected(request('status') === 'inactive')
                        >
                            Nonaktif
                        </option>

                    </select>

                </div>

            </div>


            <div class="modal-footer">

                <a
                    href="{{ route('admin.subjects.index') }}"
                    class="btn btn-outline-secondary"
                >
                    Reset
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="bi bi-search me-1"></i>

                    Terapkan

                </button>

            </div>

        </form>

    </div>

</div>
