<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalystAssignment;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Show assignment management dashboard
     */
    public function index()
    {
        // Get all assignments with relationships
        $assignments = AnalystAssignment::with(['analyst', 'trader', 'assignedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get unassigned traders
        $assignedTraderIds = AnalystAssignment::pluck('trader_id');
        $unassignedTraders = User::role('trader')
            ->whereNotIn('id', $assignedTraderIds)
            ->where('verification_status', 'verified')
            ->get();

        // Get all verified analysts
        $analysts = User::role('analyst')
            ->where('verification_status', 'verified')
            ->withCount('tradersAssigned')
            ->get();

        // Stats
        $stats = [
            'total_assignments' => $assignments->count(),
            'unassigned_traders' => $unassignedTraders->count(),
            'total_analysts' => $analysts->count(),
        ];

        return view('admin.assignments.index', compact('assignments', 'unassignedTraders', 'analysts', 'stats'));
    }

    /**
     * Assign analyst to trader
     */
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'trader_id' => 'required|exists:users,id',
            'analyst_id' => 'required|exists:users,id',
        ]);

        // Check if trader is already assigned
        $existing = AnalystAssignment::where('trader_id', $validated['trader_id'])->first();
        if ($existing) {
            return redirect()
                ->route('admin.assignments.index')
                ->with('error', 'Trader is already assigned to an analyst.');
        }

        $trader = User::findOrFail($validated['trader_id']);
        $analyst = User::findOrFail($validated['analyst_id']);

        $assignment = AnalystAssignment::create([
            'analyst_id' => $validated['analyst_id'],
            'trader_id' => $validated['trader_id'],
            'assigned_by' => Auth::id(),
        ]);

        // Notify analyst
        $this->notificationService->notifyAnalystAssigned($analyst, $trader);

        activity()
            ->performedOn($assignment)
            ->log("Assigned {$trader->name} to analyst {$analyst->name}");

        return redirect()
            ->route('admin.assignments.index')
            ->with('success', "{$trader->name} has been assigned to {$analyst->name}!");
    }

    /**
     * Reassign trader to different analyst
     */
    public function reassign(Request $request, $assignmentId)
    {
        $validated = $request->validate([
            'analyst_id' => 'required|exists:users,id',
        ]);

        $assignment = AnalystAssignment::findOrFail($assignmentId);
        $newAnalyst = User::findOrFail($validated['analyst_id']);

        $oldAnalystId = $assignment->analyst_id;
        $assignment->update([
            'analyst_id' => $validated['analyst_id'],
            'assigned_by' => Auth::id(),
        ]);

        // Notify new analyst
        $this->notificationService->notifyAnalystAssigned($newAnalyst, $assignment->trader);

        activity()
            ->performedOn($assignment)
            ->log("Reassigned {$assignment->trader->name} to {$newAnalyst->name}");

        return redirect()
            ->route('admin.assignments.index')
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * Remove assignment
     */
    public function remove($assignmentId)
    {
        $assignment = AnalystAssignment::findOrFail($assignmentId);
        
        activity()
            ->performedOn($assignment)
            ->log("Removed assignment: {$assignment->trader->name} from {$assignment->analyst->name}");

        $assignment->delete();

        return redirect()
            ->route('admin.assignments.index')
            ->with('success', 'Assignment removed successfully!');
    }
}
