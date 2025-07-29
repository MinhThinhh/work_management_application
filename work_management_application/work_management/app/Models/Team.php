<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the manager of the team
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get all team members
     */
    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get active team members
     */
    public function activeMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class)->where('is_active', true);
    }

    /**
     * Get users in this team
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
                    ->withPivot(['joined_at', 'role_in_team', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Get active users in this team
     */
    public function activeUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
                    ->wherePivot('is_active', true)
                    ->withPivot(['joined_at', 'role_in_team', 'is_active'])
                    ->withTimestamps();
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
        return $this->teamMembers()
                    ->where('user_id', $user->id)
                    ->where('is_active', true)
                    ->exists();
    }

    /**
     * Add member to team
     */
    public function addMember(User $user, string $role = 'member'): TeamMember
    {
        return $this->teamMembers()->create([
            'user_id' => $user->id,
            'role_in_team' => $role,
            'joined_at' => now(),
            'is_active' => true
        ]);
    }

    /**
     * Remove member from team
     */
    public function removeMember(User $user): bool
    {
        return $this->teamMembers()
                    ->where('user_id', $user->id)
                    ->update(['is_active' => false]);
    }

    /**
     * Get team statistics
     */
    public function getStats(): array
    {
        $totalMembers = $this->activeMembers()->count();
        $totalTasks = $this->tasks()->count();
        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        $pendingTasks = $this->tasks()->where('status', 'pending')->count();
        $inProgressTasks = $this->tasks()->where('status', 'in_progress')->count();

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
