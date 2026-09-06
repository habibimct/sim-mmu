<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\FinanceTransaction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Organisasi induk.
     */
    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }

    /**
     * Organisasi/unit di bawah organisasi ini.
     */
    public function children()
    {
        return $this->hasMany(Organization::class, 'parent_id');
    }

    /**
     * User yang bertugas pada organisasi ini.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**

     * Mengambil seluruh organisasi di bawah organisasi ini
     * secara rekursif.
     */
    /**

     * Mengambil semua organisasi yang berada di bawah organisasi ini.
     */
    public function descendantIds(): \Illuminate\Support\Collection
    {
        $ids = collect();

        foreach ($this->children as $child) {
            $ids->push($child->id);

            $ids = $ids->merge(
                $child->descendantIds()
            );
        }

        return $ids->unique()->values();
    }

    /**

     * Mengambil ID organisasi yang dapat diakses oleh user yang sedang login.
     */
    public static function accessibleIdsForUser(): \Illuminate\Support\Collection
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        $organizations = $user
            ->organizations()
            ->with('children')
            ->get();

        $ids = collect();

        foreach ($organizations as $organization) {


            // Organisasi induk:
            // dapat melihat organisasi tersebut beserta seluruh turunannya.
            if ($organization->parent_id === null) {

                $ids->push($organization->id);

                $ids = $ids->merge(
                    $organization->descendantIds()
                );
            } else {

                // Organisasi unit:
                // hanya dapat melihat organisasinya sendiri.
                $ids->push($organization->id);
            }
        }

        return $ids
            ->unique()
            ->values();
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            Teacher::class,
            'organization_teacher'
        );
    }

    /**
     * Kelas yang dimiliki organisasi.
     */
    public function schoolClasses(): HasMany
    {
        return $this->hasMany(
            SchoolClass::class
        );
    }

    /**
     * Riwayat penempatan siswa pada unit ini.
     */
    public function studentAcademicYears(): HasMany
    {
        return $this->hasMany(
            StudentAcademicYear::class
        );
    }

    public function financeTransactions(): HasMany
    {
        return $this->hasMany(
            FinanceTransaction::class
        );
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(
            Subject::class
        );
    }

    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(
            TeachingAssignment::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillable())
            ->logOnlyDirty()
            ->useLogName('organization');
    }
}
