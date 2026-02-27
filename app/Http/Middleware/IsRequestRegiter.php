<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsRequestRegiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role =  Auth::user()->role;

        if (in_array($role, ['admin', 'stucent_register'])) {
            return $next($request);
        }

        abort(403, 'شما اجازه دسترسی به این بخش را ندارید.');
    }
}
