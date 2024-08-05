<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $userData = [
            'first_name' => 'doctor',
            'last_name' => '1',
            'email' => 'doctor1@gmail.com',
            'password' => '$2y$10$1tNORTVDW7Kjk5UWgfOReu68x7VrB4fnvETle0DpII1vvNXE13.uO',
            'phone' => '+20100000002',
            'birth_date' => 'Jun 23, 2000',
            'role_id' => 2,
        ];

        $doctorData = [
            'specialization' => 'specialization1',
            'department_id' => 1,
            'fee' => 100,
        ];

        $user = User::create($userData);
        $user->doctor()->create($doctorData);
        $user->workingHours()->createMany([
            [
                'day_name' => 'Sunday',
                'start_time' => '04:00',
                'end_time' => '06:00',
            ],
            [
                'day_name' => 'Monday',
                'start_time' => '05:00',
                'end_time' => '08:00',
            ],
        ]);
    }
}
