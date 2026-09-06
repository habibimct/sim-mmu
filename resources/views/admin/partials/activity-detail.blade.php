<div class="modal fade" id="activityDetailModal" tabindex="-1"
    aria-labelledby="activityDetailModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="activityDetailModalLabel">
                    <i class="bi bi-eye me-1"></i>
                    Detail Aktivitas
                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>

            <div class="modal-body">

                <table class="table table-bordered align-middle mb-0">
                    <tr>
                        <th width="180">Waktu</th>
                        <td>{{ $activity->created_at?->format('d/m/Y H:i:s') ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>User</th>
                        <td>
                            {{ $activity->causer?->name
                                ?? $activityUser?->name
                                ?? 'Sistem' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Aktivitas</th>
                        <td>
                            {{ ucfirst($activity->event ?? $activity->description ?? '-') }}
                        </td>
                    </tr>

                    <tr>
                        <th>Modul</th>
                        <td>{{ $activity->log_name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Data</th>
                        <td>
                            @if ($activity->subject_type)
                                {{ class_basename($activity->subject_type) }}

                                @if ($activity->subject_id)
                                    #{{ $activity->subject_id }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>IP Address</th>
                        <td>{{ $activity->ip_address ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>User Agent</th>
                        <td style="word-break: break-word;">
                            {{ $activity->user_agent ?? '-' }}
                        </td>
                    </tr>
                </table>

                @php
                    $properties = $activity->properties;

                    $oldData = [];
                    $newData = [];

                    if ($properties) {
                        $oldData = $properties->get('old', []);
                        $newData = $properties->get('attributes', []);

                        $oldData = is_array($oldData) ? $oldData : [];
                        $newData = is_array($newData) ? $newData : [];
                    }

                    $fields = array_unique(
                        array_merge(
                            array_keys($oldData),
                            array_keys($newData)
                        )
                    );
                @endphp

                @if (count($fields))
                    <div class="mt-4">

                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-arrow-left-right me-1"></i>
                            Perubahan Data
                        </h6>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th width="25%">Field</th>
                                        <th width="37.5%">Sebelum</th>
                                        <th width="37.5%">Sesudah</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($fields as $field)
                                        @php
                                            $oldValue = $oldData[$field] ?? '-';
                                            $newValue = $newData[$field] ?? '-';

                                            if (is_array($oldValue)) {
                                                $oldValue = implode(', ', $oldValue);
                                            }

                                            if (is_array($newValue)) {
                                                $newValue = implode(', ', $newValue);
                                            }
                                        @endphp

                                        <tr>
                                            <td class="fw-semibold">
                                                {{ ucwords(str_replace('_', ' ', $field)) }}
                                            </td>

                                            <td>
                                                {{ $oldValue }}
                                            </td>

                                            <td>
                                                {{ $newValue }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                @endif

            </div>

            <div class="modal-footer">
                <button type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
