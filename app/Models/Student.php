<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function expertises()
    {
        return $this->belongsToMany(Expertise::class, 'student_expertise')
            ->withTimestamps();
    }

    public function educationInfo()
    {
        return $this->hasMany(EducationInfo::class);
    }

    public function projectMemberships()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function memberProjects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('role', 'position', 'joined_at')
            ->withTimestamps();
    }

    // Helper methods
    public function getWhatsappLink(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->user->phone_number);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        return "https://wa.me/{$phone}";
    }
}
