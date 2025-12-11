<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs with filtering
     */
    public function index(Request $request)
    {
        $query = Activity::with('causer');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by subject type (entity)
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter by description/event
        if ($request->filled('event')) {
            $query->where('description', 'like', "%{$request->event}%");
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description or properties
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();

        // Get filter options
        $users = User::orderBy('name')->get();
        $subjectTypes = Activity::select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->pluck('subject_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type)
                ];
            });

        // Stats
        $stats = [
            'total_logs' => Activity::count(),
            'today_logs' => Activity::whereDate('created_at', today())->count(),
            'week_logs' => Activity::where('created_at', '>=', now()->subWeek())->count(),
            'unique_users' => Activity::distinct('causer_id')->count('causer_id'),
        ];

        return view('admin.activity-logs.index', compact('activities', 'users', 'subjectTypes', 'stats'));
    }

    /**
     * Export activity logs to CSV
     */
    public function export(Request $request)
    {
        $query = Activity::with('causer');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }
        if ($request->filled('event')) {
            $query->where('description', 'like', "%{$request->event}%");
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        $activities = $query->orderBy('created_at', 'desc')->get();

        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, ['ID', 'User', 'Action', 'Entity Type', 'Entity ID', 'Description', 'Date/Time']);

            // CSV Rows
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->id,
                    $activity->causer ? $activity->causer->name : 'System',
                    $activity->log_name ?? 'default',
                    class_basename($activity->subject_type ?? 'N/A'),
                    $activity->subject_id ?? 'N/A',
                    $activity->description,
                    $activity->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
