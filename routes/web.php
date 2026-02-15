<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\EventsListingController;
use App\Http\Controllers\Api\V1\EventController;

Route::get('/', [LandingPageController::class, 'index'])->name('home');

Route::get('/events', [EventsListingController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventsListingController::class, 'show'])->name('events.show');
Route::post('/events/{event}/register', [EventsListingController::class, 'register'])->name('events.register');
Route::get('/payment/callback', [LandingPageController::class, 'paymentCallback'])->name('payment.callback');
