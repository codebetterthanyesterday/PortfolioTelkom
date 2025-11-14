<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Expertise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expertise) {
            if (empty($expertise->slug)) {
                $expertise->slug = Str::slug($expertise->name);
            }
        });
    }

    // Relationships
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_expertise')
            ->withTimestamps();
    }
}