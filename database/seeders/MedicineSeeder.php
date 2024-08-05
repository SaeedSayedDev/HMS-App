<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Traits\ImageTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MedicineSeeder extends Seeder
{
    use ImageTrait;

    public function run(): void
    {
        $medicines = [
            [
                'name' => 'Paracetamol',
                'price' => 35.99,
                'quantity' => 100,
                'expiry_date' => date('M d, Y', strtotime('+1 year')),
            ],
            [
                'name' => 'Amoxicillin',
                'price' => 38.50,
                'quantity' => 50,
                'expiry_date' => date('M d, Y', strtotime('+2 years')),
            ],
            [
                'name' => 'Lisinopril',
                'price' => 42.75,
                'quantity' => 30,
                'expiry_date' => date('M d, Y', strtotime('+1 year 6 months')),
            ],
            [
                'name' => 'Atorvastatin',
                'price' => 45.25,
                'quantity' => 40,
                'expiry_date' => date('M d, Y', strtotime('-1 day')),
            ],
            [
                'name' => 'Omeprazole',
                'price' => 37.99,
                'quantity' => 60,
                'expiry_date' => date('M d, Y', strtotime('+1 year 9 months')),
            ],
        ];

        foreach ($medicines as $medicine) {
            $medicine = Medicine::create($medicine);
            $imagePath = database_path('seeders/images/medicines/' . $medicine['name'] . '.jpg');
            $imageFile = file_exists($imagePath) ? new UploadedFile($imagePath, basename($imagePath)) : null;
            $this->storeImage($medicine, $imageFile, 'images/medicines');
        }
    }
}
