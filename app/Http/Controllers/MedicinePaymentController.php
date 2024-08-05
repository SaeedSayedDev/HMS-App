<?php

namespace App\Http\Controllers;

use App\Models\MedicinePayment;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class MedicinePaymentController extends Controller
{
    use ApiResponseTrait;

    ############################## Admin ##############################

    public function index()
    {
        $medicinePayments = MedicinePayment::with('patient', 'medicine')->get();

        return $this->respondWithData('All Retrieved successfully', $medicinePayments);
    }

    public function show(MedicinePayment $medicinePayment)
    {
        $medicinePayment->load('patient', 'medicine');

        return $this->respondWithData('Retrieved successfully', $medicinePayment);
    }

    ############################## Patient ############################## 

    public function indexPatient()
    {
        /** @var User */
        $patient = auth()->user();
        $medicinePayments = $patient->medicinePayments()->with('medicine')->get();

        return $this->respondWithData('All Retrieved successfully', $medicinePayments);
    }

    public function showPatient(MedicinePayment $medicinePayment)
    {
        if ($medicinePayment->patient_id != auth()->id()) {
            return $this->errorResponse('Unauthorized');
        }

        $medicinePayment->load('medicine');

        return $this->respondWithData('Retrieved successfully', $medicinePayment);
    }
}
