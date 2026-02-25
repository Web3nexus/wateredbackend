<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Webhooks (Public - Required for payment processing)
    Route::post('/webhooks/apple', [\App\Http\Controllers\Api\V1\WebhookController::class, 'apple']);
    Route::post('/webhooks/paystack', [\App\Http\Controllers\Api\V1\WebhookController::class, 'paystack']);

    // Authentication Routes (Public - Required for app access)
    Route::post('/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::post('/social-login', [\App\Http\Controllers\Api\V1\AuthController::class, 'socialLogin']);
    Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\Api\V1\AuthController::class, 'verify'])
        ->name('verification.verify');

    // Password Reset Routes (Public)
    Route::post('/forgot-password', [\App\Http\Controllers\Api\V1\AuthController::class, 'forgotPassword'])
        ->middleware('throttle:6,1')
        ->name('password.email');
    Route::post('/reset-password', [\App\Http\Controllers\Api\V1\AuthController::class, 'resetPassword'])
        ->name('password.update');

    // Appointment Tracking & Guest Submission (Public)
    Route::get('/appointments/track/{code}', [\App\Http\Controllers\Api\V1\AppointmentController::class, 'show']);
    Route::post('/appointments/guest', [\App\Http\Controllers\Api\V1\AppointmentController::class, 'store']);
    Route::get('/consultation-types', [\App\Http\Controllers\Api\V1\AppointmentController::class, 'indexTypes']);

    // Protected Routes (Sanctum) - ALL app content requires authentication
    Route::middleware('auth:sanctum')->group(function () {
        // App Settings & Legal Documents (Now Protected)
        Route::get('/settings', [\App\Http\Controllers\Api\V1\SettingsController::class, 'index']);
        Route::get('/legal-documents', [\App\Http\Controllers\Api\V1\SettingsController::class, 'legalDocuments']);
        Route::get('/payment-keys', [\App\Http\Controllers\Api\V1\SettingsController::class, 'paymentKeys']);

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

        // Chapters & Entries
        Route::get('/chapters/{chapter}', [\App\Http\Controllers\Api\V1\ChapterController::class, 'show']);
        Route::get('/chapters/{chapter}/entries', [\App\Http\Controllers\Api\V1\ChapterController::class, 'entries']);
        Route::get('/entries/{entry}', [\App\Http\Controllers\Api\V1\EntryController::class, 'show']);

        // Audio Teachings
        Route::get('/audios', [\App\Http\Controllers\Api\V1\AudioController::class, 'index']);
        Route::get('/audio-categories', [\App\Http\Controllers\Api\V1\AudioController::class, 'categories']);
        Route::get('/audios/{audio}', [\App\Http\Controllers\Api\V1\AudioController::class, 'show']);

        // Announcements
        Route::get('/announcements', [\App\Http\Controllers\Api\V1\AnnouncementController::class, 'index']);
        Route::get('/announcements/{id}', [\App\Http\Controllers\Api\V1\AnnouncementController::class, 'show']);
        Route::post('/announcements/{id}/read', [\App\Http\Controllers\Api\V1\AnnouncementController::class, 'markRead']);

        // Sacred Daily Rituals - NOT behind premium (these appear on the home screen for all users)
        Route::get('/sacred-daily-rituals', [\App\Http\Controllers\Api\V1\RitualController::class, 'sacredDaily']);

        // Rituals & Deities & Holidays
        Route::middleware('premium')->group(function () {
            Route::get('/rituals', [\App\Http\Controllers\Api\V1\RitualController::class, 'index']);
            Route::get('/rituals/{ritual}', [\App\Http\Controllers\Api\V1\RitualController::class, 'show']);
            Route::get('/incantations', [\App\Http\Controllers\Api\V1\IncantationController::class, 'index']);
            Route::get('/incantations/{incantation}', [\App\Http\Controllers\Api\V1\IncantationController::class, 'show']);
        });

        Route::get('/deities', [\App\Http\Controllers\Api\V1\DeityController::class, 'index']);
        Route::get('/deities/{deity}', [\App\Http\Controllers\Api\V1\DeityController::class, 'show']);

        Route::get('/holidays', [\App\Http\Controllers\Api\V1\HolidayController::class, 'index']);

        // Calendar
        Route::prefix('calendar')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V1\CalendarController::class, 'index']);
            Route::get('/month/{month}', [\App\Http\Controllers\Api\V1\CalendarController::class, 'getMonth']);
            Route::get('/special-days', [\App\Http\Controllers\Api\V1\CalendarController::class, 'getSpecialDays']);
            Route::get('/today', [\App\Http\Controllers\Api\V1\CalendarController::class, 'getToday']);
        });

        Route::post('/email/resend', [\App\Http\Controllers\Api\V1\AuthController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);

        // Profile
        Route::get('/user', [\App\Http\Controllers\Api\V1\ProfileController::class, 'show']);
        Route::get('/profile', [\App\Http\Controllers\Api\V1\ProfileController::class, 'show']);
        Route::get('/orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'index']);
        Route::get('/orders/{order}', [\App\Http\Controllers\Api\V1\OrderController::class, 'show']);
        Route::post('/orders/{order}/apply', [\App\Http\Controllers\Api\V1\OrderApplicationController::class, 'store']);
        Route::get('/order-applications', [\App\Http\Controllers\Api\V1\OrderApplicationController::class, 'index']);
        Route::post('/profile/update', [\App\Http\Controllers\Api\V1\ProfileController::class, 'update']);
        Route::post('/profile/photo', [\App\Http\Controllers\Api\V1\ProfileController::class, 'uploadPhoto']);
        Route::delete('/profile', [\App\Http\Controllers\Api\V1\ProfileController::class, 'destroy']);

        // Bookmarks
        Route::get('/bookmarks', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'index']);
        Route::post('/bookmarks', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'store']);
        Route::delete('/bookmarks/item', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'destroyByItem']);
        Route::delete('/bookmarks/{bookmark}', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'destroy']);

        // Appointments (formerly Bookings)
        Route::get('/appointments', [\App\Http\Controllers\Api\V1\AppointmentController::class, 'index']);
        Route::get('/bookings', [\App\Http\Controllers\Api\V1\AppointmentController::class, 'index']); // Legacy support
        Route::post('/appointments', [\App\Http\Controllers\Api\V1\AppointmentController::class, 'store']);
        Route::post('/bookings', [\App\Http\Controllers\Api\V1\AppointmentController::class, 'store']); // Legacy support


        // Interactions
        Route::post('/interact/like', [\App\Http\Controllers\Api\V1\InteractionController::class, 'toggleLike']);
        Route::post('/interact/comment', [\App\Http\Controllers\Api\V1\InteractionController::class, 'storeComment']);
        Route::get('/interact/comments', [\App\Http\Controllers\Api\V1\InteractionController::class, 'indexComments']);

        // Subscriptions
        Route::get('/subscription', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'index']);
        Route::post('/subscription/verify', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'verify']);
        Route::post('/subscription/sync', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'sync']);
        Route::post('/subscription/trial', [\App\Http\Controllers\Api\V1\SubscriptionController::class, 'startTrial']);

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

        // User Activity Tracking
        Route::post('/activity/track', [\App\Http\Controllers\Api\V1\ActivityController::class, 'store']);
        Route::get('/activity/history', [\App\Http\Controllers\Api\V1\ActivityController::class, 'index']);

        // Event Registration and Reminders
        Route::post('/events/{event}/register', [\App\Http\Controllers\Api\V1\EventController::class, 'register']);
        Route::delete('/events/{event}/register', [\App\Http\Controllers\Api\V1\EventController::class, 'cancelRegistration']);
        Route::post('/events/{event}/payment', [\App\Http\Controllers\Api\V1\EventController::class, 'initiatePayment']);

        Route::get('/event-reminders', [\App\Http\Controllers\Api\V1\EventReminderController::class, 'index']);
        Route::post('/events/{event}/reminder', [\App\Http\Controllers\Api\V1\EventReminderController::class, 'store']);
        Route::delete('/events/{event}/reminder', [\App\Http\Controllers\Api\V1\EventReminderController::class, 'destroy']);

        // Holiday Reminders
        Route::get('/holiday-reminders', [\App\Http\Controllers\Api\V1\HolidayReminderController::class, 'index']);
        Route::post('/holiday-reminders', [\App\Http\Controllers\Api\V1\HolidayReminderController::class, 'store']);
        Route::delete('/holiday-reminders/{holiday_reminder}', [\App\Http\Controllers\Api\V1\HolidayReminderController::class, 'destroy']);

        // Informational Content (Now Protected)
        Route::get('/faqs', [\App\Http\Controllers\Api\V1\InformationalController::class, 'indexFaqs']);
        Route::get('/user-guides', [\App\Http\Controllers\Api\V1\InformationalController::class, 'indexUserGuides']);

        // Sacred Sounds
        Route::get('/sacred-sounds', [\App\Http\Controllers\Api\V1\SacredSoundController::class, 'index']);
    });

}); // End of v1 Prefix
