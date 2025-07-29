<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'completion_time_hours',
        'quality_score',
        'on_time',
        'days_overdue',
        'manager_rating',
        'self_rating',
        'feedback',
        'manager_feedback'
    ];

    protected $casts = [
        'completion_time_hours' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'on_time' => 'boolean',
        'days_overdue' => 'integer',
        'manager_rating' => 'decimal:2',
        'self_rating' => 'decimal:2',
    ];

    /**
     * Get the task
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get performance score (0-100)
     */
    public function getPerformanceScore(): float
    {
        $score = 0;
        $factors = 0;

        // On-time completion (30%)
        if ($this->on_time) {
            $score += 30;
        } else {
            // Deduct points for being late
            $latePenalty = min($this->days_overdue * 2, 30);
            $score += max(0, 30 - $latePenalty);
        }
        $factors += 30;

        // Quality score (40%)
        if ($this->quality_score !== null) {
            $score += ($this->quality_score / 5) * 40;
            $factors += 40;
        }

        // Manager rating (30%)
        if ($this->manager_rating !== null) {
            $score += ($this->manager_rating / 5) * 30;
            $factors += 30;
        }

        return $factors > 0 ? round($score * (100 / $factors), 2) : 0;
    }

    /**
     * Get performance grade
     */
    public function getPerformanceGrade(): string
    {
        $score = $this->getPerformanceScore();
        
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    /**
     * Get performance color
     */
    public function getPerformanceColor(): string
    {
        $score = $this->getPerformanceScore();
        
        if ($score >= 80) return 'success';
        if ($score >= 60) return 'warning';
        return 'danger';
    }

    /**
     * Scope for on-time tasks
     */
    public function scopeOnTime($query)
    {
        return $query->where('on_time', true);
    }

    /**
     * Scope for overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('on_time', false)->where('days_overdue', '>', 0);
    }

    /**
     * Scope for high quality tasks
     */
    public function scopeHighQuality($query, float $threshold = 4.0)
    {
        return $query->where('quality_score', '>=', $threshold);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
