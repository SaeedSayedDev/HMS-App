<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientProfileRequest;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class PatientProfileController extends Controller
{
    use ApiResponseTrait, ImageTrait;

    private $authUser;

    public function __construct(Request $request)
    {
        $this->authUser = $request->user();
    }

    public function show()
    {
        $patient = $this->authUser->load('patient', 'image');

        return $this->respondWithData('Retrieved successfully', $patient);
    }

    public function update(PatientProfileRequest $request)
    {
        $data = $request->validated();

        $this->authUser->update($data);
        $this->authUser->patient()->updateOrCreate(['user_id' => $this->authUser->id], $data);
        $this->updateImage($this->authUser, $request->file('image'), 'images/patients');

        $patient = $this->authUser->load('patient', 'image');

        return $this->respondWithData('Updated successfully', $patient);
    }

    public function destroy()
    {
        $this->deleteImage($this->authUser);
        $this->authUser->delete();

        return $this->successResponse('Deleted successfully');
    }
}
