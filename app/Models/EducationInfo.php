<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationInfo extends Model
{
    use HasFactory;

    protected $table = 'education_info';

    protected $fillable = [
        'student_id',
        'institution_name',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
        'is_current',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('is_current', 'desc')
            ->orderBy('start_date', 'desc');
    }

    // Helper methods
    public function getPeriodAttribute(): string
    {
        $start = $this->start_date?->format('M Y') ?? 'Unknown';
        $end = $this->is_current ? 'Present' : ($this->end_date?->format('M Y') ?? 'Unknown');
        return "{$start} - {$end}";
    }
}
