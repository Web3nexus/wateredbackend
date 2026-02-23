<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Firebase Diagnostic</title><style>body{font-family:system-ui,-apple-system,sans-serif;max-width:800px;margin:2rem auto;padding:0 1rem;line-height:1.5;color:#1f2937}h1,h2{color:#111827}.success{color:#059669;font-weight:bold;}.error{color:#dc2626;font-weight:bold;}pre{background:#f3f4f6;padding:1rem;border-radius:0.5rem;overflow-x:auto;font-size:0.875em}</style></head><body>";
echo "<h1>🔥 Firebase Diagnostic Tool</h1>";

$possibleRoots = [
    __DIR__,
    __DIR__ . '/..',
    dirname(__DIR__)
];

$rootPath = null;
foreach ($possibleRoots as $path) {
    if (file_exists($path . '/artisan')) {
        $rootPath = $path;
        break;
    }
}

if (!$rootPath) {
    die("<p class='error'>❌ Could not find Laravel root (artisan file).</p></body></html>");
}

echo "<p><strong>Laravel Root:</strong> " . htmlspecialchars($rootPath) . "</p>";

$jsonPath = $rootPath . '/watered-c14bb-firebase-adminsdk-fbsvc-cf02191074.json';
echo "<p><strong>Looking for JSON at:</strong> " . htmlspecialchars($jsonPath) . "</p>";

if (!file_exists($jsonPath)) {
    die("<p class='error'>❌ JSON file NOT FOUND at the expected path! Make sure you uploaded it to the correct folder.</p></body></html>");
}

echo "<p class='success'>✅ JSON file exists!</p>";

$jsonContent = file_get_contents($jsonPath);
$data = json_decode($jsonContent, true);

if (!$data) {
    die("<p class='error'>❌ Failed to decode JSON. The file might be corrupted.</p></body></html>");
}

echo "<p class='success'>✅ JSON decoded successfully. Client Email: " . htmlspecialchars($data['client_email']) . "</p>";

if (!function_exists('openssl_sign')) {
    die("<p class='error'>❌ openssl PHP extension is not enabled on this server!</p></body></html>");
}

echo "<p class='success'>✅ OpenSSL is enabled.</p>";

$signature = '';
$success = openssl_sign("test payload", $signature, $data['private_key'], 'SHA256');

if (!$success) {
    die("<p class='error'>❌ Failed to sign using private key. This indicates a problem with the private key formatting or OpenSSL setup. Error: " . openssl_error_string() . "</p></body></html>");
}

echo "<p class='success'>✅ Successfully generated test signature using private key.</p>";

echo "<h2>📜 Recent Laravel Logs</h2>";
$logFile = $rootPath . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -60);
    echo "<pre>";
    foreach ($lastLines as $line) {
        $formatted = htmlspecialchars($line);
        if (strpos($formatted, 'ERROR') !== false || strpos($formatted, 'failed') !== false || strpos($formatted, 'Exception') !== false) {
            echo "<span style='color:#dc2626;'>$formatted</span>";
        } else {
            echo $formatted;
        }
    }
    echo "</pre>";
} else {
    echo "<p class='warning'>No laravel.log found.</p>";
}

echo "<h2>Done.</h2><p>Please take a screenshot of this page or copy its contents for debugging.</p>";
echo "</body></html>";
