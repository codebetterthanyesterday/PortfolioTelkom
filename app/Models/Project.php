<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title','slug','description','type',
        'leader_id','created_by',
        'status','visibility','published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot(['role_in_project', 'joined_at'])
            ->withTimestamps();
    }

    public function media()
    {
        return $this->hasMany(ProjectMedia::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->where('parent_id', null);
    }

    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    /** Helpers **/
    public function isGroupProject()
    {
        return in_array($this->type, [
            'school_group',
            'team_outside_school'
        ]);
    }

    public function isSchoolGroupProject()
    {
        return $this->type === 'school_group';
    }


    public function isIndividualSchoolProject()
    {
        return $this->type === 'individual';
    }

    public function isPersonalOutsideSchoolProject()
    {
        return $this->type === 'personal_outside_school';
    }

    public function isTeamOutsideSchoolProject()
    {
        return $this->type === 'team_outside_school';
    }
}
