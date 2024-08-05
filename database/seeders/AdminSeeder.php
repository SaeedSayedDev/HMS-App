<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminData = [
            'first_name' => 'admin',
            'last_name' => 'super admin',
            'email' => 'superAdmin@gmail.com',
            'password' => '$2y$10$1tNORTVDW7Kjk5UWgfOReu68x7VrB4fnvETle0DpII1vvNXE13.uO',
            'phone' => '+201096505009',
            'birth_date' => 'Jun 23, 1994',
            'role_id' => 3,
        ];
        $adminPermission = [
            'admin_id' => 1,
            'admin_management' => 1,
            'doctor_management' => 1,
            'salary_management' => 1,
            'absence_management' => 1,
            'medicine_management' => 1,
        ];
        User::create($adminData)->admin_premission()->create($adminPermission);
    }
}
