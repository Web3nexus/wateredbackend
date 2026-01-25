end
<?php

/**
 * Laravel Deployment Checker & Fixer
 * Upload this file to your public_html folder (or wherever index.php is) and visit it in your browser.
 */

$requiredPhpVersion = '8.2';
$requiredExtensions = ['bcmath', 'ctype', 'fileinfo', 'json', 'mbstring', 'openssl', 'pdo', 'tokenizer', 'xml'];
$writableDirectories = [
    'storage',
    'storage/app',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Laravel Deployment Checker</title><style>body{font-family:system-ui,-apple-system,sans-serif;max-width:800px;margin:2rem auto;padding:0 1rem;line-height:1.5;color:#1f2937}h1{color:#111827}.card{border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.5rem;margin-bottom:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.1)}.success{color:#059669;background:#ecfdf5;border-color:#10b981}.error{color:#dc2626;background:#fef2f2;border-color:#ef4444}.warning{color:#d97706;background:#fffbeb;border-color:#f59e0b}code{background:#f3f4f6;padding:0.2rem 0.4rem;border-radius:0.25rem;font-size:0.875em}button{background:#2563eb;color:white;border:none;padding:0.5rem 1rem;border-radius:0.25rem;cursor:pointer;font-size:1rem}button:hover{background:#1d4ed8}</style></head><body>";

echo "<h1>🚀 Deployment Checker</h1>";

// 1. Check PHP Version
echo "<div class='card'>";
echo "<h2>PHP Version</h2>";
if (version_compare(PHP_VERSION, $requiredPhpVersion, '>=')) {
    echo "<div class='success'>✅ Current PHP version: " . PHP_VERSION . "</div>";
} else {
    echo "<div class='error'>❌ Current PHP version: " . PHP_VERSION . ". You need " . $requiredPhpVersion . " or higher. Please update via Hostinger hPanel > Advanced > PHP Configuration.</div>";
}
echo "</div>";

// 2. Check Extensions
echo "<div class='card'>";
echo "<h2>PHP Extensions</h2>";
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}
if (empty($missingExtensions)) {
    echo "<div class='success'>✅ All core extensions are loaded.</div>";
} else {
    echo "<div class='error'>❌ Missing extensions: " . implode(', ', $missingExtensions) . ". Enable them in Hostinger hPanel > Advanced > PHP Extensions.</div>";
}
echo "</div>";

// 3. Check Files
echo "<div class='card'>";
echo "<h2>File Structure</h2>";

// Guessing root path (assuming this script is in public_html)
// If index.php is here, looking for ../vendor or ./vendor
$potentialRoots = [
    __DIR__,
    dirname(__DIR__)
];

$rootPath = null;
foreach ($potentialRoots as $path) {
    if (file_exists($path . '/artisan')) {
        $rootPath = $path;
        break;
    }
}

if ($rootPath) {
    echo "<div class='success'>✅ Found Laravel root at: " . htmlspecialchars($rootPath) . "</div>";

    // Check .env
    if (file_exists($rootPath . '/.env')) {
        echo "<div class='success'>✅ .env file exists.</div>";
    } else {
        echo "<div class='error'>❌ .env file is MISSING. Please upload it to " . htmlspecialchars($rootPath) . "</div>";
    }

    // Check vendor
    if (is_dir($rootPath . '/vendor')) {
        echo "<div class='success'>✅ vendor directory exists.</div>";
    } else {
        echo "<div class='error'>❌ vendor directory is MISSING. Run 'composer install' or upload the vendor folder.</div>";
    }

} else {
    echo "<div class='error'>❌ Could not find 'artisan' file. Ensure this script is in your public_html folder and you have uploaded your Laravel files.</div>";
}
echo "</div>";

// 4. Permissions Fixer
echo "<div class='card'>";
echo "<h2>File Permissions</h2>";

if ($rootPath) {
    $hasPermissionErrors = false;
    echo "<ul>";
    foreach ($writableDirectories as $dir) {
        $fullPath = $rootPath . '/' . $dir;

        // Create directory if missing (try to)
        if (!is_dir($fullPath)) {
            @mkdir($fullPath, 0755, true);
        }

        if (is_writable($fullPath)) {
            echo "<li class='success'>✅ " . $dir . " is writable.</li>";
        } else {
            // Attempt to fix
            if (@chmod($fullPath, 0775)) {
                echo "<li class='success'>✅ " . $dir . " was fixed (chmod 775).</li>";
            } else {
                $hasPermissionErrors = true;
                echo "<li class='error'>❌ " . $dir . " is NOT writable and auto-fix failed.</li>";
            }
        }
    }
    echo "</ul>";

    if ($hasPermissionErrors) {
        echo "<p class='warning'>⚠️ Automatic fixes failed for some folders. Go to Hostinger File Manager, right-click 'storage' and 'bootstrap/cache', checking 'Write' for Group/Others or set Permissions to 775.</p>";
    }
} else {
    echo "<p>Cannot check permissions without finding Laravel root.</p>";
}
echo "</div>";

echo "<div class='card'>";
echo "<h2>Next Steps</h2>";
echo "<p>If everything above is green try reloading your site.</p>";
echo "<p><strong>Security Warning:</strong> Delete this file (deploy_checker.php) from your server once you are done!</p>";
echo "</div>";

echo "</body></html>";
