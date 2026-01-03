<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Check if user is admin
        // Method 1: Using 'is_admin' column in users table
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized. Admin access only.');
        }

        // OR Method 2: Using 'role' column
        // if (auth()->user()->role !== 'admin') {
        //     abort(403, 'Unauthorized. Admin access only.');
        // }

        // OR Method 3: Using specific admin emails
        // $adminEmails = ['admin@qlo.com', 'superadmin@qlo.com'];
        // if (!in_array(auth()->user()->email, $adminEmails)) {
        //     abort(403, 'Unauthorized. Admin access only.');
        // }

        return $next($request);
    }
}

