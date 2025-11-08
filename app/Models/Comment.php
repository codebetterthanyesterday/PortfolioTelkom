<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id','user_id','parent_id','body','is_hidden'
    ];

    protected $casts = [
        'is_hidden' => 'boolean'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Parent comment (for replies)
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // Replies
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
