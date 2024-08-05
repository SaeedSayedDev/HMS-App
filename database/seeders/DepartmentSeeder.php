<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'Cardiology', 'description' => 'Specializes in heart diseases and circulatory system disorders.'],
            ['name' => 'Neurology', 'description' => 'Focuses on disorders of the nervous system, including the brain and spinal cord.'],
            ['name' => 'Orthopedics', 'description' => 'Deals with injuries and disorders of the musculoskeletal system.'],
            ['name' => 'Oncology', 'description' => 'Specializes in the diagnosis and treatment of cancer.'],
            ['name' => 'Pediatrics', 'description' => 'Focuses on medical care for infants, children, and adolescents.'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
