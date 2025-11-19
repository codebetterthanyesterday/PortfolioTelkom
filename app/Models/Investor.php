<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'industry',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProjects()
    {
        return $this->belongsToMany(Project::class, 'wishlists')
            ->wherePivotNull('deleted_at')
            ->withTimestamps();
    }

    // Helper methods
    public function hasWishlisted(Project $project): bool
    {
        return $this->wishlists()->where('project_id', $project->id)->exists();
    }
}