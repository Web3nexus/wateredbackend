<?php

use App\Models\Audio;
use App\Models\ContentCategory;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Content Categories ---\n";
$categories = ContentCategory::all();
foreach ($categories as $cat) {
    echo "ID: {$cat->id} | Name: '{$cat->name}' | Slug: '{$cat->slug}' | Type: '{$cat->type}'\n";
}

echo "\n--- Audios with Categories ---\n";
$audios = Audio::with('contentCategory')->get();
foreach ($audios as $audio) {
    $catName = $audio->contentCategory ? $audio->contentCategory->name : 'NULL';
    echo "Audio ID: {$audio->id} | Title: '{$audio->title}' | Category: '{$catName}' (ID: {$audio->category_id})\n";
}
