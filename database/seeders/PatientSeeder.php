<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $userData = [
            'first_name' => 'patient',
            'last_name' => '1',
            'email' => 'patient1@gmail.com',
            'password' => '$2y$10$1tNORTVDW7Kjk5UWgfOReu68x7VrB4fnvETle0DpII1vvNXE13.uO',
            'phone' => '+20100000001',
            'birth_date' => 'Jun 23, 2002',
        ];

        $patientData = [
            'gender' => true,
            'address' => 'address1',
            'blood_group' => 'A+',
        ];

        User::create($userData)->patient()->create($patientData);
    }
}
