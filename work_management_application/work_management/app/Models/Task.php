<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'due_date',
        'status',
        'priority',
        'creator_id',
        'assigned_user_id',
        'team_id',
        'assigned_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the user that created the task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the user that is assigned to the task.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Get the user that is assigned to the task (alias).
     */
    public function user(): BelongsTo
    {
        return $this->assignedUser();
    }

    /**
     * Get the team this task is assigned to
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user who assigned this task
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get task performance record
     */
    public function performance(): HasOne
    {
        return $this->hasOne(TaskPerformance::class);
    }

    /**
     * Kiểm tra và điều chỉnh ngày hết hạn nếu trước ngày bắt đầu
     * Nếu due_date < start_date thì gán due_date = start_date và trả về thông báo
     */
    public static function validateAndAdjustDates(&$data)
    {
        $message = null;

        if (isset($data['start_date']) && isset($data['due_date'])) {
            $startDate = Carbon::parse($data['start_date']);
            $dueDate = Carbon::parse($data['due_date']);

            // Nếu ngày hết hạn trước ngày bắt đầu, gán ngày hết hạn = ngày bắt đầu
            if ($dueDate->lt($startDate)) {
                $data['due_date'] = $data['start_date'];
                $message = 'Ngày hết hạn không được trước ngày bắt đầu! Đã tự động điều chỉnh ngày hết hạn thành ngày bắt đầu.';
            }
        }

        return $message;
    }

    // Helper methods

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Get days until due date
     */
    public function getDaysUntilDue(): int
    {
        if (!$this->due_date) {
            return 0;
        }

        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }

    /**
     * Get status color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'secondary',
            'in_progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get priority color
     */
    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Scope for tasks assigned to specific team
     */
    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Scope for tasks assigned by specific user
     */
    public function scopeAssignedBy($query, int $userId)
    {
        return $query->where('assigned_by', $userId);
    }

    /**
     * Scope for overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
    }

    /**
     * Scope for completed tasks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending tasks
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for in progress tasks
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
