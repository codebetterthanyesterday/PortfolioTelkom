<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'user_id',
        'parent_id',
        'content',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function allReplies()
    {
        return $this->replies()->with('allReplies');
    }

    // Scopes
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }

    // Helper methods
    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    public function getRepliesCountAttribute(): int
    {
        return $this->replies()->count();
    }
}
