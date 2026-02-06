<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('user_role') !== 'admin') {
            // If the user is not an admin, redirect them to a different page
            return redirect('/')->with('error', 'Unauthorized! PLease login First'); // You can change this to any route you want
        }

        return $next($request);
    }
}
