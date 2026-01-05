<?php

// ============================================
// FINAL SECURE ADMIN MIDDLEWARE
// File: app/Http/Middleware/AdminMiddleware.php
// ============================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * List of authorized admin emails
     */
    private array $authorizedAdmins = [
        // 'dcadmin@gmail.com',
        'dcadmin@gmail.com',
        // Add more admin emails here if needed
        // 'admin2@gmail.com',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check authentication
        if (!auth()->check()) {
            Log::warning('Unauthorized admin access attempt - Not logged in', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->route('login')
                ->with('error', 'Please login first');
        }

        $user = auth()->user();

        // Check if email exists
        if (!$user->email) {
            Log::error('User without email tried to access admin area', [
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);

            abort(403, 'Invalid user credentials');
        }

        // Check if email is in authorized admins list
        if (!in_array($user->email, $this->authorizedAdmins)) {
            Log::warning('Unauthorized email tried to access admin area', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'attempt_time' => now()->toDateTimeString()
            ]);

            abort(403, 'Unauthorized. Admin access only.');
        }

        // Check if user has name
        if (!$user->name) {
            Log::warning('Admin user with incomplete profile', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        // Verify password is set (hashed)
        if (!$user->password) {
            Log::error('Admin user without password hash', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            abort(403, 'Invalid user credentials');
        }

        // Log successful admin access
        Log::info('Admin access granted', [
            'admin_id' => $user->id,
            'admin_email' => $user->email,
            'admin_name' => $user->name,
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'access_time' => now()->toDateTimeString()
        ]);

        return $next($request);
    }

    /**
     * Check if email is authorized admin
     */
    private function isAuthorizedAdmin(string $email): bool
    {
        return in_array($email, $this->authorizedAdmins);
    }
}
