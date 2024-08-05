<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('migrate:fresh');

        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            DepartmentSeeder::class,
            PatientSeeder::class,
            DoctorSeeder::class,
            MedicineSeeder::class,
        ]);

        # Temp
        Appointment::create([
            'patient_id' => 2,
            'doctor_id' => 3,
            'date' => 'Jun 23, 2024',
            'time' => '04:30',
            'reason' => 'Cough',
        ]);
        Appointment::create([
            'patient_id' => 2,
            'doctor_id' => 3,
            'date' => 'Jun 23, 2024',
            'time' => '05:00',
            'reason' => 'Cough',
        ]);
    }
}
