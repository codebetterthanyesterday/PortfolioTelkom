<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'project_id',
    ];

    // Relationships
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
