<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TeamController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(function ($request, $next) {
                if (Auth::user()->role !== 'admin') {
                    abort(403, 'Unauthorized');
                }
                return $next($request);
            })
        ];
    }

    public function index()
    {
        $teams = Team::with(['leader', 'members'])->get();
        $managers = User::where('role', 'manager')->get();
        $users = User::where('role', 'user')->get();

        return view('admin.teams', compact('teams', 'managers', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:teams',
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:users,id'
        ]);

        // Verify the selected user is a manager
        $leader = User::findOrFail($request->leader_id);
        if ($leader->role !== 'manager') {
            return back()->withErrors(['leader_id' => 'Người được chọn không phải là manager']);
        }

        Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'leader_id' => $request->leader_id,
            'status' => 'active'
        ]);

        return redirect()->route('admin.teams.index')
                        ->with('success', 'Team đã được tạo thành công!');
    }

    public function show(Team $team)
    {
        $team->load(['leader', 'members']);
        return view('admin.teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $managers = User::where('role', 'manager')->get();
        return view('admin.teams.edit', compact('team', 'managers'));
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive'
        ]);

        // Verify the selected user is a manager
        $leader = User::findOrFail($request->leader_id);
        if ($leader->role !== 'manager') {
            return back()->withErrors(['leader_id' => 'Người được chọn không phải là manager']);
        }

        $team->update([
            'name' => $request->name,
            'description' => $request->description,
            'leader_id' => $request->leader_id,
            'status' => $request->status
        ]);

        return redirect()->route('admin.teams.index')
                        ->with('success', 'Team đã được cập nhật thành công!');
    }

    public function destroy(Team $team)
    {
        // Check if team has members
        if ($team->members()->count() > 0) {
            return back()->with('error', 'Không thể xóa team có thành viên. Vui lòng chuyển tất cả thành viên sang team khác trước.');
        }

        // Check if team has tasks
        $tasksCount = \App\Models\Task::whereHas('assignedUser', function($query) use ($team) {
            $query->where('team_id', $team->id);
        })->count();

        if ($tasksCount > 0) {
            return back()->with('error', 'Không thể xóa team có công việc. Vui lòng chuyển hoặc hoàn thành tất cả công việc trước.');
        }

        $team->delete();

        return redirect()->route('admin.teams.index')
                        ->with('success', 'Team đã được xóa thành công!');
    }



    public function getMembers(Team $team)
    {
        try {
            $members = $team->members()->get();
            $availableUsers = User::where('role', '!=', 'admin')
                                ->whereNull('team_id')
                                ->get();

            return response()->json([
                'success' => true,
                'team' => $team,
                'members' => $members,
                'availableUsers' => $availableUsers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addMember(Request $request, Team $team)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            $user = User::findOrFail($request->user_id);

            if ($user->team_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng đã thuộc team khác.'
                ], 422);
            }

            // Add user to team
            $user->update(['team_id' => $team->id]);

            return response()->json([
                'success' => true,
                'message' => 'Thành viên đã được thêm vào team thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeMember(Request $request, Team $team)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            $user = User::findOrFail($request->user_id);

            if ($user->team_id != $team->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng không phải là thành viên của team này.'
                ], 422);
            }

            // Remove user from team
            $user->update(['team_id' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Thành viên đã được xóa khỏi team thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
