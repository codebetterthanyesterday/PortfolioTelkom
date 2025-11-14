<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query()
            ->with(['student.user', 'media', 'categories'])
            ->published();

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sort = $request->get('sort', 'recent');
        if ($sort === 'popular') {
            $query->popular();
        } else {
            $query->recent();
        }

        $projects = $query->paginate(12);
        $categories = Category::all();

        return view('projects.index', compact('projects', 'categories'));
    }

    public function show(Project $project)
    {
        $project->load([
            'student.user',
            'media',
            'categories',
            'subjects',
            'teachers',
            'teamMembers.user',
            'comments' => function ($query) {
                $query->parent()->with(['user', 'allReplies.user'])->latest();
            }
        ]);

        // Increment view count
        $project->incrementViewCount();

        // Check if investor has wishlisted
        $isWishlisted = false;
        if (auth()->check() && auth()->user()->isInvestor()) {
            $isWishlisted = auth()->user()->investor->hasWishlisted($project);
        }

        return view('projects.show', compact('project', 'isWishlisted'));
    }

    public function create()
    {
        $categories = Category::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $students = Student::with('user')->get();

        return view('projects.create', compact('categories', 'subjects', 'teachers', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'type' => 'required|in:individual,team',
            'status' => 'required|in:draft,published',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:teachers,id',
            'media' => 'nullable|array|max:10',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4,mov|max:10240',
            'team_members' => 'required_if:type,team|array',
            'team_members.*' => 'exists:students,id',
            'team_positions' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $student = auth()->user()->student;

            // Create project
            $project = Project::create([
                'student_id' => $student->id,
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'description' => $validated['description'],
                'price' => $validated['price'] ?? null,
                'type' => $validated['type'],
                'status' => $validated['status'],
            ]);

            // Attach categories
            $project->categories()->attach($validated['categories']);

            // Attach subjects
            if (!empty($validated['subjects'])) {
                $project->subjects()->attach($validated['subjects']);
            }

            // Attach teachers
            if (!empty($validated['teachers'])) {
                $project->teachers()->attach($validated['teachers']);
            }

            // Handle media uploads
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $index => $file) {
                    $path = $file->store('projects/' . $project->id, 'public');
                    
                    $project->media()->create([
                        'type' => str_starts_with($file->getMimeType(), 'image') ? 'image' : 'video',
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'order' => $index,
                    ]);
                }
            }

            // Add team members if team project
            if ($validated['type'] === 'team' && !empty($validated['team_members'])) {
                // Add creator as leader
                $project->members()->create([
                    'student_id' => $student->id,
                    'role' => 'leader',
                    'position' => 'Project Leader',
                    'joined_at' => now(),
                ]);

                // Add team members
                foreach ($validated['team_members'] as $index => $memberId) {
                    if ($memberId != $student->id) {
                        $project->members()->create([
                            'student_id' => $memberId,
                            'role' => 'member',
                            'position' => $validated['team_positions'][$index] ?? 'Team Member',
                            'joined_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('projects.show', $project)
                ->with('success', 'Project created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create project: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        $project->load(['categories', 'subjects', 'teachers', 'media', 'teamMembers']);
        $categories = Category::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $students = Student::with('user')->get();

        return view('projects.edit', compact('project', 'categories', 'subjects', 'teachers', 'students'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,published,archived',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:teachers,id',
        ]);

        DB::beginTransaction();
        try {
            $project->update([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'description' => $validated['description'],
                'price' => $validated['price'] ?? null,
                'status' => $validated['status'],
            ]);

            // Sync relations
            $project->categories()->sync($validated['categories']);
            $project->subjects()->sync($validated['subjects'] ?? []);
            $project->teachers()->sync($validated['teachers'] ?? []);

            DB::commit();

            return redirect()->route('projects.show', $project)
                ->with('success', 'Project updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update project: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        // Delete media files
        foreach ($project->media as $media) {
            Storage::disk('public')->delete($media->file_path);
        }

        $project->delete();

        return redirect()->route('student.dashboard')
            ->with('success', 'Project deleted successfully!');
    }
}