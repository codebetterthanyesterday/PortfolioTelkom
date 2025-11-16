<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function gallery(Request $request)
    {
        // Get top 10 categories with most published projects
        $categories = Category::withCount(['projects' => function($q) {
                $q->published();
            }])
            ->get()
            ->filter(function($category) {
                return $category->projects_count > 0;
            })
            ->sortByDesc('projects_count')
            ->take(10)
            ->values();
        
        // Featured/Recent Projects (initial load - 8 projects)
        $featuredProjects = Project::published()
            ->with(['student.user', 'media', 'categories', 'wishlists'])
            ->recent()
            ->take(8)
            ->get();
        
        // Most Viewed Projects (6 projects)
        $mostViewedProjects = Project::published()
            ->with(['student.user', 'media', 'categories', 'wishlists'])
            ->popular()
            ->take(6)
            ->get();
        
        // Check wishlist status for investors
        $wishlistedProjects = [];
        if (auth()->check() && auth()->user()->isInvestor()) {
            $wishlistedProjects = auth()->user()->investor->wishlists()
                ->pluck('project_id')
                ->toArray();
        }
        
        return view('pages.gallery', compact(
            'categories',
            'featuredProjects',
            'mostViewedProjects',
            'wishlistedProjects'
        ));
    }

    public function galleryByCategory(Category $category)
    {
        // Get top 10 categories with most published projects
        $categories = Category::withCount(['projects' => function($q) {
                $q->published();
            }])
            ->get()
            ->filter(function($category) {
                return $category->projects_count > 0;
            })
            ->sortByDesc('projects_count')
            ->take(10)
            ->values();
        
        // Featured/Recent Projects filtered by category (8 projects)
        $featuredProjects = Project::published()
            ->with(['student.user', 'media', 'categories', 'wishlists'])
            ->whereHas('categories', function($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->recent()
            ->take(8)
            ->get();
        
        // Most Viewed Projects filtered by category (6 projects)
        $mostViewedProjects = Project::published()
            ->with(['student.user', 'media', 'categories', 'wishlists'])
            ->whereHas('categories', function($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->popular()
            ->take(6)
            ->get();
        
        // Check wishlist status for investors
        $wishlistedProjects = [];
        if (auth()->check() && auth()->user()->isInvestor()) {
            $wishlistedProjects = auth()->user()->investor->wishlists()
                ->pluck('project_id')
                ->toArray();
        }
        
        return view('pages.gallery', compact(
            'categories',
            'featuredProjects',
            'mostViewedProjects',
            'wishlistedProjects',
            'category'
        ));
    }

    public function filterProjects(Request $request)
    {
        $query = Project::published()
            ->with(['student.user', 'media', 'categories', 'wishlists']);
        
        // Filter by category slug
        if ($request->filled('category_slug') && $request->category_slug !== 'all') {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.slug', $request->category_slug);
            });
        }
        
        // Sort
        $sort = $request->get('sort', 'recent');
        if ($sort === 'popular') {
            $query->popular();
        } else {
            $query->recent();
        }
        
        $projects = $query->paginate(12);
        
        // Check wishlist status
        $wishlistedProjects = [];
        if (auth()->check() && auth()->user()->isInvestor()) {
            $wishlistedProjects = auth()->user()->investor->wishlists()
                ->pluck('project_id')
                ->toArray();
        }
        
        return response()->json([
            'success' => true,
            'projects' => $projects,
            'wishlistedProjects' => $wishlistedProjects
        ]);
    }

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
            'members.student.user',
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

        // Check if current user is project owner
        $isOwner = false;
        if (auth()->check() && auth()->user()->isStudent()) {
            $isOwner = auth()->user()->student->id === $project->student_id;
        }

        // Load data for edit modal (if owner)
        $categories = [];
        $subjects = [];
        $teachers = [];
        $students = [];
        if ($isOwner) {
            $categories = Category::all();
            $subjects = Subject::all();
            $teachers = Teacher::all();
            $students = Student::with('user')->where('id', '!=', auth()->user()->student->id)->get();
        }

        return view('pages.project-detail', compact('project', 'isWishlisted', 'isOwner', 'categories', 'subjects', 'teachers', 'students'));
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
            'status' => 'required|in:draft,published,archived',
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
                        'project_id' => $project->id,
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
                        
                        // Notify team member about being mentioned
                        $memberStudent = Student::find($memberId);
                        if ($memberStudent) {
                            Notification::create([
                                'user_id' => $memberStudent->user_id,
                                'type' => 'team_mention',
                                'notifiable_type' => Project::class,
                                'notifiable_id' => $project->id,
                                'data' => json_encode([
                                    'project_title' => $project->title,
                                    'project_slug' => $project->slug,
                                    'leader_name' => $student->user->full_name ?? $student->user->username,
                                    'message' => 'You have been added as a team member'
                                ])
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('student.profile')
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

    public function getEditData(Project $project)
    {
        try {
            $this->authorize('update', $project);

            $project->load([
                'categories',
                'subjects', 
                'teachers',
                'media' => function($query) {
                    $query->where('type', 'image')->orderBy('order');
                },
                'members.student.user'
            ]);

            return response()->json([
                'success' => true,
                'project' => [
                    'id' => $project->id,
                    'title' => $project->title,
                    'description' => $project->description,
                    'price' => $project->price,
                    'status' => $project->status,
                    'type' => $project->type,
                    'categories' => $project->categories,
                    'subjects' => $project->subjects,
                    'teachers' => $project->teachers,
                    'media' => $project->media,
                    'team_members' => $project->members->map(function($member) {
                        return [
                            'id' => $member->id,
                            'student_id' => $member->student_id,
                            'role' => $member->role,
                            'position' => $member->position,
                            'student' => [
                                'id' => $member->student->id,
                                'user' => $member->student->user
                            ]
                        ];
                    })
                ]
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengedit proyek ini'
            ], 403);
        } catch (\Exception $e) {
            \Log::error('Error loading project for edit: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data proyek'
            ], 500);
        }
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
            'media' => 'nullable|array|max:10',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4,mov|max:10240',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:students,id',
            'team_positions' => 'nullable|array',
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

            // Handle media uploads (add new media)
            if ($request->hasFile('media')) {
                $existingMediaCount = $project->media()->count();
                
                foreach ($request->file('media') as $index => $file) {
                    $path = $file->store('projects/' . $project->id, 'public');
                    
                    $project->media()->create([
                        'project_id' => $project->id,
                        'type' => str_starts_with($file->getMimeType(), 'image') ? 'image' : 'video',
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'order' => $existingMediaCount + $index,
                    ]);
                }
            }

            // Update team members if team project
            if ($project->type === 'team' && isset($validated['team_members'])) {
                // Get current leader
                $leader = $project->members()->where('role', 'leader')->first();
                
                // Get existing member IDs before deletion
                $existingMembers = $project->members()->where('role', 'member')->pluck('student_id')->toArray();
                
                // Delete old members (except leader)
                $project->members()->where('role', 'member')->delete();
                
                // Add new team members
                foreach ($validated['team_members'] as $index => $memberId) {
                    // Don't add leader as member
                    if ($leader && $leader->student_id != $memberId) {
                        $project->members()->create([
                            'student_id' => $memberId,
                            'role' => 'member',
                            'position' => $validated['team_positions'][$index] ?? 'Team Member',
                            'joined_at' => now(),
                        ]);
                        
                        // Notify newly added team members (not previously in the team)
                        if (!in_array($memberId, $existingMembers)) {
                            $memberStudent = Student::find($memberId);
                            if ($memberStudent) {
                                Notification::create([
                                    'user_id' => $memberStudent->user_id,
                                    'type' => 'team_mention',
                                    'notifiable_type' => Project::class,
                                    'notifiable_id' => $project->id,
                                    'data' => json_encode([
                                        'project_title' => $project->title,
                                        'project_slug' => $project->slug,
                                        'leader_name' => auth()->user()->full_name ?? auth()->user()->username,
                                        'message' => 'You have been added as a team member'
                                    ])
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully!',
                'project' => $project
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update project: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Project $project)
    {
        try {
            $this->authorize('delete', $project);

            // Delete media files
            foreach ($project->media as $media) {
                Storage::disk('public')->delete($media->file_path);
            }

            $project->delete();

            // Return JSON response for AJAX
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Proyek berhasil dihapus!'
                ]);
            }

            return redirect()->route('student.dashboard')
                ->with('success', 'Proyek berhasil dihapus!');
        } catch (\Exception $e) {
            \Log::error('Error deleting project: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus proyek: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus proyek!');
        }
    }
}