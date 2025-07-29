<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    /**
     * Get all teams (Admin only)
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                $teams = Team::with(['manager', 'activeMembers.user'])
                           ->withCount('activeMembers')
                           ->get();
            } elseif ($user->role === 'manager') {
                $teams = Team::where('manager_id', $user->id)
                           ->with(['manager', 'activeMembers.user'])
                           ->withCount('activeMembers')
                           ->get();
            } else {
                $teams = $user->activeTeams()
                            ->with(['manager', 'activeMembers.user'])
                            ->withCount('activeMembers')
                            ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $teams
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teams',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new team (Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admins can create teams.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:teams',
                'description' => 'nullable|string',
                'manager_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if manager has manager role
            $manager = User::find($request->manager_id);
            if ($manager->role !== 'manager') {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user is not a manager'
                ], 422);
            }

            $team = Team::create($request->all());
            $team->load(['manager', 'activeMembers.user']);

            return response()->json([
                'success' => true,
                'message' => 'Team created successfully',
                'data' => $team
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific team
     */
    public function show(Team $team): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Check permissions
            if ($user->role !== 'admin' && 
                !$user->managesTeam($team) && 
                !$user->isMemberOf($team)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this team'
                ], 403);
            }

            $team->load([
                'manager', 
                'activeMembers.user',
                'tasks' => function($query) {
                    $query->with(['user', 'assignedBy']);
                }
            ]);

            $stats = $team->getStats();

            return response()->json([
                'success' => true,
                'data' => [
                    'team' => $team,
                    'stats' => $stats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update team (Admin or Manager)
     */
    public function update(Request $request, Team $team): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin' && !$user->managesTeam($team)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this team'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255|unique:teams,name,' . $team->id,
                'description' => 'nullable|string',
                'manager_id' => 'sometimes|exists:users,id',
                'is_active' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // If changing manager, check if new manager has manager role
            if ($request->has('manager_id')) {
                $manager = User::find($request->manager_id);
                if ($manager->role !== 'manager') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected user is not a manager'
                    ], 422);
                }
            }

            $team->update($request->all());
            $team->load(['manager', 'activeMembers.user']);

            return response()->json([
                'success' => true,
                'message' => 'Team updated successfully',
                'data' => $team
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete team (Admin only)
     */
    public function destroy(Team $team): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only admins can delete teams.'
                ], 403);
            }

            $team->delete();

            return response()->json([
                'success' => true,
                'message' => 'Team deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete team',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add member to team
     */
    public function addMember(Request $request, Team $team): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin' && !$user->managesTeam($team)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to add members to this team'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'role_in_team' => 'sometimes|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $memberUser = User::find($request->user_id);
            
            // Check if user is already a member
            if ($team->hasMember($memberUser)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already a member of this team'
                ], 422);
            }

            $teamMember = $team->addMember($memberUser, $request->role_in_team ?? 'member');

            return response()->json([
                'success' => true,
                'message' => 'Member added successfully',
                'data' => $teamMember->load('user')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add member',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove member from team
     */
    public function removeMember(Request $request, Team $team): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin' && !$user->managesTeam($team)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to remove members from this team'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $memberUser = User::find($request->user_id);
            
            if (!$team->hasMember($memberUser)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a member of this team'
                ], 422);
            }

            $team->removeMember($memberUser);

            return response()->json([
                'success' => true,
                'message' => 'Member removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove member',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
