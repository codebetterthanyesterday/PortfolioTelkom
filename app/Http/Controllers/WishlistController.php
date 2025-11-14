<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $investor = auth()->user()->investor;
        $wishlists = $investor->wishlistProjects()
            ->with(['student.user', 'media', 'categories'])
            ->latest('wishlists.created_at')
            ->paginate(12);

        return view('investor.wishlists', compact('wishlists'));
    }

    public function toggle(Project $project)
    {
        $investor = auth()->user()->investor;

        if ($investor->hasWishlisted($project)) {
            $investor->wishlists()->where('project_id', $project->id)->delete();
            $message = 'Project removed from wishlist!';
        } else {
            $investor->wishlists()->create([
                'project_id' => $project->id,
            ]);
            $message = 'Project added to wishlist!';
        }

        return back()->with('success', $message);
    }
}
