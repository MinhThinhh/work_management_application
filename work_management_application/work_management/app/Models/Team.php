<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'leader_id',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the leader of the team
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Get all members of this team
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'team_id');
    }

    /**
     * Get all active members (users with this team_id)
     */
    public function activeMembers(): HasMany
    {
        return $this->members();
    }

    /**
     * Get member count
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }

    /**
     * Get tasks assigned to this team
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get KPI targets for this team
     */
    public function kpiTargets(): HasMany
    {
        return $this->hasMany(KpiTarget::class);
    }

    /**
     * Get KPI evaluations for this team
     */
    public function kpiEvaluations(): HasMany
    {
        return $this->hasMany(KpiEvaluation::class);
    }

    /**
     * Check if user is member of this team
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('id', $user->id)->exists();
    }

    /**
     * Add member to team
     */
    public function addMember(User $user): bool
    {
        return $user->update(['team_id' => $this->id]);
    }

    /**
     * Remove member from team
     */
    public function removeMember(User $user): bool
    {
        return $user->update(['team_id' => null]);
    }

    /**
     * Get team statistics
     */
    public function getStats(): array
    {
        $totalMembers = $this->members()->count();

        // Get tasks assigned to team members
        $memberIds = $this->members()->pluck('id');
        $totalTasks = Task::whereIn('assigned_to', $memberIds)->count();
        $completedTasks = Task::whereIn('assigned_to', $memberIds)->where('status', 'completed')->count();
        $pendingTasks = Task::whereIn('assigned_to', $memberIds)->where('status', 'pending')->count();
        $inProgressTasks = Task::whereIn('assigned_to', $memberIds)->where('status', 'in_progress')->count();

        return [
            'total_members' => $totalMembers,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'in_progress_tasks' => $inProgressTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0
        ];
    }
}
