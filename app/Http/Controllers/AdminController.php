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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

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
            ->whereHas('project')
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
        return view('pages.admin.users');
    }

    public function filterUsers(Request $request)
    {
        $query = User::with(['student', 'investor'])
            ->withCount(['comments']);

        // Show trashed users if requested
        if ($request->show_deleted === 'true' || $request->show_deleted === '1') {
            $query->onlyTrashed();
        } elseif ($request->show_deleted === 'all') {
            $query->withTrashed();
        }

        // Role filter - apply first to handle admin inclusion properly
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'ILIKE', "%{$search}%")
                  ->orWhere('username', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhereHas('student', function($q) use ($search) {
                      $q->where('student_id', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
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

        $users = $query->paginate($request->get('per_page', 10));

        return response()->json($users);
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
        return view('pages.admin.comments');
    }

    public function filterComments(Request $request)
    {
        $query = Comment::with(['user.student', 'user.investor', 'project.student.user'])
            ->withCount('replies');

        // Show trashed comments if requested
        if ($request->show_deleted === 'true' || $request->show_deleted === '1') {
            $query->onlyTrashed();
        } elseif ($request->show_deleted === 'all') {
            $query->withTrashed();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'ILIKE', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('full_name', 'ILIKE', "%{$search}%")
                        ->orWhere('username', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%");
                  })
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('title', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // Comment type filter (parent/reply)
        if ($request->filled('type')) {
            if ($request->type === 'parent') {
                $query->whereNull('parent_id');
            } elseif ($request->type === 'reply') {
                $query->whereNotNull('parent_id');
            }
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

        $comments = $query->paginate($request->get('per_page', 10));

        return response()->json($comments);
    }

    public function wishlists()
    {
        return view('pages.admin.wishlist');
    }

    public function filterWishlists(Request $request)
    {
        $query = Wishlist::with(['investor.user', 'project.student.user', 'project.media']);

        // Show trashed wishlists if requested
        if ($request->show_deleted === 'true' || $request->show_deleted === '1') {
            $query->onlyTrashed();
        } elseif ($request->show_deleted === 'all') {
            $query->withTrashed();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('investor.user', function($q) use ($search) {
                    $q->where('full_name', 'ILIKE', "%{$search}%")
                      ->orWhere('username', 'ILIKE', "%{$search}%")
                      ->orWhere('email', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('investor', function($q) use ($search) {
                    $q->where('company_name', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('project', function($q) use ($search) {
                    $q->where('title', 'ILIKE', "%{$search}%");
                });
            });
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

        $wishlists = $query->paginate($request->get('per_page', 10));

        return response()->json($wishlists);
    }

    public function deleteWishlist(Wishlist $wishlist)
    {
        $wishlist->delete();
        return response()->json(['success' => true, 'message' => 'Wishlist moved to trash successfully.']);
    }

    public function restoreWishlist($id)
    {
        $wishlist = Wishlist::withTrashed()->findOrFail($id);
        $wishlist->restore();
        return response()->json(['success' => true, 'message' => 'Wishlist restored successfully.']);
    }

    public function forceDeleteWishlist($id)
    {
        $wishlist = Wishlist::withTrashed()->findOrFail($id);
        $wishlist->forceDelete();
        return response()->json(['success' => true, 'message' => 'Wishlist permanently deleted.']);
    }

    public function deleteAllWishlists()
    {
        try {
            $deletedCount = Wishlist::whereNull('deleted_at')->count();
            Wishlist::whereNull('deleted_at')->delete();
            return response()->json([
                'success' => true,
                'message' => 'All wishlists moved to trash successfully.',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete all wishlists: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restoreAllWishlists()
    {
        try {
            $restoredCount = Wishlist::onlyTrashed()->count();
            Wishlist::onlyTrashed()->restore();
            return response()->json([
                'success' => true,
                'message' => 'All wishlists restored successfully.',
                'restored_count' => $restoredCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore all wishlists: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser(User $user)
    {
        if ($user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete admin user.'], 403);
        }

        $user->delete();
        return response()->json(['success' => true, 'message' => 'User moved to trash successfully.']);
    }

    public function restoreUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        if ($user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Cannot modify admin user.'], 403);
        }
        
        $user->restore();
        return response()->json(['success' => true, 'message' => 'User restored successfully.']);
    }

    public function forceDeleteUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        if ($user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete admin user.'], 403);
        }
        
        $user->forceDelete();
        return response()->json(['success' => true, 'message' => 'User permanently deleted.']);
    }

    public function deleteAllUsers()
    {
        try {
            // Exclude admins from bulk deletion
            $deletedCount = User::whereNull('deleted_at')
                ->where('role', '!=', 'admin')
                ->count();

            User::whereNull('deleted_at')
                ->where('role', '!=', 'admin')
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'All non-admin users moved to trash successfully.',
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete all users: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function restoreAllUsers()
    {
        try {
            $restoredCount = User::onlyTrashed()->count();
            User::onlyTrashed()->restore();

            return response()->json([
                'success' => true,
                'message' => 'All users restored successfully.',
                'restored_count' => $restoredCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore all users: ' . $e->getMessage(),
            ], 500);
        }
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

    public function deleteAllProjects()
    {
            try {
                // Get all active (non-deleted) projects
                $deletedCount = Project::whereNull('deleted_at')->count();
            
                // Soft delete all active projects
                Project::whereNull('deleted_at')->delete();
            
                return response()->json([
                    'success' => true, 
                    'message' => 'All projects have been deleted successfully.',
                    'deleted_count' => $deletedCount
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to delete all projects: ' . $e->getMessage()
                ], 500);
            }
        }

        public function restoreAllProjects()
        {
            try {
                // Get all soft-deleted projects
                $restoredCount = Project::onlyTrashed()->count();
            
                // Restore all soft-deleted projects
                Project::onlyTrashed()->restore();
            
                return response()->json([
                    'success' => true, 
                    'message' => 'All projects have been restored successfully.',
                    'restored_count' => $restoredCount
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to restore all projects: ' . $e->getMessage()
                ], 500);
            }
    }

    public function deleteComment(Comment $comment)
    {
        $comment->delete();
        return response()->json(['success' => true, 'message' => 'Comment moved to trash successfully.']);
    }

    public function restoreComment($id)
    {
        $comment = Comment::withTrashed()->findOrFail($id);
        $comment->restore();
        return response()->json(['success' => true, 'message' => 'Comment restored successfully.']);
    }

    public function forceDeleteComment($id)
    {
        $comment = Comment::withTrashed()->findOrFail($id);
        $comment->forceDelete();
        return response()->json(['success' => true, 'message' => 'Comment permanently deleted.']);
    }

    public function deleteAllComments()
    {
        try {
            // Get all active (non-deleted) comments
            $deletedCount = Comment::whereNull('deleted_at')->count();
            
            // Soft delete all active comments
            Comment::whereNull('deleted_at')->delete();
            
            return response()->json([
                'success' => true, 
                'message' => 'All comments have been deleted successfully.',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to delete all comments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restoreAllComments()
    {
        try {
            // Get all soft-deleted comments
            $restoredCount = Comment::onlyTrashed()->count();
            
            // Restore all soft-deleted comments
            Comment::onlyTrashed()->restore();
            
            return response()->json([
                'success' => true, 
                'message' => 'All comments have been restored successfully.',
                'restored_count' => $restoredCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to restore all comments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleUserStatus($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        if ($user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Cannot modify admin user status.'], 403);
        }

        if ($user->trashed()) {
            $user->restore();
            return response()->json(['success' => true, 'message' => 'User restored successfully.']);
        } else {
            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deactivated successfully.']);
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

    /**
     * Reset the entire application database similar to `php artisan migrate:fresh`.
     * Preserves existing admin users and keeps current admin session valid.
     */
    public function resetDatabase(Request $request)
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $phrase = $request->input('phrase');
        $requiredPhrase = 'HAPUS SEMUA DATA APLIKASI';
        if ($phrase !== $requiredPhrase) {
            return response()->json([
                'success' => false,
                'message' => 'Konfirmasi tidak cocok. Ketik tepat: ' . $requiredPhrase
            ], 422);
        }

        // Collect all current admin users to preserve (including soft-deleted ones if any)
        $admins = User::withTrashed()->where('role', 'admin')->get();
        if ($admins->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan akun admin untuk dipertahankan.'
            ], 500);
        }

        $adminData = $admins->map(function ($admin) {
            return [
                'id' => $admin->id, // preserve ID so existing session remains valid
                'full_name' => $admin->full_name,
                'username' => $admin->username,
                'email' => $admin->email,
                'password' => $admin->password, // already hashed
                'role' => $admin->role,
                'bio' => $admin->bio,
                'email_verified_at' => $admin->email_verified_at,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        try {
            // Perform migrate:fresh (drops all tables & re-runs migrations)
            Artisan::call('migrate:fresh', ['--force' => true]);

            // Reinsert admin users (bypass model events for speed & to preserve IDs)
            DB::table('users')->insert($adminData->toArray());

            // Optionally you could run seeders EXCEPT admin seeder if it would duplicate.
            // Artisan::call('db:seed', ['--force' => true]); // (commented out intentionally)

            return response()->json([
                'success' => true,
                'message' => 'Database berhasil di-reset. Semua data non-admin dihapus. Admin dipertahankan.',
                'admin_preserved' => $admins->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset database: ' . $e->getMessage(),
            ], 500);
        }
    }
}
