<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\EventsListingController;
use App\Http\Controllers\TeachingListingController;
use App\Http\Controllers\Api\V1\EventController;

Route::get('/', [LandingPageController::class, 'index'])->name('home');

Route::get('/debug-config', function () {
    $storagePath = storage_path('app/public');
    $publicLink = public_path('storage');

    return [
        'app_url' => config('app.url'),
        'asset_url' => config('app.asset_url'),
        'filesystem_driver' => config('filesystems.default'),
        'public_disk_config' => config('filesystems.disks.public'),
        'storage_path' => $storagePath,
        'storage_path_exists' => is_dir($storagePath),
        'storage_path_perms' => substr(sprintf('%o', fileperms($storagePath)), -4),
        'public_link' => $publicLink,
        'public_link_exists' => file_exists($publicLink),
        'public_link_is_link' => is_link($publicLink),
        'public_link_target' => is_link($publicLink) ? readlink($publicLink) : 'NOT_A_LINK',
        'files_in_storage' => \Illuminate\Support\Facades\File::files($storagePath),
        'directories_in_storage' => \Illuminate\Support\Facades\File::directories($storagePath),
    ];
});

Route::get('/events', [EventsListingController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventsListingController::class, 'show'])->name('events.show');
Route::post('/events/{event}/register', [EventsListingController::class, 'register'])->name('events.register');

Route::get('/teachings', [TeachingListingController::class, 'index'])->name('teachings.index');
Route::get('/teachings/{teaching:slug}', [TeachingListingController::class, 'show'])->name('teachings.show');

Route::get('/payment/callback', [LandingPageController::class, 'paymentCallback'])->name('payment.callback');

Route::get('/privacy-policy', [LandingPageController::class, 'privacy'])->name('privacy');
Route::get('/terms-of-service', [LandingPageController::class, 'terms'])->name('terms');
Route::get('/contact-us', [LandingPageController::class, 'contact'])->name('contact');
Route::get('/account-deletion', [LandingPageController::class, 'deletion'])->name('deletion');

// Custom Firebase Password Reset
Route::get('/password/reset', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
