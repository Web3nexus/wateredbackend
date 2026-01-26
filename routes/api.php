<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/settings', [\App\Http\Controllers\Api\V1\SettingsController::class, 'index']);

    // Traditions endpoints
    Route::get('/traditions', [\App\Http\Controllers\Api\V1\TraditionController::class, 'index']);
    Route::get('/traditions/{tradition}', [\App\Http\Controllers\Api\V1\TraditionController::class, 'show']);
    Route::get('/traditions/{tradition}/collections', [\App\Http\Controllers\Api\V1\TraditionController::class, 'collections']);

    // Commerce endpoints
    Route::get('/products', [\App\Http\Controllers\Api\V1\ProductController::class, 'index']);
    Route::get('/products/{product}', [\App\Http\Controllers\Api\V1\ProductController::class, 'show']);
    Route::post('/checkout', [\App\Http\Controllers\Api\V1\ProductController::class, 'checkout']);

    // Temple endpoints
    Route::get('/temples', [\App\Http\Controllers\Api\V1\TempleController::class, 'index']);
    Route::get('/temples/near-me', [\App\Http\Controllers\Api\V1\TempleController::class, 'nearMe']);
    Route::get('/temples/{temple}', [\App\Http\Controllers\Api\V1\TempleController::class, 'show']);

    // Consultation Types (Public)
    Route::get('/consultation-types', [\App\Http\Controllers\Api\V1\BookingController::class, 'indexTypes']);

    // Search & Wisdom
    Route::get('/search', [\App\Http\Controllers\Api\V1\SearchController::class, 'search']);
    Route::get('/daily-wisdom', [\App\Http\Controllers\Api\V1\DailyWisdomController::class, 'index']);

    // Collections endpoints
    Route::get('/collections/{collection}', [\App\Http\Controllers\Api\V1\CollectionController::class, 'show']);
    Route::get('/collections/{collection}/chapters', [\App\Http\Controllers\Api\V1\CollectionController::class, 'chapters']);

    // Chapters endpoints
    Route::get('/chapters/{chapter}', [\App\Http\Controllers\Api\V1\ChapterController::class, 'show']);
    Route::get('/chapters/{chapter}/entries', [\App\Http\Controllers\Api\V1\ChapterController::class, 'entries']);

    // Entries endpoints
    Route::get('/entries/{entry}', [\App\Http\Controllers\Api\V1\EntryController::class, 'show']);

    // Video Feed endpoints
    Route::get('/videos', [\App\Http\Controllers\Api\V1\VideoController::class, 'index']);
    Route::get('/videos/{video}', [\App\Http\Controllers\Api\V1\VideoController::class, 'show']);

    // Audio Feed endpoints
    // Audio Feed endpoints
    Route::get('/audios', [\App\Http\Controllers\Api\V1\AudioController::class, 'index']);
    Route::get('/audios/{audio}', [\App\Http\Controllers\Api\V1\AudioController::class, 'show']);

    // Auth Routes (Public)
    Route::post('/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('/social-login', [\App\Http\Controllers\Api\V1\AuthController::class, 'socialLogin']);

    // Community Posts (Public Read)
    Route::get('/community/posts', [\App\Http\Controllers\Api\V1\CommunityController::class, 'index']);
    Route::get('/community/posts/{post}/comments', [\App\Http\Controllers\Api\V1\CommunityController::class, 'comments']);

    // Watered Calendar (Public)
    Route::prefix('calendar')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\V1\CalendarController::class, 'index']);
        Route::get('/month/{month}', [\App\Http\Controllers\Api\V1\CalendarController::class, 'getMonth']);
        Route::get('/special-days', [\App\Http\Controllers\Api\V1\CalendarController::class, 'getSpecialDays']);
        Route::get('/today', [\App\Http\Controllers\Api\V1\CalendarController::class, 'getToday']);
    });
});



// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Debug Permission Route (Temporary)
    require __DIR__ . '/debug_permissions.php';

    Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);

    // Bookmarks
    Route::get('/bookmarks', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'index']);
    Route::post('/bookmarks', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'store']);
    Route::delete('/bookmarks/item', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'destroyByItem']);
    Route::delete('/bookmarks/{bookmark}', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'destroy']);

    // Bookings
    Route::get('/consultation-types', [\App\Http\Controllers\Api\V1\BookingController::class, 'indexTypes']);
    Route::get('/bookings', [\App\Http\Controllers\Api\V1\BookingController::class, 'index']);
    Route::post('/bookings', [\App\Http\Controllers\Api\V1\BookingController::class, 'store']);

    // Community (Authenticated Actions Only)
    Route::prefix('community')->group(function () {
        Route::post('/posts', [\App\Http\Controllers\Api\V1\CommunityController::class, 'store']);
        Route::delete('/posts/{post}', [\App\Http\Controllers\Api\V1\CommunityController::class, 'destroy']);

        Route::post('/posts/{post}/comments', [\App\Http\Controllers\Api\V1\CommunityController::class, 'storeComment']);

        Route::post('/posts/{post}/like', [\App\Http\Controllers\Api\V1\CommunityController::class, 'toggleLike']);
    });

    // Events
    Route::get('/events', [\App\Http\Controllers\Api\V1\EventController::class, 'index']);
    Route::get('/events/{event}', [\App\Http\Controllers\Api\V1\EventController::class, 'show']);
    Route::post('/events/{event}/register', [\App\Http\Controllers\Api\V1\EventController::class, 'register']);
    Route::delete('/events/{event}/register', [\App\Http\Controllers\Api\V1\EventController::class, 'cancel']);

    // Rituals
    Route::get('/rituals', [\App\Http\Controllers\Api\V1\RitualController::class, 'index']);
    Route::get('/rituals/{ritual}', [\App\Http\Controllers\Api\V1\RitualController::class, 'show']);

    // Incantations
    Route::get('/incantations', [\App\Http\Controllers\Api\V1\IncantationController::class, 'index']);
    Route::get('/incantations/{incantation}', [\App\Http\Controllers\Api\V1\IncantationController::class, 'show']);

    // Deities
    Route::get('/deities', [\App\Http\Controllers\Api\V1\DeityController::class, 'index']);
    Route::get('/deities/{deity}', [\App\Http\Controllers\Api\V1\DeityController::class, 'show']);

    // Subscriptions
    Route::get('/subscription', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'index']);
    Route::post('/subscription/verify', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'verify']);

    // Reminders
    Route::get('/reminders', [\App\Http\Controllers\Api\V1\ReminderController::class, 'index']);
    Route::post('/reminders', [\App\Http\Controllers\Api\V1\ReminderController::class, 'store']);
    Route::put('/reminders/{reminder}', [\App\Http\Controllers\Api\V1\ReminderController::class, 'update']);
    Route::delete('/reminders/{reminder}', [\App\Http\Controllers\Api\V1\ReminderController::class, 'destroy']);




});
