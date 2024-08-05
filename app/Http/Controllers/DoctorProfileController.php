<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorProfileRequest;
use App\Http\Requests\StoreDoctorProfileRequest;
use App\Http\Requests\UpdateDoctorProfileRequest;
use App\Http\Requests\WorkingHoursRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorProfileController extends Controller
{
    use ApiResponseTrait, ImageTrait;

    private $authUser;

    public function __construct(Request $request)
    {
        $this->authUser = $request->user();
    }

    public function show()
    {
        $doctor = $this->authUser->load('doctor', 'image');

        return $this->respondWithData('Retrieved successfully', $doctor);
    }

    public function update(DoctorProfileRequest $request)
    {
        $data = $request->validated();

        $this->authUser->update($data);
        $this->authUser->doctor()->updateOrCreate(['user_id' => $this->authUser->id], $data);
        $this->updateImage($this->authUser, $request->file('image'), 'images/doctors');

        $doctor = $this->authUser->load('doctor', 'image');

        return $this->respondWithData('Updated successfully', $doctor);
    }


    public function destroy()
    {
        $this->deleteImage($this->authUser);
        $this->authUser->delete();

        return $this->successResponse('Deleted successfully');
    }

    ####################################### Admin ###############################



    public function doctors()
    {
        $doctors = User::where('role_id', 2)->get()->load('doctor', 'image', 'workingHours');

        return $this->respondWithData('Retrieved successfully', $doctors);
    }

    public function showDoctor($doctor_id)
    {
        $doctor = User::find($doctor_id)->load('doctor', 'image', 'workingHours');

        return $this->respondWithData('Retrieved successfully', $doctor);
    }

    public function storeDoctor(StoreDoctorProfileRequest $request)
    {
        $data = $request->validated();
        $data['role_id'] = 2;
        $data['password'] = Hash::make($request->password);

        $doctor = User::create($data);
        $doctor->doctor()->updateOrCreate(['user_id' => $this->authUser->id], $data);
        $this->storeImage($doctor, $request->file('image'), 'images/doctors');

        $doctor->load('doctor', 'image');

        return $this->respondWithData('Stores successfully', $doctor);
    }

    public function updateDoctor(DoctorProfileRequest $request, $doctor_id)
    {
        $data = $request->validated();
        $doctor = User::find($doctor_id);
        DB::beginTransaction();
        $doctor->update($data);

        $doctor->doctor()->update($data);
        $this->updateImage($this->authUser, $request->file('image'), 'images/doctors');
        DB::commit();

        $doctor = $this->authUser->load('doctor', 'image');

        return $this->respondWithData('Updated successfully', $doctor);
    }

    public function updateWorkingHours(WorkingHoursRequest $request, $doctor_id)
    {
        $data = $request->validated();
        if (!$this->isTimeAligned($request->start_time, 15) || !$this->isTimeAligned($request->end_time, 15)) {
            return $this->errorResponse('Start and end time must be aligned with 15 minutes');
        }

        $doctor = User::find($doctor_id);
        $doctor->workingHours()->updateOrCreate(['doctor_id' => $doctor_id, 'day_name' => $request->day], $data);

        return $this->successResponse('Updated successfully');
    }

    public function destroyDoctor($doctor_id)
    {
        $doctor = User::where('role_id', 2)->find($doctor_id);

        $this->deleteImage($doctor);
        $doctor->delete();

        return $this->successResponse('Deleted successfully');
    }

    private function isTimeAligned($time, $interval)
    {
        $minutes = date('i', strtotime($time));
        return $minutes % $interval === 0;
    }
}
