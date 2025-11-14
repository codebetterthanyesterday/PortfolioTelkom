<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;

class CommentPolicy
{
    /**
     * Determine if the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // User can delete their own comment
        if ($user->id === $comment->user_id) {
            return true;
        }
        
        // Project owner can delete comments on their project
        if ($user->student) {
            $project = $comment->project;
            return $project->student_id === $user->student->id;
        }
        
        return false;
    }
}
