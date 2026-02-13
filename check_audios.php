<?php

use App\Models\Audio;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$audios = \App\Models\Audio::with('contentCategory')->get();

echo "Total Audios: " . $audios->count() . "\n";
foreach ($audios as $audio) {
    echo $audio->id . ": " . $audio->title . " | Category: " . ($audio->contentCategory->name ?? 'None') . " | URL: " . $audio->audio_url . "\n";
}
