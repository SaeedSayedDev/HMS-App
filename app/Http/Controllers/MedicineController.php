<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicineRequest;
use App\Http\Requests\StripeRequest;
use App\Models\Medicine;
use App\Models\MedicinePayment;
use App\Models\User;
use App\Services\StripeService;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageTrait;
use App\Traits\NotificationTrait;

class MedicineController extends Controller
{
    use ApiResponseTrait, ImageTrait, NotificationTrait;

    public function __construct(protected StripeService $stripeService)
    {
    }

    public function index()
    {
        $medicines = Medicine::with('image')->get();

        return $this->respondWithData('All Retrieved successfully', $medicines);
    }

    public function store(MedicineRequest $request)
    {
        $data = $request->validated();

        $medicine = Medicine::create($data);
        $this->storeImage($medicine, $request->file('image'), 'images/medicines');
        $medicine->load('image');

        return $this->respondWithData('Created successfully', $medicine);
    }

    public function show(Medicine $medicine)
    {
        $medicine->load('image');

        return $this->respondWithData('Retrieved successfully', $medicine);
    }

    public function update(MedicineRequest $request, Medicine $medicine)
    {
        $data = $request->validated();

        $medicine->update($data);
        $this->updateImage($medicine, $request->file('image'), 'images/medicines');
        $medicine->load('image');

        return $this->respondWithData('Updated successfully', $medicine);
    }

    public function destroy(Medicine $medicine)
    {
        $this->deleteImage($medicine);
        $medicine->delete();

        return $this->successResponse('Deleted successfully');
    }

    public function pay(StripeRequest $request, Medicine $medicine)
    {
        $data = $request->validated();
        $quantity = $request->validate(['quantity' => 'required|integer|min:1'])['quantity'];

        if ($quantity && $quantity > $medicine->quantity) {
            return $this->errorResponse('Invalid quantity', 422);
        }

        try {
            $token = $this->stripeService->createToken($data);

            $amount = $medicine->price * $quantity * 100;

            $charge = $this->stripeService->createCharge($amount, 'egp', $token);

            $medicine->quantity -= $quantity;
            $medicine->save();

            if ($medicine->quantity == 0) {
                $admin = User::where('role_id', 3)->first();
                $this->notify($admin->id, 'medicine', 'Medicine Out of Stock', 'Medicine ' . $medicine->name . ' is out of stock');
            }

            MedicinePayment::create([
                'payment_id' => $charge->id,
                'patient_id' => auth()->id(),
                'medicine_id' => $medicine->id,
                'quantity' => $quantity,
                'amount' => $amount
            ]);

            return $this->successResponse('Payment successful');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
