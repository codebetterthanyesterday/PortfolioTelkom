<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Comment;
use App\Models\Wishlist;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => User::students()->count(),
            'total_investors' => User::investors()->count(),
            'total_projects' => Project::count(),
            'published_projects' => Project::published()->count(),
            'total_comments' => Comment::count(),
            'total_wishlists' => Wishlist::count(),
        ];

        $recentProjects = Project::with(['student.user', 'media'])
            ->latest()
            ->limit(5)
            ->get();

        $recentUsers = User::latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentProjects', 'recentUsers'));
    }
}
