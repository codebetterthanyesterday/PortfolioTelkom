<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Student;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        
        // This Week Popular - Projects with most views in last 7 days (limit 9)
        $thisWeekPopular = Project::published()
            ->with(['student.user', 'media', 'categories', 'wishlists'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('view_count', 'desc')
            ->take(9)
            ->get();
        
        // Most Viewed - All time (limit 8)
        $mostViewed = Project::published()
            ->with(['student.user', 'media', 'categories', 'wishlists'])
            ->popular()
            ->take(8)
            ->get();
        
        // Featured - Most recent published (limit 8)
        $featured = Project::published()
            ->with(['student.user', 'media', 'categories', 'wishlists'])
            ->recent()
            ->take(8)
            ->get();
        
        // Top 3 Categories with most projects
        $topCategories = Category::withCount(['projects' => function($q) {
                $q->published();
            }])
            ->get()
            ->filter(function($category) {
                return $category->projects_count > 0;
            })
            ->sortByDesc('projects_count')
            ->take(3)
            ->values();
        
        // Get projects for each top category (4 each)
        $categoryProjects = [];
        foreach ($topCategories as $category) {
            $categoryProjects[$category->id] = Project::published()
                ->with(['student.user', 'media', 'categories', 'wishlists'])
                ->whereHas('categories', function($q) use ($category) {
                    $q->where('categories.id', $category->id);
                })
                ->recent()
                ->take(4)
                ->get();
        }
        
        // Most Experienced Students - Based on published project count (limit 12)
        $experiencedStudents = Student::with('user')
            ->withCount(['projects' => function($q) {
                $q->published();
            }])
            ->get()
            ->filter(function($student) {
                return $student->projects_count > 0;
            })
            ->sortByDesc('projects_count')
            ->take(12)
            ->values();
        
        // Check wishlist status for investors
        $wishlistedProjects = [];
        if (auth()->check() && auth()->user()->isInvestor()) {
            $wishlistedProjects = auth()->user()->investor->wishlists()
                ->pluck('project_id')
                ->toArray();
        }
        
        return view('pages.home', compact(
            'categories',
            'thisWeekPopular',
            'mostViewed',
            'featured',
            'topCategories',
            'categoryProjects',
            'experiencedStudents',
            'wishlistedProjects'
        ));
    }
    
    public function filterProjects(Request $request)
    {
        $categoryId = $request->get('category_id');
        
        // Filter projects based on category
        if ($categoryId && $categoryId !== 'all') {
            $thisWeekPopular = Project::published()
                ->with(['student.user', 'media', 'categories', 'wishlists'])
                ->whereHas('categories', function($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                })
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('view_count', 'desc')
                ->take(9)
                ->get();
            
            $mostViewed = Project::published()
                ->with(['student.user', 'media', 'categories', 'wishlists'])
                ->whereHas('categories', function($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                })
                ->popular()
                ->take(8)
                ->get();
            
            $featured = Project::published()
                ->with(['student.user', 'media', 'categories', 'wishlists'])
                ->whereHas('categories', function($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                })
                ->recent()
                ->take(8)
                ->get();
        } else {
            // Same as index method
            $thisWeekPopular = Project::published()
                ->with(['student.user', 'media', 'categories', 'wishlists'])
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('view_count', 'desc')
                ->take(9)
                ->get();
            
            $mostViewed = Project::published()
                ->with(['student.user', 'media', 'categories', 'wishlists'])
                ->popular()
                ->take(8)
                ->get();
            
            $featured = Project::published()
                ->with(['student.user', 'media', 'categories', 'wishlists'])
                ->recent()
                ->take(8)
                ->get();
        }
        
        $wishlistedProjects = [];
        if (auth()->check() && auth()->user()->isInvestor()) {
            $wishlistedProjects = auth()->user()->investor->wishlists()
                ->pluck('project_id')
                ->toArray();
        }
        
        return response()->json([
            'success' => true,
            'thisWeekPopular' => $thisWeekPopular,
            'mostViewed' => $mostViewed,
            'featured' => $featured,
            'wishlistedProjects' => $wishlistedProjects
        ]);
    }
}

