<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\StripeRequest;
use App\Models\Appointment;
use App\Models\User;
use App\Services\StripeService;
use App\Traits\ApiResponseTrait;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class AppointmentController extends Controller
{
    use ApiResponseTrait, NotificationTrait;

    public function __construct(protected StripeService $stripeService)
    {
    }

    public function availableSlots(Request $request, User $doctor)
    {
        $dayName = $request->day_name;
        $workingHours = $doctor
            ->workingHours()
            ->where('day_name', $dayName)
            ->first();

        if (!$workingHours) {
            return $this->errorResponse('No working hours available for this doctor on the specified day');
        }

        $slots = collect();
        $date = Carbon::now()->next(Carbon::parse("next $dayName")->dayOfWeek)->format('M d, Y');
        $startTime = Carbon::parse("{$date} {$workingHours->start_time}");
        $endTime = Carbon::parse("{$date} {$workingHours->end_time}");
        $interval = CarbonInterval::minutes(15);

        while ($startTime->lessThan($endTime)) {
            $slots->push($startTime->format('H:i:s'));
            $startTime->add($interval);
        }

        $reservedSlots = $doctor
            ->doctorAppointments()
            ->where('date', $date)
            ->whereNot('status', 'rejected')
            ->pluck('time');

        $availableSlots = $slots->diff($reservedSlots);

        return $this->respondWithData('Available slots retrieved successfully', $availableSlots->values());
    }


    ########################## Patient ############################

    public function index(Request $request)
    {
        /** @var User */
        $patient = auth()->user();
        $appointments = $patient->patientAppointments()->with('doctor');

        if ($request->has('paid')) {
            $paid = filter_var($request->paid, FILTER_VALIDATE_BOOLEAN);
            $appointments = $appointments->where('paid', $paid);
        }

        $appointments = $appointments->get();
        
        return $this->respondWithData('All Retrieved successfully', $appointments);
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);

        return $this->respondWithData('Retrieved successfully', $appointment);
    }

    public function store(AppointmentRequest $request)
    {
        $data = $request->validated();
        $data['patient_id'] = auth()->id();
        $data['date'] = Carbon::now()->next(Carbon::parse("next {$data['day_name']}")->dayOfWeek)->format('Y-m-d');
        $doctor = User::where('role_id', 2)->findOrFail($data['doctor_id']);
        $data['price'] = $doctor->doctor->fee;

        $validationResult = $this->validateStoreAppointment($data, $doctor);
        if ($validationResult !== true) {
            return $validationResult;
        }

        $appointment = Appointment::create($data);
        $appointment->load('patient', 'doctor');
        
        $this->notify($data['doctor_id'], 'appointment', 'Appointment Reminder', 'You have an appointment on ' . $data['date'] . ' at ' . $data['time']);

        return $this->respondWithData('Created successfully', $appointment);
    }

    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $data = $request->validated();

        $appointment->update($data);

        return $this->respondWithData('Updated successfully', $appointment);
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);

        $appointment->delete();

        return $this->successResponse('Deleted successfully');
    }

    public function pay(StripeRequest $request, Appointment $appointment)
    {
        $data = $request->validated();
        
        if ($appointment->paid) {
            return $this->errorResponse('Already paid', 409);
        }

        try {
            $token = $this->stripeService->createToken($data);
            
            $amount = $appointment->price * 100;
            
            $charge = $this->stripeService->createCharge($amount, 'egp', $token);

            $appointment->update(['paid' => true]);

            return $this->successResponse('Payment successful');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    ########################## Doctor ############################

    public function indexDoctor()
    {
        /** @var User */
        $doctor = auth()->user();
        $appointments = $doctor->doctorAppointments()->where('paid', true)->get();

        return $this->respondWithData('All Retrieved successfully', $appointments);
    }

    public function showDoctor(Appointment $appointment)
    {
        $this->authorize('viewDoctor', $appointment);

        return $this->respondWithData('Retrieved successfully', $appointment);
    }

    public function accept(Appointment $appointment)
    {
        $this->authorize('accept', $appointment);

        if (!$appointment->paid) {
            return $this->errorResponse('Please pay first', 409);
        } elseif ($appointment->status != 'pending') {
            return $this->errorResponse('Already accepted or rejected', 409);
        }

        $appointment->update(['status' => 'accepted']);

        $this->notify($appointment->patient_id, 'appointment', 'Appointment Accepted', 'Your appointment on' . $appointment->date . 'at' . $appointment->time . 'has been accepted');

        return $this->successResponse('Accepted successfully');
    }

    public function reject(Appointment $appointment)
    {
        $this->authorize('reject', $appointment);

        if ($appointment->status != 'pending') {
            return $this->errorResponse('Already accepted or rejected', 409);
        }

        $appointment->update(['status' => 'rejected']);

        $this->notify($appointment->patient_id, 'appointment', 'Appointment Rejected', 'Your appointment on' . $appointment->date . 'at' . $appointment->time . 'has been rejected');

        return $this->successResponse('Rejected successfully');
    }

    ########################## Helper Functions ############################

    private function validateStoreAppointment($data, $doctor)
    {
        // Check if the patient already has a pending appointment with the doctor
        if (Appointment::where([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'status' => 'pending',
        ])->exists()) {
            return $this->errorResponse('You already have a pending appointment with this doctor', 409);
        }

        // Check if the doctor already has an appointment at the given date and time
        if (Appointment::where([
            'date' => $data['date'],
            'time' => $data['time'],
        ])->where('status', '!=', 'rejected')->exists()) {
            return $this->errorResponse('This time is not available', 409);
        }

        // Check if the time is within the doctor's working hours
        $workingHours = $doctor->workingHours()->where('day_name', $data['day_name'])->first();
        if (!$workingHours) {
            return $this->errorResponse('No working hours available for this doctor on the specified day', 409);
        }

        $appointmentTime = Carbon::parse("{$data['date']} {$data['time']}");
        $startTime = Carbon::parse("{$data['date']} {$workingHours->start_time}");
        $endTime = Carbon::parse("{$data['date']} {$workingHours->end_time}");

        if (!$appointmentTime->between($startTime, $endTime)) {
            return $this->errorResponse('This time is not within the doctor\'s working hours', 409);
        }

        // Ensure the appointment time aligns with the doctor's appointment slot intervals
        if ($appointmentTime->diffInMinutes($startTime) % 15 !== 0 || $appointmentTime->equalTo($endTime)) {
            return $this->errorResponse('This time does not align with the doctor\'s appointment slot intervals', 409);
        }

        // All validations passed
        return true;
    }
}
