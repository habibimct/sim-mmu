<div class="modal fade"
    id="activityLogFilterModal"
    tabindex="-1"
    aria-labelledby="activityLogFilterModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="GET"
                action="{{ route('admin.activity-logs.index') }}">

                <div class="modal-header">

                    <h5 class="modal-title"
                        id="activityLogFilterModalLabel">
                        <i class="bi bi-funnel me-1"></i>
                        Filter Activity Log
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        {{-- User --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                User
                            </label>

                            <select name="user_id"
                                class="form-select">

                                <option value="">
                                    Semua User
                                </option>

                                @foreach ($users as $user)

                                    <option value="{{ $user->id }}"
                                        @selected(
                                            (string) request('user_id')
                                            ===
                                            (string) $user->id
                                        )>
                                        {{ $user->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- Modul --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Modul
                            </label>

                            <select name="log_name"
                                class="form-select">

                                <option value="">
                                    Semua Modul
                                </option>

                                @foreach ($logNames as $logName)

                                    <option value="{{ $logName }}"
                                        @selected(
                                            request('log_name')
                                            ===
                                            $logName
                                        )>
                                        {{ ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $logName
                                            )
                                        ) }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- Aktivitas --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Aktivitas
                            </label>

                            <select name="event"
                                class="form-select">

                                <option value="">
                                    Semua Aktivitas
                                </option>

                                @foreach ($events as $event)

                                    <option value="{{ $event }}"
                                        @selected(
                                            request('event')
                                            ===
                                            $event
                                        )>
                                        {{ ucfirst($event) }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- Tanggal Mulai --}}
                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                Tanggal Mulai
                            </label>

                            <input type="date"
                                name="date_from"
                                class="form-control"
                                value="{{ request('date_from') }}">

                        </div>

                        {{-- Tanggal Sampai --}}
                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                Tanggal Sampai
                            </label>

                            <input type="date"
                                name="date_to"
                                class="form-control"
                                value="{{ request('date_to') }}">

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <a href="{{ route('admin.activity-logs.index') }}"
                        class="btn btn-secondary">

                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset

                    </a>

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-funnel me-1"></i>
                        Terapkan Filter

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
