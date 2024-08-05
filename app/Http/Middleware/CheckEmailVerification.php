<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmailVerification
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->hasVerifiedEmail()) {
            return $this->errorResponse('Your email address is not verified.', 401);
        }

        return $next($request);
    }
}
