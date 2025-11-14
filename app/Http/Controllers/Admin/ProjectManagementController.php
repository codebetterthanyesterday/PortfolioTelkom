<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['student.user', 'categories']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $projects = $query->latest()->paginate(20);

        return view('admin.projects.index', compact('projects'));
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return back()->with('success', 'Project deleted successfully!');
    }

    public function updateStatus(Request $request, Project $project)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,published,archived',
        ]);

        $project->update(['status' => $validated['status']]);

        return back()->with('success', 'Project status updated!');
    }
}
