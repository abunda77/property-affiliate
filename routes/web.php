<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// DEBUG ROUTE
Route::get('/debug-request', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'url' => $request->fullUrl(),
        'method' => $request->method(),
        'host' => $request->getHost(),
        'ip' => $request->ip(),
        'headers' => $request->headers->all(),
        'server' => $request->server->all(),
    ]);
});

// Redirect home to property catalog
Route::get('/', function () {
    return redirect()->route('properties.index');
});

// Public property catalog routes
Route::get('/properties', function () {
    return view('properties.index');
})->name('properties.index');

Route::get('/p/{slug}', function ($slug) {
    return view('properties.show', ['slug' => $slug]);
})->name('property.show');

// Legal pages routes
Route::get('/terms-and-conditions', function () {
    return view('legal.terms');
})->name('legal.terms');

Route::get('/privacy-policy', function () {
    return view('legal.privacy');
})->name('legal.privacy');

Route::get('/disclaimer', function () {
    return view('legal.disclaimer');
})->name('legal.disclaimer');

Route::get('/about-us', function () {
    return view('about-us');
})->name('about-us');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Referral redirect route - redirects to property catalog with affiliate tracking
Route::get('/ref/{affiliate_code}', function ($affiliate_code) {
    // Validate affiliate code exists
    $affiliate = \App\Models\User::where('affiliate_code', $affiliate_code)
        ->where('status', \App\Enums\UserStatus::ACTIVE)
        ->first();

    if ($affiliate) {
        // Set affiliate cookie for 30 days (43200 minutes)
        cookie()->queue('affiliate_id', $affiliate->id, 43200);

        // Record the visit via the tracking middleware
        // The middleware will handle visit recording
    }

    // Redirect to property catalog with ref parameter
    // This ensures the tracking middleware processes it
    return redirect()->route('properties.index', ['ref' => $affiliate_code]);
})->name('referral.redirect');

// Affiliate promotional materials download route
Route::middleware(['auth'])->group(function () {
    Route::get('/affiliate/promo/download/{property}', [App\Http\Controllers\AffiliatePromoController::class, 'download'])
        ->name('affiliate.download-promo');
});

// Dynamic robots.txt
Route::get('/robots.txt', function () {
    $content = "User-agent: *\nDisallow:\n\nSitemap: ".url('/sitemap.xml');

    return response($content)->header('Content-Type', 'text/plain');
});

// Auth Route Redirects to Filament
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

Route::get('/register', function () {
    return redirect()->route('filament.admin.auth.register');
})->name('register');

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect('/admin');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});
