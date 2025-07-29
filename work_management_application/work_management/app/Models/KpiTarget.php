<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'metric_id',
        'year',
        'target_value',
        'current_value',
        'set_by',
        'notes'
    ];

    protected $casts = [
        'year' => 'integer',
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the team
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the metric
     */
    public function metric(): BelongsTo
    {
        return $this->belongsTo(KpiMetric::class, 'metric_id');
    }

    /**
     * Get who set the target
     */
    public function setter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    /**
     * Calculate achievement percentage
     */
    public function getAchievementPercentage(): float
    {
        if ($this->target_value == 0) {
            return 0;
        }
        
        return round(($this->current_value / $this->target_value) * 100, 2);
    }

    /**
     * Check if target is achieved
     */
    public function isAchieved(): bool
    {
        return $this->current_value >= $this->target_value;
    }

    /**
     * Get status color based on achievement
     */
    public function getStatusColor(): string
    {
        $percentage = $this->getAchievementPercentage();
        
        if ($percentage >= 100) {
            return 'success'; // Green
        } elseif ($percentage >= 80) {
            return 'warning'; // Yellow
        } else {
            return 'danger'; // Red
        }
    }

    /**
     * Scope for specific year
     */
    public function scopeYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific team
     */
    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }
}
