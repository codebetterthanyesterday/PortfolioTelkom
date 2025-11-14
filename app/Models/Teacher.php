<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nip',
        'email',
        'phone_number',
        'institution',
    ];

    // Relationships
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_teacher')
            ->withTimestamps();
    }
}