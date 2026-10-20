<?php

namespace App\Imports;

use App\Models\Guardian;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuardiansImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private array $organizationIds;
    private array $processedNiks = [];
    private array $errors = [];
    private int $successCount = 0;

    public function __construct(array $organizationIds)
    {
        $this->organizationIds = $organizationIds;
    }

    public function collection(Collection $rows): void
    {
        $organizations = Organization::query()
            ->whereIn('id', $this->organizationIds)
            ->where('is_active', true)
            ->get();

        $role = Role::where('code', 'wali')->first();

        if (! $role) {
            throw new \RuntimeException(
                'Role wali belum tersedia di database.'
            );
        }

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $data = $row->toArray();

            $unitCode = trim((string) ($data['unit'] ?? ''));
            $name = trim((string) ($data['nama_wali'] ?? ''));
            $nik = trim((string) ($data['nik'] ?? ''));
            $phone = trim((string) ($data['no_hp'] ?? ''));

            $validator = Validator::make([
                'unit' => $unitCode,
                'name' => $name,
                'nik' => $nik,
                'phone' => $phone,
            ], [
                'unit' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'nik' => ['required', 'regex:/^[0-9]{16}$/'],
                'phone' => ['nullable', 'string', 'max:30'],
            ], [
                'unit.required' => 'Nama unit wajib diisi.',
                'name.required' => 'Nama wali wajib diisi.',
                'nik.required' => 'NIK wajib diisi.',
                'nik.regex' => 'NIK harus berupa 16 digit angka.',
            ]);

            if ($validator->fails()) {
                $this->addError(
                    $rowNumber,
                    $validator->errors()->first()
                );
                continue;
            }

            $organization = $organizations->first(
                fn ($item) => Str::lower(trim($item->code))
                    === Str::lower($unitCode)
            );

            if (! $organization) {
                $this->addError(
                    $rowNumber,
                    "Unit '{$unitCode}' tidak ditemukan atau tidak dapat diakses."
                );
                continue;
            }

            if (isset($this->processedNiks[$nik])) {
                $this->addError(
                    $rowNumber,
                    'NIK duplikat pada file Excel.'
                );
                continue;
            }

            $this->processedNiks[$nik] = true;

            if (Guardian::where('nik', $nik)->exists()) {
                $this->addError(
                    $rowNumber,
                    'NIK sudah terdaftar sebagai wali.'
                );
                continue;
            }

            try {
                DB::transaction(function () use (
                    $name,
                    $nik,
                    $phone,
                    $organization,
                    $role
                ) {
                    $initialPassword = 'PMUB@' . $nik;

                    $user = User::create([
                        'name' => $name,
                        'email' => null,
                        'password' => Hash::make($initialPassword),
                        'is_active' => true,
                    ]);

                    $user->roles()->attach($role->id);

                    Guardian::create([
                        'user_id' => $user->id,
                        'organization_id' => $organization->id,
                        'name' => $name,
                        'nik' => $nik,
                        'phone' => $phone !== '' ? $phone : null,
                        'initial_password' => $initialPassword,
                        'is_active' => true,
                    ]);
                });

                $this->successCount++;
            } catch (\Throwable $e) {
                report($e);

                $this->addError(
                    $rowNumber,
                    'Gagal menyimpan data. Periksa log aplikasi.'
                );
            }
        }
    }

    private function addError(int $row, string $message): void
    {
        $this->errors[] = [
            'row' => $row,
            'message' => $message,
        ];
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
