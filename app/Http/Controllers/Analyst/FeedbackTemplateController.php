<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Models\FeedbackTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackTemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index()
    {
        $templates = Auth::user()->feedbackTemplates()->latest()->get();
        return view('analyst.templates.index', compact('templates'));
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:risk,psychology,strategy,general',
            'content' => 'required|string',
        ]);

        Auth::user()->feedbackTemplates()->create($validated);

        return redirect()->route('analyst.templates.index')
            ->with('success', 'Template created successfully.');
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, FeedbackTemplate $template)
    {
        // Ensure ownership
        if ($template->analyst_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:risk,psychology,strategy,general',
            'content' => 'required|string',
        ]);

        $template->update($validated);

        return redirect()->route('analyst.templates.index')
            ->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(FeedbackTemplate $template)
    {
        // Ensure ownership
        if ($template->analyst_id !== Auth::id()) {
            abort(403);
        }

        $template->delete();

        return redirect()->route('analyst.templates.index')
            ->with('success', 'Template deleted successfully.');
    }
}
