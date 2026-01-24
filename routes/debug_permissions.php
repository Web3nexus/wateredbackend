<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

// Add this to your api.php primarily for debugging purposes.
// Access via GET /api/debug/permissions

Route::get('/debug/permissions', function () {
    $paths = [
        storage_path(),
        storage_path('app'),
        storage_path('framework'),
        storage_path('logs'),
        bootstrap_path('cache'),
    ];

    $results = [];

    foreach ($paths as $path) {
        if (!File::exists($path)) {
            // Try to create if missing
            try {
                File::makeDirectory($path, 0775, true);
                $results[$path] = 'Created';
            } catch (\Exception $e) {
                $results[$path] = 'Missing (Failed to create: ' . $e->getMessage() . ')';
            }
        } else {
            $isWritable = is_writable($path);
            $perms = substr(sprintf('%o', fileperms($path)), -4);

            // Attempt to chmod if not writable (might not work depending on user)
            if (!$isWritable) {
                try {
                    chmod($path, 0775);
                    $isWritable = is_writable($path); // Re-check
                    $results[$path] = 'Fixed (Now ' . ($isWritable ? 'Writable' : 'Not Writable') . ')';
                } catch (\Exception $e) {
                    $results[$path] = "Not Writable ($perms) - Fix Failed: " . $e->getMessage();
                }
            } else {
                $results[$path] = "OK ($perms)";
            }
        }
    }

    return Response::json([
        'status' => 'Permission Check',
        'results' => $results,
        'server_user' => exec('whoami'),
        'php_user' => get_current_user(),
    ]);
});
