<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Hash;

class TeamsAndMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create managers
        $managers = [
            [
                'name' => 'Nguyễn Văn Manager A',
                'email' => 'manager.a@company.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'employee_id' => 'MGR001',
                'hire_date' => '2023-01-15',
                'department' => 'Development',
                'position' => 'Development Manager',
                'phone' => '0901234567',
                'address' => 'Hà Nội'
            ],
            [
                'name' => 'Trần Thị Manager B',
                'email' => 'manager.b@company.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'employee_id' => 'MGR002',
                'hire_date' => '2023-02-01',
                'department' => 'Marketing',
                'position' => 'Marketing Manager',
                'phone' => '0901234568',
                'address' => 'Hồ Chí Minh'
            ],
            [
                'name' => 'Lê Văn Manager C',
                'email' => 'manager.c@company.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'employee_id' => 'MGR003',
                'hire_date' => '2023-03-01',
                'department' => 'Sales',
                'position' => 'Sales Manager',
                'phone' => '0901234569',
                'address' => 'Đà Nẵng'
            ]
        ];

        $createdManagers = [];
        foreach ($managers as $managerData) {
            $createdManagers[] = User::create($managerData);
        }

        // Create teams
        $teams = [
            [
                'name' => 'Development Team Alpha',
                'description' => 'Frontend and Backend Development Team',
                'manager_id' => $createdManagers[0]->id,
                'is_active' => true
            ],
            [
                'name' => 'Marketing Team Beta',
                'description' => 'Digital Marketing and Content Team',
                'manager_id' => $createdManagers[1]->id,
                'is_active' => true
            ],
            [
                'name' => 'Sales Team Gamma',
                'description' => 'Sales and Customer Relations Team',
                'manager_id' => $createdManagers[2]->id,
                'is_active' => true
            ]
        ];

        $createdTeams = [];
        foreach ($teams as $teamData) {
            $createdTeams[] = Team::create($teamData);
        }

        // Create team members
        $teamMembers = [
            // Development Team Alpha members
            [
                'name' => 'Phạm Văn Developer 1',
                'email' => 'dev1@company.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'employee_id' => 'DEV001',
                'hire_date' => '2023-04-01',
                'department' => 'Development',
                'position' => 'Frontend Developer',
                'phone' => '0901234570',
                'address' => 'Hà Nội',
                'team_id' => $createdTeams[0]->id,
                'role_in_team' => 'senior_developer'
            ],
            [
                'name' => 'Hoàng Thị Developer 2',
                'email' => 'dev2@company.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'employee_id' => 'DEV002',
                'hire_date' => '2023-05-01',
                'department' => 'Development',
                'position' => 'Backend Developer',
                'phone' => '0901234571',
                'address' => 'Hà Nội',
                'team_id' => $createdTeams[0]->id,
                'role_in_team' => 'developer'
            ],
            [
                'name' => 'Vũ Văn Developer 3',
                'email' => 'dev3@company.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'employee_id' => 'DEV003',
                'hire_date' => '2023-06-01',
                'department' => 'Development',
                'position' => 'Full Stack Developer',
                'phone' => '0901234572',
                'address' => 'Hà Nội',
                'team_id' => $createdTeams[0]->id,
                'role_in_team' => 'developer'
            ],
            // Marketing Team Beta members
            [
                'name' => 'Đỗ Thị Marketing 1',
                'email' => 'marketing1@company.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'employee_id' => 'MKT001',
                'hire_date' => '2023-04-15',
                'department' => 'Marketing',
                'position' => 'Digital Marketing Specialist',
                'phone' => '0901234573',
                'address' => 'Hồ Chí Minh',
                'team_id' => $createdTeams[1]->id,
                'role_in_team' => 'specialist'
            ],
            [
                'name' => 'Bùi Văn Marketing 2',
                'email' => 'marketing2@company.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'employee_id' => 'MKT002',
                'hire_date' => '2023-05-15',
                'department' => 'Marketing',
                'position' => 'Content Creator',
                'phone' => '0901234574',
                'address' => 'Hồ Chí Minh',
                'team_id' => $createdTeams[1]->id,
                'role_in_team' => 'creator'
            ],
            // Sales Team Gamma members
            [
                'name' => 'Đinh Thị Sales 1',
                'email' => 'sales1@company.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'employee_id' => 'SAL001',
                'hire_date' => '2023-06-15',
                'department' => 'Sales',
                'position' => 'Sales Executive',
                'phone' => '0901234575',
                'address' => 'Đà Nẵng',
                'team_id' => $createdTeams[2]->id,
                'role_in_team' => 'executive'
            ],
            [
                'name' => 'Cao Văn Sales 2',
                'email' => 'sales2@company.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'employee_id' => 'SAL002',
                'hire_date' => '2023-07-01',
                'department' => 'Sales',
                'position' => 'Account Manager',
                'phone' => '0901234576',
                'address' => 'Đà Nẵng',
                'team_id' => $createdTeams[2]->id,
                'role_in_team' => 'account_manager'
            ]
        ];

        foreach ($teamMembers as $memberData) {
            $teamId = $memberData['team_id'];
            $roleInTeam = $memberData['role_in_team'];
            
            unset($memberData['team_id'], $memberData['role_in_team']);
            
            $user = User::create($memberData);
            
            // Add user to team
            TeamMember::create([
                'team_id' => $teamId,
                'user_id' => $user->id,
                'joined_at' => now(),
                'role_in_team' => $roleInTeam,
                'is_active' => true
            ]);
        }
    }
}
