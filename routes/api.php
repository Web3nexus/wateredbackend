<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/settings', [\App\Http\Controllers\Api\V1\SettingsController::class, 'index']);
    Route::get('/legal-documents', [\App\Http\Controllers\Api\V1\SettingsController::class, 'legalDocuments']);

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

    // Event endpoints
    Route::get('/events', [\App\Http\Controllers\Api\V1\EventController::class, 'index']);
    Route::get('/events/{event}', [\App\Http\Controllers\Api\V1\EventController::class, 'show']);

    // Consultation Types (Public)
    Route::get('/consultation-types', [\App\Http\Controllers\Api\V1\BookingController::class, 'indexTypes']);

    // Search & Wisdom
    Route::get('/search', [\App\Http\Controllers\Api\V1\SearchController::class, 'search']);
    Route::get('/daily-wisdom', [\App\Http\Controllers\Api\V1\DailyWisdomController::class, 'index']);

    // Collections endpoints
    Route::get('/text-collections', [\App\Http\Controllers\Api\V1\CollectionController::class, 'index']);
    Route::get('/textcollection', [\App\Http\Controllers\Api\V1\CollectionController::class, 'index']);
    Route::get('/textcollections', [\App\Http\Controllers\Api\V1\CollectionController::class, 'index']);
    Route::get('/text-collection', [\App\Http\Controllers\Api\V1\CollectionController::class, 'index']);
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
    Route::get('/audios', [\App\Http\Controllers\Api\V1\AudioController::class, 'index']);
    Route::get('/audios/{audio}', [\App\Http\Controllers\Api\V1\AudioController::class, 'show']);

    // Public Community
    Route::get('/community/posts', [\App\Http\Controllers\Api\V1\PostController::class, 'index']);
    Route::get('/community/posts/{post}/comments', [\App\Http\Controllers\Api\V1\CommunityController::class, 'comments']);

    // Public Events/Rituals/etc
    Route::get('/events', [\App\Http\Controllers\Api\V1\EventController::class, 'index']);
    Route::get('/events/{event}', [\App\Http\Controllers\Api\V1\EventController::class, 'show']);
    Route::get('/rituals', [\App\Http\Controllers\Api\V1\RitualController::class, 'index']);
    Route::get('/rituals/{ritual}', [\App\Http\Controllers\Api\V1\RitualController::class, 'show']);
    Route::get('/deities', [\App\Http\Controllers\Api\V1\DeityController::class, 'index']);
    Route::get('/deities/{deity}', [\App\Http\Controllers\Api\V1\DeityController::class, 'show']);
    Route::get('/incantations', [\App\Http\Controllers\Api\V1\IncantationController::class, 'index']);
    Route::get('/incantations/{incantation}', [\App\Http\Controllers\Api\V1\IncantationController::class, 'show']);
    Route::get('/holidays', [\App\Http\Controllers\Api\V1\HolidayController::class, 'index']);

    // Calendar
    Route::prefix('calendar')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\V1\CalendarController::class, 'index']);
        Route::get('/month/{month}', [\App\Http\Controllers\Api\V1\CalendarController::class, 'getMonth']);
        Route::get('/special-days', [\App\Http\Controllers\Api\V1\CalendarController::class, 'getSpecialDays']);
        Route::get('/today', [\App\Http\Controllers\Api\V1\CalendarController::class, 'getToday']);
    });

    // Auth Public
    Route::post('/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login'])->name('login');
    Route::post('/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('/social-login', [\App\Http\Controllers\Api\V1\AuthController::class, 'socialLogin']);

    // Verification Logic (Signed Routes)
    Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\Api\V1\AuthController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Public Debug Tool
    Route::get('/auth-debug', function (Request $request) {
        $email = $request->query('email');
        $user = $email ? \App\Models\User::where('email', $email)->first() : null;

        return response()->json([
            'database_config' => [
                'connection' => config('database.default'),
                'database' => config('database.connections.' . config('database.default') . '.database'),
            ],
            'searching_for' => $email,
            'user_found' => $user ? true : false,
            'user_id' => $user?->id,
            'email' => $user?->email,
            'verified_at' => $user?->email_verified_at,
            'is_verified_method' => $user?->hasVerifiedEmail(),
            'server_time' => now()->toDateTimeString(),
        ]);
    });

    // Protected Routes (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/email/resend', [\App\Http\Controllers\Api\V1\AuthController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);

        // Profile
        Route::get('/user', [\App\Http\Controllers\Api\V1\ProfileController::class, 'show']);
        Route::get('/profile', [\App\Http\Controllers\Api\V1\ProfileController::class, 'show']);
        Route::post('/profile/update', [\App\Http\Controllers\Api\V1\ProfileController::class, 'update']);
        Route::post('/profile/photo', [\App\Http\Controllers\Api\V1\ProfileController::class, 'uploadPhoto']);

        // Bookmarks
        Route::get('/bookmarks', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'index']);
        Route::post('/bookmarks', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'store']);
        Route::delete('/bookmarks/item', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'destroyByItem']);
        Route::delete('/bookmarks/{bookmark}', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'destroy']);

        // Bookings
        Route::get('/bookings', [\App\Http\Controllers\Api\V1\BookingController::class, 'index']);
        Route::post('/bookings', [\App\Http\Controllers\Api\V1\BookingController::class, 'store']);

        // Community Actions
        Route::prefix('community')->group(function () {
            Route::post('/posts', [\App\Http\Controllers\Api\V1\PostController::class, 'store']);
            Route::delete('/posts/{post}', [\App\Http\Controllers\Api\V1\CommunityController::class, 'destroy']);
            Route::post('/posts/{post}/comments', [\App\Http\Controllers\Api\V1\CommunityController::class, 'storeComment']);
            Route::post('/posts/{post}/like', [\App\Http\Controllers\Api\V1\PostController::class, 'toggleLike']);
            Route::post('/posts/{post}/share', [\App\Http\Controllers\Api\V1\PostController::class, 'share']);
        });

        // Interactions
        Route::post('/interact/like', [\App\Http\Controllers\Api\V1\InteractionController::class, 'toggleLike']);
        Route::post('/interact/comment', [\App\Http\Controllers\Api\V1\InteractionController::class, 'storeComment']);
        Route::get('/interact/comments', [\App\Http\Controllers\Api\V1\InteractionController::class, 'indexComments']);

        // Subscriptions
        Route::get('/subscription', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'index']);
        Route::post('/subscription/verify', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'verify']);

        // Reminders
        Route::get('/reminders', [\App\Http\Controllers\Api\V1\ReminderController::class, 'index']);
        Route::post('/reminders', [\App\Http\Controllers\Api\V1\ReminderController::class, 'store']);
        Route::put('/reminders/{reminder}', [\App\Http\Controllers\Api\V1\ReminderController::class, 'update']);
        Route::delete('/reminders/{reminder}', [\App\Http\Controllers\Api\V1\ReminderController::class, 'destroy']);

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
        Route::post('/notifications/token', [\App\Http\Controllers\Api\V1\NotificationController::class, 'updateToken']);
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAsRead']);
        Route::get('/notifications/settings', [\App\Http\Controllers\Api\V1\NotificationController::class, 'getSettings']);
        Route::post('/notifications/settings', [\App\Http\Controllers\Api\V1\NotificationController::class, 'updateSettings']);
    });

    // Informational Content (Public)
    Route::get('/faqs', [\App\Http\Controllers\Api\V1\InformationalController::class, 'indexFaqs']);
    Route::get('/user-guides', [\App\Http\Controllers\Api\V1\InformationalController::class, 'indexUserGuides']);

}); // End of v1 Prefix
