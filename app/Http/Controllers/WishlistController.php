<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Wishlist;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    /**
     * Toggle wishlist status for a project
     */
    public function toggle(Project $project)
    {
        $investor = auth()->user()->investor;
        
        // Check if already wishlisted (only non-deleted)
        $wishlist = Wishlist::where('investor_id', $investor->id)
            ->where('project_id', $project->id)
            ->first();
        
        if ($wishlist) {
            // Remove from wishlist (soft delete)
            $wishlist->delete();
            
            return response()->json([
                'success' => true,
                'isWishlisted' => false,
                'message' => 'Dihapus dari wishlist'
            ]);
        } else {
            // Check if previously deleted, restore it
            $trashedWishlist = Wishlist::withTrashed()
                ->where('investor_id', $investor->id)
                ->where('project_id', $project->id)
                ->onlyTrashed()
                ->first();
            
            if ($trashedWishlist) {
                // Restore the previously deleted wishlist
                $trashedWishlist->restore();
                
                return response()->json([
                    'success' => true,
                    'isWishlisted' => true,
                    'message' => 'Ditambahkan ke wishlist'
                ]);
            }
            
            // Add to wishlist
            try {
                Wishlist::create([
                    'investor_id' => $investor->id,
                    'project_id' => $project->id,
                ]);
                
                // Create notification for the project owner (student)
                Notification::create([
                    'user_id' => $project->student->user_id,
                    'type' => 'project_wishlisted',
                    'notifiable_type' => Project::class,
                    'notifiable_id' => $project->id,
                    'priority' => Notification::PRIORITY_LOW,
                    'category' => Notification::CATEGORY_PROJECT,
                    'data' => [
                        'investor_name' => auth()->user()->full_name ?? auth()->user()->username,
                        'project_title' => $project->title,
                        'project_slug' => $project->slug,
                        'message' => 'menambahkan proyek Anda ke wishlist',
                    ],
                ]);
                
                return response()->json([
                    'success' => true,
                    'isWishlisted' => true,
                    'message' => 'Ditambahkan ke wishlist'
                ]);
            } catch (\Exception $e) {
                // Handle duplicate or other errors gracefully
                $errorMessage = $e->getMessage();
                
                // Check for duplicate entry (MySQL, PostgreSQL, SQLite)
                if (strpos($errorMessage, 'Duplicate entry') !== false || 
                    strpos($errorMessage, 'UNIQUE constraint') !== false ||
                    strpos($errorMessage, 'Unique violation') !== false ||
                    strpos($errorMessage, 'duplicate key value') !== false) {
                    // Already exists, treat as success
                    return response()->json([
                        'success' => true,
                        'isWishlisted' => true,
                        'message' => 'Sudah ada di wishlist'
                    ]);
                }
                
                // Log the error for debugging
                \Log::error('Wishlist creation failed: ' . $errorMessage);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan ke wishlist'
                ], 500);
            }
        }
    }
    
    /**
     * Remove a project from wishlist (for profile page)
     */
    public function remove(Project $project)
    {
        $investor = auth()->user()->investor;
        
        $wishlist = Wishlist::where('investor_id', $investor->id)
            ->where('project_id', $project->id)
            ->first();
        
        if ($wishlist) {
            $wishlist->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Proyek dihapus dari wishlist'
                ]);
            }
            
            return redirect()->back()->with('success', 'Proyek dihapus dari wishlist');
        }
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Proyek tidak ditemukan di wishlist'
            ], 404);
        }
        
        return redirect()->back()->with('error', 'Proyek tidak ditemukan di wishlist');
    }
}
