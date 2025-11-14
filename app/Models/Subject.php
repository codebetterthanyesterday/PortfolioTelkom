<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    // Relationships
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_subject')
            ->withTimestamps();
    }
}