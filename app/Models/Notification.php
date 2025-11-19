<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
        'priority',
        'category',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Notification types
    const TYPE_PROJECT_COMMENT = 'project_comment';
    const TYPE_COMMENT_REPLY = 'comment_reply';
    const TYPE_TEAM_MENTION = 'team_mention';
    const TYPE_PROJECT_LIKED = 'project_liked';
    const TYPE_PROJECT_SHARED = 'project_shared';
    const TYPE_MEMBER_JOINED = 'member_joined';
    const TYPE_MEMBER_LEFT = 'member_left';
    const TYPE_PROJECT_STATUS_CHANGED = 'project_status_changed';
    const TYPE_MENTION_IN_COMMENT = 'mention_in_comment';
    const TYPE_PROJECT_WISHLISTED = 'project_wishlisted';

    // Priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Categories
    const CATEGORY_SOCIAL = 'social';
    const CATEGORY_PROJECT = 'project';
    const CATEGORY_TEAM = 'team';
    const CATEGORY_SYSTEM = 'system';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeOlderThan($query, $days)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    // Helper methods
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function isHighPriority(): bool
    {
        return in_array($this->priority, [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    public function isUrgent(): bool
    {
        return $this->priority === self::PRIORITY_URGENT;
    }

    public function isRecent(): bool
    {
        return $this->created_at->isAfter(now()->subHours(24));
    }

    public function markAsReadAndRedirect(): string
    {
        $this->markAsRead();
        return $this->getUrl();
    }

    /**
     * Get formatted notification message
     */
    public function getMessage(): string
    {
        $data = $this->data;
        
        switch ($this->type) {
            case self::TYPE_PROJECT_COMMENT:
                return "{$data['commenter_name']} {$data['message']}";
            
            case self::TYPE_COMMENT_REPLY:
                return "{$data['commenter_name']} {$data['message']}";
            
            case self::TYPE_TEAM_MENTION:
                return "{$data['leader_name']} {$data['message']}";
            
            case self::TYPE_PROJECT_LIKED:
                return "{$data['user_name']} liked your project";
            
            case self::TYPE_PROJECT_SHARED:
                return "{$data['user_name']} shared your project";
            
            case self::TYPE_MEMBER_JOINED:
                return "{$data['member_name']} joined your team project";
            
            case self::TYPE_MEMBER_LEFT:
                return "{$data['member_name']} left your team project";
            
            case self::TYPE_PROJECT_STATUS_CHANGED:
                return "Your project status changed to {$data['new_status']}";
            
            case self::TYPE_MENTION_IN_COMMENT:
                return "{$data['commenter_name']} mentioned you in a comment";
            
            case self::TYPE_PROJECT_WISHLISTED:
                return "{$data['investor_name']} {$data['message']}";
            
            default:
                return $data['message'] ?? 'You have a new notification';
        }
    }

    /**
     * Get notification URL
     */
    public function getUrl(): string
    {
        $data = $this->data;
        
        switch ($this->type) {
            case 'project_comment':
            case 'comment_reply':
                return route('projects.show', ['project' => $data['project_slug']]) . '#comment-' . $data['comment_id'];
            
            case 'team_mention':
                return route('projects.show', ['project' => $data['project_slug']]);
            
            case 'project_wishlisted':
                return route('projects.show', ['project' => $data['project_slug']]);
            
            default:
                return '#';
        }
    }

    /**
     * Get notification icon class
     */
    public function getIconClass(): string
    {
        switch ($this->type) {
            case self::TYPE_PROJECT_COMMENT:
                return 'fa-comment';
            
            case self::TYPE_COMMENT_REPLY:
                return 'fa-reply';
            
            case self::TYPE_TEAM_MENTION:
                return 'fa-users';
            
            case self::TYPE_PROJECT_LIKED:
                return 'fa-heart';
            
            case self::TYPE_PROJECT_SHARED:
                return 'fa-share';
            
            case self::TYPE_MEMBER_JOINED:
                return 'fa-user-plus';
            
            case self::TYPE_MEMBER_LEFT:
                return 'fa-user-minus';
            
            case self::TYPE_PROJECT_STATUS_CHANGED:
                return 'fa-info-circle';
            
            case self::TYPE_MENTION_IN_COMMENT:
                return 'fa-at';
            
            case self::TYPE_PROJECT_WISHLISTED:
                return 'fa-heart';
            
            default:
                return 'fa-bell';
        }
    }

    /**
     * Get notification title
     */
    public function getTitle(): string
    {
        $data = $this->data;
        
        switch ($this->type) {
            case 'project_comment':
            case 'comment_reply':
                return $data['project_title'] ?? 'Project';
            
            case 'team_mention':
                return $data['project_title'] ?? 'Team Project';
            
            case 'project_wishlisted':
                return $data['project_title'] ?? 'Project';
            
            default:
                return 'Notification';
        }
    }

    /**
     * Get time ago string
     */
    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get notification priority color
     */
    public function getPriorityColor(): string
    {
        switch ($this->priority) {
            case self::PRIORITY_URGENT:
                return 'text-red-600';
            case self::PRIORITY_HIGH:
                return 'text-orange-600';
            case self::PRIORITY_MEDIUM:
                return 'text-yellow-600';
            case self::PRIORITY_LOW:
            default:
                return 'text-gray-600';
        }
    }

    /**
     * Get notification category color
     */
    public function getCategoryColor(): string
    {
        switch ($this->category) {
            case self::CATEGORY_SOCIAL:
                return 'bg-blue-100 text-blue-800';
            case self::CATEGORY_PROJECT:
                return 'bg-green-100 text-green-800';
            case self::CATEGORY_TEAM:
                return 'bg-purple-100 text-purple-800';
            case self::CATEGORY_SYSTEM:
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Get notification summary for API
     */
    public function toSummaryArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'icon' => $this->getIconClass(),
            'url' => $this->getUrl(),
            'is_read' => $this->isRead(),
            'is_urgent' => $this->isUrgent(),
            'priority' => $this->priority,
            'category' => $this->category,
            'time_ago' => $this->getTimeAgo(),
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Create a notification with default values
     */
    public static function createNotification(array $data): self
    {
        return self::create(array_merge([
            'priority' => self::PRIORITY_MEDIUM,
            'category' => self::CATEGORY_SYSTEM,
        ], $data));
    }
}