<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailRequest;
use App\Http\Requests\Auth\VerificationRequest;
use App\Mail\VerificationEmail;
use App\Models\Otp;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Mail;

class VerificationEmailController extends Controller
{
    use ApiResponseTrait;

    public function create(EmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return $this->successResponse('Email already verified');
        }

        $otp = rand(100000, 999999);

        $data['title'] = "HMS Support";
        $data['otp'] = $otp;
        $email = $user->email;
        Mail::send('emails.sendEmailOtp',  ['otp' => $data['otp']], function ($message) use ($data, $email) {
            $message->to($email)->subject($data['title']);
        });

        Otp::updateOrCreate(['user_id' => $user->id], ['code' => $otp]);

      
        // Mail::to($user->email)->send(new VerificationEmail($user->full_name, $otp));

        return $this->successResponse('OTP sent successfully');
    }

    public function store(VerificationRequest $request)
    {
        $user = User::where('email', $request->email)->with('otp')->first();

        if ($user->hasVerifiedEmail()) {
            return $this->successResponse('Email already verified');
        } elseif (!$user->otp || $user->otp->code != $request->otp) {
            return $this->errorResponse('Invalid OTP code', 401);
        } elseif ($user->otp->updated_at->addMinutes(10)->isPast()) {
            return $this->errorResponse('OTP code expired', 401);
        }

        $user->otp()->delete();
        $user->markEmailAsVerified();

        return $this->successResponse('Email verified successfully');
    }
}
