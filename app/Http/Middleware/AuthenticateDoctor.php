<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateDoctor
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role_id != 2) {
            return $this->errorResponse('Unauthorized', 401);
        }

        return $next($request);
    }
}
