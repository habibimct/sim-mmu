<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();

        $totalStudents = Student::count();

        $totalTeachers = Teacher::count();

        $totalUnits = Organization::where('type', 'UNIT')->count();

        $activities = Activity::with('causer')
            ->latest()
            ->take(10)
            ->get();

        $activityUserIds = $activities
            ->pluck('user_id')
            ->filter()
            ->unique();

        $activityUsers = User::whereIn('id', $activityUserIds)
            ->pluck('name', 'id');

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalTeachers',
            'totalUnits',
            'activities',
            'activityUsers'
        ));
    }

    public function activityLogs(Request $request): View
    {
        $query = Activity::with('causer');

        /*
    |--------------------------------------------------------------------------
    | Filter User
    |--------------------------------------------------------------------------
    */

        if ($request->filled('user_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('causer_id', $request->user_id)
                    ->orWhere('user_id', $request->user_id);
            });
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Modul
    |--------------------------------------------------------------------------
    */

        if ($request->filled('log_name')) {
            $query->where(
                'log_name',
                $request->log_name
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Aktivitas
    |--------------------------------------------------------------------------
    */

        if ($request->filled('event')) {
            $query->where(
                'event',
                $request->event
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Tanggal Mulai
    |--------------------------------------------------------------------------
    */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Tanggal Sampai
    |--------------------------------------------------------------------------
    */

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        $activities = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | User untuk filter
    |--------------------------------------------------------------------------
    */

        $users = User::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        /*
    |--------------------------------------------------------------------------
    | Modul untuk filter
    |--------------------------------------------------------------------------
    */

        $logNames = Activity::query()
            ->whereNotNull('log_name')
            ->where('log_name', '!=', '')
            ->distinct()
            ->orderBy('log_name')
            ->pluck('log_name');

        /*
    |--------------------------------------------------------------------------
    | Jenis aktivitas untuk filter
    |--------------------------------------------------------------------------
    */

        $events = Activity::query()
            ->whereNotNull('event')
            ->where('event', '!=', '')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        return view(
            'admin.activity-logs.index',
            compact(
                'activities',
                'users',
                'logNames',
                'events'
            )
        );
    }

    public function activityDetail(int $activity): View
    {
        $activity = Activity::findOrFail($activity);

        $activity->load('causer');

        $activityUser = null;

        if ($activity->user_id) {
            $activityUser = User::find($activity->user_id);
        }

        return view('admin.partials.activity-detail', compact(
            'activity',
            'activityUser'
        ));
    }
}
