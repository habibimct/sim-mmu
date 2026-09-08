<?php

namespace App\Http\Controllers\KetuaInduk;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Unit dalam cakupan Ketua Induk
        |--------------------------------------------------------------------------
        |
        | Organisasi induk tidak ditampilkan.
        | Yang digunakan hanya seluruh unit di bawahnya.
        |
        */

        $parentOrganizations = $user
            ->organizations()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        $organizationIds = collect();

        foreach ($parentOrganizations as $organization) {

            $organizationIds = $organizationIds->merge(
                $organization->descendantIds()
            );

        }

        $organizationIds = $organizationIds
            ->unique()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Daftar unit untuk filter
        |--------------------------------------------------------------------------
        */

        $organizations = Organization::query()
            ->whereIn('id', $organizationIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Query Guru
        |--------------------------------------------------------------------------
        */

        $query = Teacher::query()
            ->with('organizations')
            ->whereHas('organizations', function ($q) use ($organizationIds) {

                $q->whereIn(
                    'organizations.id',
                    $organizationIds
                );

            });


        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'nik',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'email',
                    'like',
                    "%{$search}%"
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Unit
        |--------------------------------------------------------------------------
        */

        if ($request->filled('organization_id')) {

            $query->whereHas('organizations', function ($q) use ($request, $organizationIds) {

                $q->whereIn(
                    'organizations.id',
                    $organizationIds
                );

                $q->where(
                    'organizations.id',
                    $request->organization_id
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        if ($request->status === 'active') {

            $query->where(
                'is_active',
                true
            );

        } elseif ($request->status === 'inactive') {

            $query->where(
                'is_active',
                false
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Data Guru
        |--------------------------------------------------------------------------
        */

        $teachers = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();


        return view(
            'ketua-induk.teachers.index',
            compact(
                'teachers',
                'organizations'
            )
        );
    }
}
