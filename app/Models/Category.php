<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relationships
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_category')
            ->withTimestamps();
    }

    // Helper methods
    public function getProjectsCountAttribute(): int
    {
        return $this->projects()->count();
    }
}