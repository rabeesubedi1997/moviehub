<?php
echo "<h1>MovieHub Environment Check</h1>";

// Check PHP version
echo "PHP Version: " . phpversion() . "<br>";

// Check extensions
$required_extensions = ['pdo', 'pdo_mysql', 'gd'];
echo "<h2>Required Extensions:</h2>";
foreach ($required_extensions as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? '✅' : '❌') . "<br>";
}

// Check directories
$directories = [
    'public/assets',
    'public/assets/images',
    'public/assets/images/news',
    'public/assets/images/movies'
];

echo "<h2>Directory Permissions:</h2>";
foreach ($directories as $dir) {
    $fullPath = __DIR__ . '/../' . $dir;
    echo "$dir: " . (is_writable($fullPath) ? '✅' : '❌') . "<br>";
}

// Check database connection
try {
    require_once __DIR__ . '/../config/database.php';
    echo "<h2>Database Connection: ✅</h2>";
} catch (Exception $e) {
    echo "<h2>Database Connection: ❌</h2>";
    echo "Error: " . $e->getMessage();
}
