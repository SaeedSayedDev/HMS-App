<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePatient
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role_id != 1) {
            return $this->errorResponse('Unauthorized', 401);
        }

        return $next($request);
    }
}
