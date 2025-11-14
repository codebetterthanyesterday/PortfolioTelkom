<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'title',
        'slug',
        'price',
        'type',
        'description',
        'status',
        'view_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'view_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function owner()
    {
        return $this->student();
    }

    public function media()
    {
        return $this->hasMany(ProjectMedia::class)->orderBy('order');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'project_category')
            ->withTimestamps();
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'project_subject')
            ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'project_teacher')
            ->withTimestamps();
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function teamMembers()
    {
        return $this->belongsToMany(Student::class, 'project_members')
            ->withPivot('role', 'position', 'joined_at')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(Investor::class, 'wishlists')
            ->withTimestamps();
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeIndividual($query)
    {
        return $query->where('type', 'individual');
    }

    public function scopeTeam($query)
    {
        return $query->where('type', 'team');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('view_count', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Helper methods
    public function isTeam(): bool
    {
        return $this->type === 'team';
    }

    public function isIndividual(): bool
    {
        return $this->type === 'individual';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getLeader()
    {
        return $this->members()->where('role', 'leader')->first();
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function getThumbnailAttribute()
    {
        return $this->media()->first();
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}