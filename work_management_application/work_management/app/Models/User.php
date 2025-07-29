<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * Get the tasks created by the user.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'creator_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
        'hire_date',
        'department',
        'position',
        'phone',
        'address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // Team relationships

    /**
     * Get teams managed by this user
     */
    public function managedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'manager_id');
    }

    /**
     * Get team memberships
     */
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get active team memberships
     */
    public function activeTeamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class)->where('is_active', true);
    }

    /**
     * Get teams this user belongs to
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')
                    ->withPivot(['joined_at', 'role_in_team', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Get active teams this user belongs to
     */
    public function activeTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')
                    ->wherePivot('is_active', true)
                    ->withPivot(['joined_at', 'role_in_team', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Get tasks assigned to this user
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'user_id');
    }

    /**
     * Get tasks assigned by this user (for managers)
     */
    public function assignedByTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    // KPI relationships

    /**
     * Get KPI targets for this user
     */
    public function kpiTargets(): HasMany
    {
        return $this->hasMany(KpiTarget::class);
    }

    /**
     * Get KPI targets set by this user
     */
    public function setKpiTargets(): HasMany
    {
        return $this->hasMany(KpiTarget::class, 'set_by');
    }

    /**
     * Get KPI evaluations for this user
     */
    public function kpiEvaluations(): HasMany
    {
        return $this->hasMany(KpiEvaluation::class);
    }

    /**
     * Get KPI evaluations conducted by this user
     */
    public function conductedEvaluations(): HasMany
    {
        return $this->hasMany(KpiEvaluation::class, 'evaluated_by');
    }

    /**
     * Get task performance records
     */
    public function taskPerformances(): HasMany
    {
        return $this->hasMany(TaskPerformance::class);
    }

    // Helper methods

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user manages a specific team
     */
    public function managesTeam(Team $team): bool
    {
        return $this->managedTeams()->where('id', $team->id)->exists();
    }

    /**
     * Check if user is member of a specific team
     */
    public function isMemberOf(Team $team): bool
    {
        return $this->activeTeamMemberships()
                    ->where('team_id', $team->id)
                    ->exists();
    }

    /**
     * Get user's current team (first active team)
     */
    public function getCurrentTeam(): ?Team
    {
        return $this->activeTeams()->first();
    }

    /**
     * Get user's role in a specific team
     */
    public function getRoleInTeam(Team $team): ?string
    {
        $membership = $this->teamMemberships()
                          ->where('team_id', $team->id)
                          ->where('is_active', true)
                          ->first();

        return $membership?->role_in_team;
    }
}
