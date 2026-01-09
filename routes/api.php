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
});



// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);

    // Bookmarks
    Route::get('/bookmarks', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'index']);
    Route::post('/bookmarks', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'store']);
    Route::delete('/bookmarks/item', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'destroyByItem']);
    Route::delete('/bookmarks/{bookmark}', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'destroy']);

    // Bookings
    Route::get('/bookings', [\App\Http\Controllers\Api\V1\BookingController::class, 'index']);
    Route::post('/bookings', [\App\Http\Controllers\Api\V1\BookingController::class, 'store']);
});
