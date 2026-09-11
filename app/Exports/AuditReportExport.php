<?php

namespace App\Exports;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\Activitylog\Models\Activity;

class AuditReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected Request $request;

    protected Collection $organizationIds;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->organizationIds =
            Organization::accessibleIdsForUser(
                $request->user()
            );
    }

    public function collection(): Enumerable
    {
        $logName = $this->request->input('log_name');
        $event = $this->request->input('event');
        $year = $this->request->input('year');
        $month = $this->request->input('month');
        $dateFrom = $this->request->input('date_from');
        $dateTo = $this->request->input('date_to');
        $search = $this->request->input('search');

        return Activity::query()
            ->with('causer')
            ->where(function ($query) {

                foreach ($this->organizationIds as $organizationId) {

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
                fn ($query) =>
                    $query->where('log_name', $logName)
            )
            ->when(
                $event,
                fn ($query) =>
                    $query->where('event', $event)
            )
            ->when(
                $year,
                fn ($query) =>
                    $query->whereYear('created_at', $year)
            )
            ->when(
                $month,
                fn ($query) =>
                    $query->whereMonth('created_at', $month)
            )
            ->when(
                $dateFrom,
                fn ($query) =>
                    $query->whereDate(
                        'created_at',
                        '>=',
                        $dateFrom
                    )
            )
            ->when(
                $dateTo,
                fn ($query) =>
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
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu',
            'Pengguna',
            'Email',
            'Aktivitas',
            'Modul',
            'Data',
            'Organisasi',
            'Deskripsi',
            'Perubahan',
            'IP Address',
        ];
    }

    public function map($activity): array
    {
        $properties = $activity->properties;

        if ($properties instanceof Collection) {
            $properties = $properties->toArray();
        }

        $properties = $properties ?? [];

        $old = $properties['old'] ?? [];

        $attributes =
            $properties['attributes'] ?? [];

        $organizationId =
            $attributes['organization_id']
            ?? $old['organization_id']
            ?? null;

        $organization = $organizationId
            ? Organization::find($organizationId)
            : null;

        $changes = [];

        $fields = array_unique(
            array_merge(
                array_keys($old),
                array_keys($attributes)
            )
        );

        foreach ($fields as $field) {

            $oldValue =
                $old[$field] ?? null;

            $newValue =
                $attributes[$field] ?? null;

            if (
                $activity->event === 'updated'
                && $oldValue === $newValue
            ) {
                continue;
            }

            $changes[] =
                $field .
                ': ' .
                $this->formatValue($oldValue) .
                ' → ' .
                $this->formatValue($newValue);
        }

        return [
            $activity->id,

            optional($activity->created_at)
                ->format('d/m/Y H:i:s'),

            $activity->causer?->name ?? 'Sistem',

            $activity->causer?->email ?? '-',

            $this->eventLabel(
                $activity->event
            ),

            $this->moduleLabel(
                $activity->log_name
            ),

            $activity->subject_type
                ? class_basename(
                    $activity->subject_type
                ) . ' #' . $activity->subject_id
                : '-',

            $organization?->name ?? '-',

            $activity->description ?? '-',

            $changes
                ? implode("\n", $changes)
                : '-',

            $activity->ip_address ?? '-',
        ];
    }

    private function eventLabel(?string $event): string
    {
        return match ($event) {
            'created' => 'Dibuat',
            'updated' => 'Diperbarui',
            'deleted' => 'Dihapus',
            default => $event ?: '-',
        };
    }

    private function moduleLabel(?string $module): string
    {
        return match ($module) {
            'finance_deposit' =>
                'Setoran',

            'finance_transaction' =>
                'Transaksi Keuangan',

            default =>
                $module ?: '-',
        };
    }

    private function formatValue(mixed $value): string
    {
        if ($value === null) {
            return '-';
        }

        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }

        if (is_array($value)) {
            return json_encode(
                $value,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            );
        }

        return (string) $value;
    }
}
