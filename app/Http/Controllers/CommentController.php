<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = $project->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        // Load necessary relationships
        $comment->load('user', 'parent.user');
        $project->load('student.user', 'members.student.user');

        // Handle notifications
        $this->createNotifications($comment, $project);

        return back();
    }

    protected function createNotifications(Comment $comment, Project $project)
    {
        $commenterUserId = $comment->user_id;
        $notifiedUserIds = [];

        // Case 1: If this is a reply to a parent comment
        if ($comment->parent_id) {
            $parentComment = $comment->parent;
            
            // Notify the parent comment author (if not the same user)
            if ($parentComment->user_id !== $commenterUserId) {
                Notification::createNotification([
                    'user_id' => $parentComment->user_id,
                    'type' => Notification::TYPE_COMMENT_REPLY,
                    'notifiable_type' => Comment::class,
                    'notifiable_id' => $comment->id,
                    'priority' => Notification::PRIORITY_MEDIUM,
                    'category' => Notification::CATEGORY_SOCIAL,
                    'data' => [
                        'project_id' => $project->id,
                        'project_title' => $project->title,
                        'project_slug' => $project->slug,
                        'comment_id' => $comment->id,
                        'parent_comment_id' => $comment->parent_id,
                        'commenter_name' => $comment->user->full_name ?? $comment->user->username,
                        'commenter_avatar' => $comment->user->avatar,
                        'comment_excerpt' => substr($comment->content, 0, 100),
                        'message' => 'replied to your comment'
                    ]
                ]);
                
                $notifiedUserIds[] = $parentComment->user_id;
            }
        }

        // Check for @mentions in comment content
        $this->handleMentions($comment, $project, $notifiedUserIds);

        // Case 2: Notify project owner/members about new comment
        if ($project->isIndividual()) {
            // For individual projects, notify the project owner only
            $projectOwnerId = $project->student->user_id;
            
            if ($projectOwnerId !== $commenterUserId && !in_array($projectOwnerId, $notifiedUserIds)) {
                Notification::createNotification([
                    'user_id' => $projectOwnerId,
                    'type' => Notification::TYPE_PROJECT_COMMENT,
                    'notifiable_type' => Comment::class,
                    'notifiable_id' => $comment->id,
                    'priority' => Notification::PRIORITY_MEDIUM,
                    'category' => Notification::CATEGORY_PROJECT,
                    'data' => [
                        'project_id' => $project->id,
                        'project_title' => $project->title,
                        'project_slug' => $project->slug,
                        'comment_id' => $comment->id,
                        'commenter_name' => $comment->user->full_name ?? $comment->user->username,
                        'commenter_avatar' => $comment->user->avatar,
                        'comment_excerpt' => substr($comment->content, 0, 100),
                        'message' => 'commented on your project'
                    ]
                ]);
            }
        } else {
            // For team projects, notify all team members (including leader)
            $teamMemberUserIds = $project->members()
                ->with('student.user')
                ->get()
                ->pluck('student.user_id')
                ->unique()
                ->filter(function($userId) use ($commenterUserId, $notifiedUserIds) {
                    // Don't notify the commenter and those already notified
                    return $userId !== $commenterUserId && !in_array($userId, $notifiedUserIds);
                });

            foreach ($teamMemberUserIds as $memberId) {
                Notification::createNotification([
                    'user_id' => $memberId,
                    'type' => Notification::TYPE_PROJECT_COMMENT,
                    'notifiable_type' => Comment::class,
                    'notifiable_id' => $comment->id,
                    'priority' => Notification::PRIORITY_MEDIUM,
                    'category' => Notification::CATEGORY_TEAM,
                    'data' => [
                        'project_id' => $project->id,
                        'project_title' => $project->title,
                        'project_slug' => $project->slug,
                        'comment_id' => $comment->id,
                        'commenter_name' => $comment->user->full_name ?? $comment->user->username,
                        'commenter_avatar' => $comment->user->avatar,
                        'comment_excerpt' => substr($comment->content, 0, 100),
                        'message' => 'commented on your team project'
                    ]
                ]);
            }
        }
    }

    /**
     * Handle @mentions in comments
     */
    protected function handleMentions(Comment $comment, Project $project, array &$notifiedUserIds)
    {
        // Extract @mentions from comment content
        preg_match_all('/@(\w+)/', $comment->content, $mentions);
        
        if (!empty($mentions[1])) {
            $usernames = $mentions[1];
            
            // Find users by username
            $mentionedUsers = \App\Models\User::whereIn('username', $usernames)->get();
            
            foreach ($mentionedUsers as $mentionedUser) {
                // Don't notify the commenter or already notified users
                if ($mentionedUser->id !== $comment->user_id && !in_array($mentionedUser->id, $notifiedUserIds)) {
                    Notification::createNotification([
                        'user_id' => $mentionedUser->id,
                        'type' => Notification::TYPE_MENTION_IN_COMMENT,
                        'notifiable_type' => Comment::class,
                        'notifiable_id' => $comment->id,
                        'priority' => Notification::PRIORITY_HIGH,
                        'category' => Notification::CATEGORY_SOCIAL,
                        'data' => [
                            'project_id' => $project->id,
                            'project_title' => $project->title,
                            'project_slug' => $project->slug,
                            'comment_id' => $comment->id,
                            'commenter_name' => $comment->user->full_name ?? $comment->user->username,
                            'commenter_avatar' => $comment->user->avatar,
                            'comment_excerpt' => substr($comment->content, 0, 100),
                            'message' => 'mentioned you in a comment'
                        ]
                    ]);
                    
                    $notifiedUserIds[] = $mentionedUser->id;
                }
            }
        }
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully!');
    }
}
