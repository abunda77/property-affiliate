<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: [
            \App\Http\Middleware\TrustProxies::class,
        ]);
        
        $middleware->web(prepend: [
            \App\Http\Middleware\ForceHttpsDetection::class,
        ]);
        
        $middleware->web(append: [
            \App\Http\Middleware\AffiliateTrackingMiddleware::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Generate sitemap daily at 2:00 AM
        $schedule->command('sitemap:generate')->dailyAt('02:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle validation exceptions with user-friendly messages
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Handle authorization exceptions with 403 responses
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to perform this action.',
                    'code' => 'UNAUTHORIZED',
                ], 403);
            }

            return response()->view('errors.403', [
                'message' => $e->getMessage() ?: 'You are not authorized to perform this action.',
            ], 403);
        });

        // Handle model not found exceptions with 404 pages
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                    'code' => 'NOT_FOUND',
                ], 404);
            }

            return response()->view('errors.404', [
                'message' => 'The resource you are looking for could not be found.',
            ], 404);
        });

        // Log all unexpected errors with context
        $exceptions->report(function (\Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }

            $userId = null;
            try {
                $userId = \Illuminate\Support\Facades\Auth::id();
            } catch (\Exception $authException) {
                // Ignore auth errors during error logging
            }

            \Illuminate\Support\Facades\Log::error('Unexpected error occurred', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_id' => $userId,
            ]);
        });
    })->create();
