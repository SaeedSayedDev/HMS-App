<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Mail\PasswordResetEmail;
use App\Models\Otp;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Mail;

class PasswordResetEmailController extends Controller
{
    use ApiResponseTrait;

    public function create(EmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $otp = rand(100000, 999999);

        Otp::updateOrCreate(['user_id' => $user->id], ['code' => $otp]);
        Mail::to($user->email)->send(new PasswordResetEmail($user->full_name, $otp));

        return $this->successResponse('OTP sent successfully');
    }

    public function store(PasswordResetRequest $request)
    {
        $user = User::where('email', $request->email)->with('otp')->first();

        if (!$user->otp || $user->otp->code != $request->otp) {
            $this->errorResponse('Invalid OTP code', 422);
        } elseif ($user->otp->updated_at->addMinutes(10)->isPast()) {
            $this->errorResponse('OTP code expired', 422);
        }

        $user->otp()->delete();
        $user->update(['password' => $request->password]);

        return $this->successResponse('Password reset successfully');
    }
}
