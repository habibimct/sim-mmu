<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
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
        | Organisasi Kepala Unit
        |--------------------------------------------------------------------------
        |
        | Kepala Unit hanya melihat guru yang berada
        | pada unit yang menjadi organisasinya.
        |
        */

        $organizations = $user
            ->organizations()
            ->where('is_active', true)
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ID organisasi yang menjadi cakupan
        |--------------------------------------------------------------------------
        */

        $organizationIds = $organizations
            ->pluck('id')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Query Guru
        |--------------------------------------------------------------------------
        */

        $query = Teacher::query()
            ->with([
                'organizations',
                'user.roles',
            ])
            ->whereHas(
                'organizations',
                function ($q) use ($organizationIds) {

                    $q->whereIn(
                        'organizations.id',
                        $organizationIds
                    );
                }
            );


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
        |
        | Jika Kepala Unit hanya memiliki satu unit,
        | filter tetap aman karena hanya unit miliknya
        | yang tersedia.
        |
        */

        if ($request->filled('organization_id')) {

            if (
                $organizationIds->contains(
                    (int) $request->organization_id
                )
            ) {

                $query->whereHas(
                    'organizations',
                    function ($q) use ($request) {

                        $q->where(
                            'organizations.id',
                            $request->organization_id
                        );
                    }
                );

            } else {

                /*
                 * ID organisasi di luar cakupan Kepala Unit.
                 */

                $query->whereRaw('1 = 0');
            }
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
            'kepala-unit.teachers.index',
            compact(
                'teachers',
                'organizations'
            )
        );
    }
}
