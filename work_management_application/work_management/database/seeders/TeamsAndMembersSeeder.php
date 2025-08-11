<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;

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
                'name' => 'Võ Thị Phương Thảo',
                'email' => 'manager.a@company.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'phone' => '0901234567',
                'address' => 'Hà Nội'
            ],
            [
                'name' => 'Lê Đình Huy',
                'email' => 'manager.b@company.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'phone' => '0901234568',
                'address' => 'Hồ Chí Minh'
            ],
            [
                'name' => 'Phạm Đăng Thịnh',
                'email' => 'manager.c@company.com',
                'password' => Hash::make('password'),
                'phone' => '0901234569',
                'address' => 'Đà Nẵng'
            ],
            [
                'name' => 'Phạm Thanh Sơn',
                'email' => 'manager.d@company.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'phone' => '0901234570',
                'address' => 'Cần Thơ'
            ]
        ];

        $createdManagers = [];
        foreach ($managers as $managerData) {
            $createdManagers[] = User::create($managerData);
        }

        // Create teams
        $teams = [
            [
                'name' => 'Đội Phát Triển',
                'description' => 'Đội phát triển phần mềm',
                'leader_id' => $createdManagers[0]->id,
                'status' => 'active'
            ],
            [
                'name' => 'Đội Marketing',
                'description' => 'Đội marketing và truyền thông',
                'leader_id' => $createdManagers[1]->id,
                'status' => 'active'
            ],
            [
                'name' => 'Đội Bán Hàng',
                'description' => 'Đội bán hàng và chăm sóc khách hàng',
                'leader_id' => $createdManagers[2]->id,
                'status' => 'active'
            ],
            [
                'name' => 'Đội Nhân Sự',
                'description' => 'Đội nhân sự và hành chính',
                'leader_id' => $createdManagers[3]->id,
                'status' => 'active'
            ]
        ];

        $createdTeams = [];
        foreach ($teams as $teamData) {
            $createdTeams[] = Team::create($teamData);
        }

        // Create team members (users)
        $teamMembers = [
            [
                'name' => 'Huỳnh Kim Thỏa',
                'email' => 'user1@company.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '0901234570',
                'address' => 'Hà Nội'
            ],
            [
                'name' => 'Lê Thị Bích Mỹ',
                'email' => 'user2@company.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '0901234571',
                'address' => 'Hồ Chí Minh'
            ],
            [
                'name' => 'Bùi Thị Mai Trâm',
                'email' => 'user3@company.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '0901234572',
                'address' => 'Đà Nẵng'
            ],
            [
                'name' => 'Nguyễn Tuệ Nhi',
                'email' => 'user4@company.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '0901234573',
                'address' => 'Cần Thơ'
            ]
        ];

        // Create users and assign to teams
        $teamMembers[0]['team_id'] = $createdTeams[0]->id;
        $teamMembers[1]['team_id'] = $createdTeams[1]->id;
        $teamMembers[2]['team_id'] = $createdTeams[2]->id;
        $teamMembers[3]['team_id'] = $createdTeams[3]->id;

        foreach ($teamMembers as $memberData) {
            User::create($memberData);
        }
    }
}
