<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    /**
     * Determine if the user can view the project.
     */
    public function view(?User $user, Project $project): bool
    {
        // Everyone can view published projects
        if ($project->isPublished()) {
            return true;
        }
        
        // Only owner can view unpublished projects
        return $user && $user->student && $user->student->id === $project->student_id;
    }

    /**
     * Determine if the user can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        // Only the project owner (student) or team leader can update
        if ($user->student && $user->student->id === $project->student_id) {
            return true;
        }
        
        // Check if user is team leader
        if ($project->isTeam()) {
            $leader = $project->getLeader();
            return $leader && $user->student && $leader->student_id === $user->student->id;
        }
        
        return false;
    }

    /**
     * Determine if the user can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        // Only the project owner can delete
        return $user->student && $user->student->id === $project->student_id;
    }
}
