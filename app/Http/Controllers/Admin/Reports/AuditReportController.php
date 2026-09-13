<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Exports\AuditReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditReportController extends Controller
{
    /**
     * Laporan audit.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Scope organisasi
        |--------------------------------------------------------------------------
        */

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        $logName = $request->input('log_name');
        $event = $request->input('event');
        $year = $request->input('year');
        $month = $request->input('month');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');

        $request->validate([
            'log_name' => [
                'nullable',
                'in:finance_deposit,finance_transaction',
            ],

            'event' => [
                'nullable',
                'in:created,updated,deleted',
            ],

            'year' => [
                'nullable',
                'integer',
                'min:2000',
                'max:2100',
            ],

            'month' => [
                'nullable',
                'integer',
                'between:1,12',
            ],

            'date_from' => [
                'nullable',
                'date',
            ],

            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],

            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Query dasar
        |--------------------------------------------------------------------------
        */

        $query = Activity::query()
            ->with('causer')
            ->where(function ($query) use ($organizationIds) {

                /*
                | Activity yang memiliki organization_id
                | pada attributes.
                */

                foreach ($organizationIds as $organizationId) {

                    $query->orWhereJsonContains(
                        'properties->attributes->organization_id',
                        (int) $organizationId
                    );
                }

                /*
                | Untuk aktivitas update, organization_id
                | dapat berada pada old atau attributes.
                */

                foreach ($organizationIds as $organizationId) {

                    $query->orWhereJsonContains(
                        'properties->old->organization_id',
                        (int) $organizationId
                    );
                }
            })
            ->when(
                $logName,
                fn($query) =>
                $query->where('log_name', $logName)
            )
            ->when(
                $event,
                fn($query) =>
                $query->where('event', $event)
            )
            ->when(
                $year,
                fn($query) =>
                $query->whereYear('created_at', $year)
            )
            ->when(
                $month,
                fn($query) =>
                $query->whereMonth('created_at', $month)
            )
            ->when(
                $dateFrom,
                fn($query) =>
                $query->whereDate(
                    'created_at',
                    '>=',
                    $dateFrom
                )
            )
            ->when(
                $dateTo,
                fn($query) =>
                $query->whereDate(
                    'created_at',
                    '<=',
                    $dateTo
                )
            )
            ->when(
                $search,
                function ($query) use ($search) {

                    $query->where(function ($query) use ($search) {

                        $query
                            ->where(
                                'description',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'log_name',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'event',
                                'like',
                                '%' . $search . '%'
                            );
                    });
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalActivities =
            (clone $query)->count();

        $createdActivities =
            (clone $query)
            ->where('event', 'created')
            ->count();

        $updatedActivities =
            (clone $query)
            ->where('event', 'updated')
            ->count();

        $deletedActivities =
            (clone $query)
            ->where('event', 'deleted')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $activities = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Tahun yang tersedia
        |--------------------------------------------------------------------------
        */

        $years = Activity::query()
            ->where(function ($query) use ($organizationIds) {

                foreach ($organizationIds as $organizationId) {

                    $query->orWhereJsonContains(
                        'properties->attributes->organization_id',
                        (int) $organizationId
                    );

                    $query->orWhereJsonContains(
                        'properties->old->organization_id',
                        (int) $organizationId
                    );
                }
            })
            ->selectRaw(
                'YEAR(created_at) as year'
            )
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view(
            'admin.reports.audit.index',
            compact(
                'activities',
                'years',
                'logName',
                'event',
                'year',
                'month',
                'dateFrom',
                'dateTo',
                'search',
                'totalActivities',
                'createdActivities',
                'updatedActivities',
                'deletedActivities',
            )
        );
    }

    /**
     * Detail aktivitas audit.
     */
    public function detail(Activity $activity)
    {
        $user = request()->user();

        /*
    |--------------------------------------------------------------------------
    | Scope organisasi
    |--------------------------------------------------------------------------
    */

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        /*
    |--------------------------------------------------------------------------
    | Ambil properties
    |--------------------------------------------------------------------------
    */

        $properties = $activity->properties;

        if ($properties instanceof \Illuminate\Support\Collection) {
            $properties = $properties->toArray();
        }

        $properties = $properties ?? [];

        $old = $properties['old'] ?? [];

        $attributes = $properties['attributes'] ?? [];

        /*
    |--------------------------------------------------------------------------
    | Tentukan organization_id
    |--------------------------------------------------------------------------
    */

        $organizationId =
            $attributes['organization_id']
            ?? $old['organization_id']
            ?? null;

        /*
    |--------------------------------------------------------------------------
    | Pastikan activity berada dalam scope organisasi
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $organizationId !== null
                && $organizationIds->contains(
                    (int) $organizationId
                ),
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Ambil organisasi
    |--------------------------------------------------------------------------
    */

        $organization = Organization::find($organizationId);

        /*
    |--------------------------------------------------------------------------
    | Buat daftar perubahan
    |--------------------------------------------------------------------------
    */

        $changes = [];

        $fieldNames = array_unique(
            array_merge(
                array_keys($old),
                array_keys($attributes)
            )
        );

        foreach ($fieldNames as $field) {

            $oldValue =
                $old[$field] ?? null;

            $newValue =
                $attributes[$field] ?? null;

            /*
        | created:
        | hanya ada attributes
        */

            if (
                $activity->event === 'created'
                && !array_key_exists($field, $old)
            ) {
                $oldValue = null;
            }

            /*
        | deleted:
        | biasanya hanya ada old
        */

            if (
                $activity->event === 'deleted'
                && !array_key_exists($field, $attributes)
            ) {
                $newValue = null;
            }

            /*
        | Untuk updated, hanya tampilkan field
        | yang benar-benar berubah.
        */

            if (
                $activity->event === 'updated'
                && $oldValue === $newValue
            ) {
                continue;
            }

            $changes[] = [
                'field' => $field,
                'old' => $this->formatAuditValue(
                    $field,
                    $oldValue
                ),
                'new' => $this->formatAuditValue(
                    $field,
                    $newValue
                ),
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return response()->json([

            'id' =>
            $activity->id,

            'date' =>
            optional($activity->created_at)
                ->format('d/m/Y H:i:s'),

            'user' => [
                'id' =>
                $activity->causer?->id,

                'name' =>
                $activity->causer?->name,

                'email' =>
                $activity->causer?->email,
            ],

            'event' =>
            $activity->event,

            'module' =>
            $activity->log_name,

            'description' =>
            $activity->description,

            'subject' => [
                'type' =>
                $activity->subject_type
                    ? class_basename(
                        $activity->subject_type
                    )
                    : null,

                'id' =>
                $activity->subject_id,
            ],

            'organization' => [
                'id' =>
                $organization?->id,

                'name' =>
                $organization?->name,
            ],

            'ip_address' =>
            $activity->ip_address,

            'user_agent' =>
            $activity->user_agent,

            'changes' =>
            $changes,
        ]);
    }

    /**
     * Format nilai audit agar mudah dibaca.
     */
    private function formatAuditValue(
        string $field,
        mixed $value
    ): mixed {
        if ($value === null) {
            return '-';
        }

        /*
    |--------------------------------------------------------------------------
    | Nilai boolean
    |--------------------------------------------------------------------------
    */

        if (is_bool($value)) {
            return $value
                ? 'Ya'
                : 'Tidak';
        }

        /*
    |--------------------------------------------------------------------------
    | Array / object
    |--------------------------------------------------------------------------
    */

        if (is_array($value)) {
            return json_encode(
                $value,
                JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Field tertentu
    |--------------------------------------------------------------------------
    */

        if ($field === 'amount') {
            return 'Rp ' .
                number_format(
                    (float) $value,
                    0,
                    ',',
                    '.'
                );
        }

        if ($field === 'payment_method') {

            return match ($value) {
                'cash' =>
                'Tunai',

                'bank_transfer' =>
                'Transfer Bank',

                'online' =>
                'Online',

                default =>
                ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $value
                    )
                ),
            };
        }

        if ($field === 'status') {

            return match ($value) {
                'pending' =>
                'Menunggu Konfirmasi',

                'confirmed' =>
                'Dikonfirmasi',

                'rejected' =>
                'Ditolak',

                'cancelled' =>
                'Dibatalkan',

                default =>
                ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $value
                    )
                ),
            };
        }

        if (
            in_array(
                $field,
                [
                    'deposit_date',
                    'transaction_date',
                    'confirmed_at',
                    'cancelled_at',
                ],
                true
            )
        ) {

            try {

                return \Carbon\Carbon::parse($value)
                    ->format('d/m/Y H:i:s');
            } catch (\Throwable) {

                return (string) $value;
            }
        }

        return (string) $value;
    }

    /**
     * Export laporan audit ke Excel.
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new AuditReportExport($request),
            'laporan-audit-' .
                now()->format('Y-m-d-His') .
                '.xlsx'
        );
    }

    /**
     * Export laporan audit ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $user = $request->user();

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        $logName = $request->input('log_name');
        $event = $request->input('event');
        $year = $request->input('year');
        $month = $request->input('month');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');

        $activities = Activity::query()
            ->with('causer')
            ->where(function ($query) use ($organizationIds) {

                foreach ($organizationIds as $organizationId) {

                    $query->orWhereJsonContains(
                        'properties->attributes->organization_id',
                        (int) $organizationId
                    );

                    $query->orWhereJsonContains(
                        'properties->old->organization_id',
                        (int) $organizationId
                    );
                }
            })
            ->when(
                $logName,
                fn($query) =>
                $query->where('log_name', $logName)
            )
            ->when(
                $event,
                fn($query) =>
                $query->where('event', $event)
            )
            ->when(
                $year,
                fn($query) =>
                $query->whereYear('created_at', $year)
            )
            ->when(
                $month,
                fn($query) =>
                $query->whereMonth('created_at', $month)
            )
            ->when(
                $dateFrom,
                fn($query) =>
                $query->whereDate(
                    'created_at',
                    '>=',
                    $dateFrom
                )
            )
            ->when(
                $dateTo,
                fn($query) =>
                $query->whereDate(
                    'created_at',
                    '<=',
                    $dateTo
                )
            )
            ->when(
                $search,
                function ($query) use ($search) {

                    $query->where(function ($query) use ($search) {

                        $query
                            ->where(
                                'description',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'log_name',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'event',
                                'like',
                                '%' . $search . '%'
                            );
                    });
                }
            )
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Nama organisasi
    |--------------------------------------------------------------------------
    */

        $organizationNames = collect();

        foreach ($activities as $activity) {

            $properties = $activity->properties;

            if ($properties instanceof \Illuminate\Support\Collection) {
                $properties = $properties->toArray();
            }

            $properties = $properties ?? [];

            $attributes =
                $properties['attributes'] ?? [];

            $old =
                $properties['old'] ?? [];

            $organizationId =
                $attributes['organization_id']
                ?? $old['organization_id']
                ?? null;

            if ($organizationId) {

                $organization =
                    Organization::find($organizationId);

                if ($organization) {
                    $organizationNames->push(
                        $organization->name
                    );
                }
            }
        }

        $organizationNames =
            $organizationNames
            ->filter()
            ->unique()
            ->values();

        $organizationName =
            $organizationNames->count() === 1
            ? $organizationNames->first()
            : 'Beberapa Unit';

        /*
|--------------------------------------------------------------------------
| Profil Organisasi untuk Kop
|--------------------------------------------------------------------------
*/

        $organization = null;

        if ($organizationNames->count() === 1) {

            $organization =
                Organization::whereIn(
                    'id',
                    $organizationIds
                )
                ->where(
                    'name',
                    $organizationNames->first()
                )
                ->first();
        }

        $induk =
            Organization::where(
                'type',
                'induk'
            )
            ->firstOrFail();

        /*
    |--------------------------------------------------------------------------
    | Generate PDF
    |--------------------------------------------------------------------------
    */

        $pdf = Pdf::loadView(
            'admin.reports.audit.exports.pdf',
            [
                'activities' =>$activities,

                'organizationName' =>$organizationName,

                'organization' =>$organization,

                'induk' =>$induk,

                'logName' =>$logName,

                'event' =>$event,

                'year' =>$year,

                'month' =>$month,

                'dateFrom' =>$dateFrom,

                'dateTo' =>$dateTo,

                'search' =>$search,
            ]
        );

        $pdf->setPaper(
            'A4',
            'landscape'
        );

        return $pdf->download(
            'laporan-audit-' .
                now()->format('Y-m-d-His') .
                '.pdf'
        );
    }
}
