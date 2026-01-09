<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;

Route::get('/debug-assets', function () {
    return [
        'filament_css_url' => Vite::asset('resources/css/filament.css'),
        'app_url' => config('app.url'),
        'asset_url' => config('app.asset_url'),
    ];
});
