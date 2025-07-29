<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'year',
        'quarter',
        'overall_score',
        'manager_comments',
        'self_assessment',
        'improvement_areas',
        'achievements',
        'metric_scores',
        'evaluated_by',
        'evaluation_date',
        'status'
    ];

    protected $casts = [
        'year' => 'integer',
        'quarter' => 'integer',
        'overall_score' => 'decimal:2',
        'metric_scores' => 'array',
        'evaluation_date' => 'date',
    ];

    /**
     * Get the user being evaluated
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
     * Get the evaluator
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'rejected' => 'Rejected'
        ];
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    /**
     * Get status color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Check if evaluation is editable
     */
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    /**
     * Get evaluation period label
     */
    public function getPeriodLabel(): string
    {
        if ($this->quarter) {
            return "Q{$this->quarter} {$this->year}";
        }
        return "Annual {$this->year}";
    }

    /**
     * Scope for specific year
     */
    public function scopeYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope for specific quarter
     */
    public function scopeQuarter($query, int $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    /**
     * Scope for annual evaluations
     */
    public function scopeAnnual($query)
    {
        return $query->whereNull('quarter');
    }

    /**
     * Scope for specific status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for team
     */
    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }
}
