<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of teams.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Admin can see all teams, Manager can see teams they manage, User can see teams they belong to
        if ($user->role === 'admin') {
            $teams = Team::with(['manager', 'activeMembers.user'])->get();
        } elseif ($user->role === 'manager') {
            $teams = Team::where('manager_id', $user->id)
                         ->with(['manager', 'activeMembers.user'])
                         ->get();
        } else {
            $teams = Team::whereHas('activeMembers', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['manager', 'activeMembers.user'])->get();
        }

        $managers = User::where('role', 'manager')->get();
        
        return view('teams.index', compact('teams', 'managers'));
    }

    /**
     * Store a newly created team.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only admin can create teams
        if ($user->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không có quyền tạo team.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:teams',
            'description' => 'nullable|string',
            'manager_id' => 'required|exists:users,id'
        ]);

        // Verify the selected user is a manager
        $manager = User::findOrFail($request->manager_id);
        if ($manager->role !== 'manager') {
            return redirect()->back()->withErrors(['manager_id' => 'Người được chọn không phải là manager']);
        }

        Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'manager_id' => $request->manager_id,
            'is_active' => true
        ]);

        return redirect()->route('teams.index')->with('success', 'Team đã được tạo thành công!');
    }

    /**
     * Display the specified team.
     */
    public function show(Team $team)
    {
        $user = Auth::user();
        
        // Check permission
        if ($user->role === 'user' && !$team->hasMember($user)) {
            abort(403, 'Bạn không có quyền xem team này.');
        }
        
        if ($user->role === 'manager' && $team->manager_id !== $user->id) {
            abort(403, 'Bạn không có quyền xem team này.');
        }

        $team->load(['manager', 'activeMembers.user', 'tasks']);
        $availableUsers = User::where('role', 'user')
                             ->whereDoesntHave('activeTeams', function($query) use ($team) {
                                 $query->where('team_id', $team->id);
                             })
                             ->get();

        return view('teams.show', compact('team', 'availableUsers'));
    }

    /**
     * Update the specified team.
     */
    public function update(Request $request, Team $team)
    {
        $user = Auth::user();
        
        // Only admin can update teams
        if ($user->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật team.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
            'description' => 'nullable|string',
            'manager_id' => 'required|exists:users,id',
            'is_active' => 'boolean'
        ]);

        // Verify the selected user is a manager
        $manager = User::findOrFail($request->manager_id);
        if ($manager->role !== 'manager') {
            return redirect()->back()->withErrors(['manager_id' => 'Người được chọn không phải là manager']);
        }

        $team->update([
            'name' => $request->name,
            'description' => $request->description,
            'manager_id' => $request->manager_id,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('teams.index')->with('success', 'Team đã được cập nhật thành công!');
    }

    /**
     * Remove the specified team.
     */
    public function destroy(Team $team)
    {
        $user = Auth::user();
        
        // Only admin can delete teams
        if ($user->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa team.');
        }

        // Check if team has active members
        if ($team->activeMembers()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa team có thành viên. Vui lòng xóa tất cả thành viên trước.');
        }

        // Check if team has tasks
        if ($team->tasks()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa team có công việc. Vui lòng chuyển hoặc xóa tất cả công việc trước.');
        }

        $team->delete();

        return redirect()->route('teams.index')->with('success', 'Team đã được xóa thành công!');
    }

    /**
     * Add a member to the team.
     */
    public function addMember(Request $request, Team $team)
    {
        $user = Auth::user();
        
        // Only admin and team manager can add members
        if ($user->role !== 'admin' && $team->manager_id !== $user->id) {
            return redirect()->back()->with('error', 'Bạn không có quyền thêm thành viên vào team này.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_in_team' => 'required|in:member,senior_member,team_lead'
        ]);

        // Check if user is already a member
        if ($team->hasMember(User::find($request->user_id))) {
            return redirect()->back()->with('error', 'Người dùng đã là thành viên của team này.');
        }

        $team->addMember(User::find($request->user_id), $request->role_in_team);

        return redirect()->back()->with('success', 'Thành viên đã được thêm vào team thành công!');
    }

    /**
     * Remove a member from the team.
     */
    public function removeMember(Request $request, Team $team)
    {
        $user = Auth::user();
        
        // Only admin and team manager can remove members
        if ($user->role !== 'admin' && $team->manager_id !== $user->id) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa thành viên khỏi team này.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $memberUser = User::find($request->user_id);
        
        if (!$team->hasMember($memberUser)) {
            return redirect()->back()->with('error', 'Người dùng không phải là thành viên của team này.');
        }

        $team->removeMember($memberUser);

        return redirect()->back()->with('success', 'Thành viên đã được xóa khỏi team thành công!');
    }
}
