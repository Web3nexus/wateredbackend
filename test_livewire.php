<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$state = [
    'livewire-file:xxxxx' => 'some-temporary-path'
];

$closure = function ($state) {
    if (is_string($state) && str_starts_with($state, 'http')) {
        return str_replace(\Illuminate\Support\Facades\Storage::url(''), '', $state);
    }
    return $state;
};

var_dump($closure($state));
var_dump($closure('http://wateredbackend.test/storage/deities/xyz.jpg'));
