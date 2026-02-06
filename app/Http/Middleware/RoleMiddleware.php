<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Basic Check: Does the session even exist?
        if (!Session::has('urs_id') || !Session::has('user_role')) {
            return redirect()->route('login')->with('errorMessage', 'Please log in first.');
        }

        // 2. Role Check: Does the role match the route requirement?
        if (Session::get('user_role') !== $role) {
            return redirect()->route('login')->with('errorMessage', 'Unauthorized access level.');
        }

        // 3. Security Layer: Prevent "Session Fixation"
        // If the user is logged in but the session is extremely old, 
        // it's safer to re-verify or keep it fresh.
        // We also add a header to prevent sensitive admin pages from being cached.
        
        $response = $next($request);

        return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }
}