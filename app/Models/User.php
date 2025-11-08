<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // opsional
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name','email','password','role','phone',
        'is_approved','approved_at','approved_by',
        'avatar_path','school','major','profile_bio'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime'
        ];
    }

    // Student: projects they lead
    public function leadingProjects()
    {
        return $this->hasMany(Project::class, 'leader_id');
    }

    // Project creator
    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    // Member of group projects
    public function projectMemberships()
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot(['role_in_project', 'joined_at'])
            ->withTimestamps();
    }

    // Investor wishlist
    public function wishlists()
    {
        return $this->belongsToMany(Project::class, 'wishlists')->withTimestamps();
    }

    // Comments made by user
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Admin approval
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Admin logs
    public function adminActions()
    {
        return $this->hasMany(AdminAction::class, 'admin_id');
    }

    /** Helper: check roles */
    public function isStudent() { return $this->role === 'student'; }
    public function isInvestor() { return $this->role === 'investor'; }
    public function isAdmin() { return $this->role === 'admin'; }
}
