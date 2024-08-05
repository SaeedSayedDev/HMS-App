<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\FirebaseToken;
use App\Models\User;
use App\Traits\ApiResponseTrait;

class AuthController extends Controller
{
    use ApiResponseTrait;
    
    public function __construct(protected VerificationEmailController $verificationEmailController) {}

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        $this->verificationEmailController->create(new EmailRequest(['email' => $user->email]));

        return $this->respondWithData('Register successfully', $user);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if (!$token = auth()->attempt($data)) {
            return $this->errorResponse('Unauthorized', 401);
        }

        if ($fcm_token = $request->header('fcm_token')) {
            FirebaseToken::updateOrCreate(
                ['fcm_token' => $fcm_token],
                ['user_id' => auth()->id()]
            );
        }

        return $this->respondWithToken('Login successfully', auth()->user(), $token);
    }

    public function logout()
    {
        auth()->logout();

        return $this->successResponse('Logout successfully');
    }

    public function me()
    {
        return $this->respondWithData('Retrieved successfully', auth()->user());
    }
}
