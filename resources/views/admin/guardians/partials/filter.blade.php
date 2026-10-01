<div class="modal fade" id="guardianFilterModal" tabindex="-1"
    aria-labelledby="guardianFilterModalLabel" aria-hidden="true">

    <div class="modal-dialog">

        <form method="GET"
            action="{{ route('admin.guardians.index') }}"
            class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="guardianFilterModalLabel">
                    <i class="bi bi-funnel me-1"></i>
                    Filter Wali
                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- Pencarian --}}
                <div class="mb-3">

                    <label for="guardian_search"
                        class="form-label">
                        Pencarian
                    </label>

                    <input type="text"
                        name="search"
                        id="guardian_search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Nama, NIK, atau No. HP">

                </div>

                {{-- Organisasi --}}
                <div class="mb-3">

                    <label for="guardian_organization_id"
                        class="form-label">
                        Unit
                    </label>

                    <select name="organization_id"
                        id="guardian_organization_id"
                        class="form-select">

                        <option value="">
                            Semua Unit
                        </option>

                        @foreach ($organizations as $organization)

                            <option value="{{ $organization->id }}"
                                @selected(
                                    (string) request('organization_id')
                                    === (string) $organization->id
                                )>
                                {{ $organization->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Status --}}
                <div class="mb-3">

                    <label for="guardian_status"
                        class="form-label">
                        Status
                    </label>

                    <select name="status"
                        id="guardian_status"
                        class="form-select">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="active"
                            @selected(request('status') === 'active')>
                            Aktif
                        </option>

                        <option value="inactive"
                            @selected(request('status') === 'inactive')>
                            Nonaktif
                        </option>

                    </select>

                </div>

            </div>

            <div class="modal-footer">

                <a href="{{ route('admin.guardians.index') }}"
                    class="btn btn-secondary">
                    Reset
                </a>

                <button type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="submit"
                    class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i>
                    Terapkan
                </button>

            </div>

        </form>

    </div>

</div>
