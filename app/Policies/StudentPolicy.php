<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Student;

class StudentPolicy
{
    /**
     * Determine if the user can update the student profile.
     */
    public function update(User $user, Student $student): bool
    {
        // User can only update their own profile
        return $user->id === $student->user_id;
    }
}