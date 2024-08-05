<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordRequest;
use App\Http\Requests\Auth\PasswordUpdateRequest;
use App\Traits\ApiResponseTrait;

class PasswordController extends Controller
{
    use ApiResponseTrait;

    public function confirm(PasswordRequest $request)
    {
        $data = $request->validated();
        $data['email'] = $request->user()->email;

        if (!auth()->validate($data)) {
            return $this->errorResponse('Invalid password', 401);
        }

        return $this->successResponse('Password confirmed successfully');
    }

    public function update(PasswordUpdateRequest $request)
    {
        $data = $request->validated();

        $request->user()->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }
}
