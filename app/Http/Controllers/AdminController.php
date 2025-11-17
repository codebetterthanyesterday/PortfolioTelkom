<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Comment;
use App\Models\Wishlist;
use App\Models\Student;
use App\Models\Investor;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Statistics
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_investors' => User::where('role', 'investor')->count(),
            'total_projects' => Project::count(),
            'published_projects' => Project::where('status', 'published')->count(),
            'total_comments' => Comment::count(),
            'total_wishlists' => Wishlist::count(),
        ];

        // Recent data
        $recent_users = User::with(['student', 'investor'])
            ->whereIn('role', ['student', 'investor'])
            ->latest()
            ->take(5)
            ->get();

        $recent_projects = Project::with(['student.user', 'categories', 'media'])
            ->withCount(['comments', 'wishlists'])
            ->latest()
            ->take(5)
            ->get();

        $recent_comments = Comment::with(['user', 'project'])
            ->latest()
            ->take(5)
            ->get();

        // Monthly growth calculations (compared to last month)
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;
        
        $projectsThisMonth = Project::whereMonth('created_at', $currentMonth)->count();
        $projectsLastMonth = Project::whereMonth('created_at', $lastMonth)->count();
        $projectGrowth = $projectsLastMonth > 0 ? round((($projectsThisMonth - $projectsLastMonth) / $projectsLastMonth) * 100) : 0;
        
        $usersThisMonth = User::whereMonth('created_at', $currentMonth)->count();
        $usersLastMonth = User::whereMonth('created_at', $lastMonth)->count();
        $userGrowth = $usersLastMonth > 0 ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100) : 0;
        
        $commentsThisMonth = Comment::whereMonth('created_at', $currentMonth)->count();
        $commentsLastMonth = Comment::whereMonth('created_at', $lastMonth)->count();
        $commentGrowth = $commentsLastMonth > 0 ? round((($commentsThisMonth - $commentsLastMonth) / $commentsLastMonth) * 100) : 0;
        
        $wishlistsThisMonth = Wishlist::whereMonth('created_at', $currentMonth)->count();
        $wishlistsLastMonth = Wishlist::whereMonth('created_at', $lastMonth)->count();
        $wishlistGrowth = $wishlistsLastMonth > 0 ? round((($wishlistsThisMonth - $wishlistsLastMonth) / $wishlistsLastMonth) * 100) : 0;

        $growth = [
            'projects' => $projectGrowth,
            'users' => $userGrowth,
            'comments' => $commentGrowth,
            'wishlists' => $wishlistGrowth,
        ];

        return view('pages.admin.dashboard', compact('stats', 'recent_users', 'recent_projects', 'recent_comments', 'growth'));
    }

    public function users()
    {
        $users = User::with(['student', 'investor'])
            ->withCount(['comments'])
            ->latest()
            ->paginate(20);

        return view('pages.admin.users', compact('users'));
    }

    public function projects()
    {
        $categories = Category::all();
        return view('pages.admin.projects', compact('categories'));
    }

    public function filterProjects(Request $request)
    {
        $query = Project::with(['student.user', 'categories', 'media'])
            ->withCount(['comments', 'wishlists']);

        // Show trashed projects if requested
        if ($request->show_deleted === 'true' || $request->show_deleted === '1') {
            $query->onlyTrashed();
        } elseif ($request->show_deleted === 'all') {
            $query->withTrashed();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%")
                  ->orWhereHas('student.user', function($q) use ($search) {
                      $q->where('full_name', 'ILIKE', "%{$search}%")
                        ->orWhere('username', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortField = $request->get('sort_field', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $projects = $query->paginate($request->get('per_page', 10));

        return response()->json($projects);
    }

    public function comments()
    {
        $comments = Comment::with(['user', 'project'])
            ->latest()
            ->paginate(20);

        return view('pages.admin.comments', compact('comments'));
    }

    public function wishlists()
    {
        $wishlists = Wishlist::with(['investor.user', 'project'])
            ->latest()
            ->paginate(20);

        return view('pages.admin.wishlist', compact('wishlists'));
    }

    public function deleteUser(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete admin user.');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    public function deleteProject(Project $project)
    {
        $project->delete();
        return response()->json(['success' => true, 'message' => 'Project moved to trash successfully.']);
    }

    public function restoreProject($id)
    {
        $project = Project::withTrashed()->findOrFail($id);
        $project->restore();
        return response()->json(['success' => true, 'message' => 'Project restored successfully.']);
    }

    public function forceDeleteProject($id)
    {
        $project = Project::withTrashed()->findOrFail($id);
        $project->forceDelete();
        return response()->json(['success' => true, 'message' => 'Project permanently deleted.']);
    }

    public function deleteComment(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully.');
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot modify admin user status.');
        }

        if ($user->trashed()) {
            $user->restore();
            return back()->with('success', 'User activated successfully.');
        } else {
            $user->delete();
            return back()->with('success', 'User deactivated successfully.');
        }
    }

    public function toggleProjectStatus(Project $project)
    {
        // Toggle is_published status or soft delete
        if ($project->trashed()) {
            $project->restore();
            return back()->with('success', 'Project restored successfully.');
        } else {
            $project->delete();
            return back()->with('success', 'Project archived successfully.');
        }
    }
}
