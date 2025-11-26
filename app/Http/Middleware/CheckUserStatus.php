<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user is blocked
            if ($user->status === UserStatus::BLOCKED) {
                // Store error message before logout
                $errorMessage = 'Your account has been blocked. Please contact the administrator.';
                
                Auth::logout();
                
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Flash error message after session regeneration
                return redirect()->route('filament.admin.auth.login')
                    ->with('error', $errorMessage);
            }
        }

        return $next($request);
    }
}
