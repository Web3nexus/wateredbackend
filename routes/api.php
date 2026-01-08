<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/settings', [\App\Http\Controllers\Api\V1\SettingsController::class, 'index']);

    // Traditions endpoints
    Route::get('/traditions', [\App\Http\Controllers\Api\V1\TraditionController::class, 'index']);
    Route::get('/traditions/{tradition}', [\App\Http\Controllers\Api\V1\TraditionController::class, 'show']);
    Route::get('/traditions/{tradition}/collections', [\App\Http\Controllers\Api\V1\TraditionController::class, 'collections']);

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
    Route::get('/audios', [\App\Http\Controllers\Api\V1\AudioController::class, 'index']);
    Route::get('/audios/{audio}', [\App\Http\Controllers\Api\V1\AudioController::class, 'show']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
