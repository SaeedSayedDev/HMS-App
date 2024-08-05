<?php

namespace App\Http\Middleware;

use App\Models\AdminPermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = auth()->user();
        $adminPermission = AdminPermission::where('admin_id', $user->id)->first();

        if (!$adminPermission || !$adminPermission->{$permission}) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
