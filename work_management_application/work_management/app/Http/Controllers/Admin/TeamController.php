<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $teams = Team::with(['manager', 'activeMembers'])->get();
        $managers = User::where('role', 'manager')->get();
        
        return view('admin.teams', compact('teams', 'managers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:teams',
            'description' => 'nullable|string',
            'manager_id' => 'required|exists:users,id'
        ]);

        // Verify the selected user is a manager
        $manager = User::findOrFail($request->manager_id);
        if ($manager->role !== 'manager') {
            return back()->withErrors(['manager_id' => 'Người được chọn không phải là manager']);
        }

        Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'manager_id' => $request->manager_id,
            'is_active' => true
        ]);

        return redirect()->route('admin.teams.index')
                        ->with('success', 'Team đã được tạo thành công!');
    }

    public function show(Team $team)
    {
        $team->load(['manager', 'activeMembers.user']);
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
            'manager_id' => 'required|exists:users,id',
            'is_active' => 'boolean'
        ]);

        // Verify the selected user is a manager
        $manager = User::findOrFail($request->manager_id);
        if ($manager->role !== 'manager') {
            return back()->withErrors(['manager_id' => 'Người được chọn không phải là manager']);
        }

        $team->update([
            'name' => $request->name,
            'description' => $request->description,
            'manager_id' => $request->manager_id,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.teams.index')
                        ->with('success', 'Team đã được cập nhật thành công!');
    }

    public function destroy(Team $team)
    {
        // Check if team has active members
        if ($team->activeMembers()->count() > 0) {
            return back()->with('error', 'Không thể xóa team có thành viên. Vui lòng xóa tất cả thành viên trước.');
        }

        // Check if team has tasks
        if ($team->tasks()->count() > 0) {
            return back()->with('error', 'Không thể xóa team có công việc. Vui lòng chuyển hoặc xóa tất cả công việc trước.');
        }

        $team->delete();

        return redirect()->route('admin.teams.index')
                        ->with('success', 'Team đã được xóa thành công!');
    }

    public function addMember(Request $request, Team $team)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_in_team' => 'required|in:member,senior_member,team_lead'
        ]);

        // Check if user is already a member
        if ($team->members()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'Người dùng đã là thành viên của team này.');
        }

        $team->members()->create([
            'user_id' => $request->user_id,
            'role_in_team' => $request->role_in_team,
            'is_active' => true
        ]);

        return back()->with('success', 'Thành viên đã được thêm vào team thành công!');
    }

    public function removeMember(Request $request, Team $team)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $member = $team->members()->where('user_id', $request->user_id)->first();
        
        if (!$member) {
            return back()->with('error', 'Người dùng không phải là thành viên của team này.');
        }

        $member->delete();

        return back()->with('success', 'Thành viên đã được xóa khỏi team thành công!');
    }
}
