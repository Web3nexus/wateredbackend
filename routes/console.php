<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // Added this line for Schedule facade

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send Ritual Reminders every minute
Schedule::command('rituals:send-reminders')->everyMinute();

// Expire subscriptions past their end date (runs every 5 minutes on the hour window)
Schedule::command('subscriptions:expire')->everyFiveMinutes();
