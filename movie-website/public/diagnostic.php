<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function checkDirectory($path)
{
    echo "<h3>Checking: $path</h3>";
    echo "Exists: " . (file_exists($path) ? 'Yes' : 'No') . "<br>";
    echo "Is Directory: " . (is_dir($path) ? 'Yes' : 'No') . "<br>";
    echo "Is Writable: " . (is_writable($path) ? 'Yes' : 'No') . "<br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms($path)), -4) . "<br>";
    echo "Owner: " . fileowner($path) . "<br>";
    echo "Group: " . filegroup($path) . "<br>";
}

$paths = [
    __DIR__,
    __DIR__ . '/assets',
    __DIR__ . '/assets/images',
    __DIR__ . '/assets/images/news',
];

echo "<h2>PHP Configuration</h2>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";

echo "<h2>Directory Permissions</h2>";
foreach ($paths as $path) {
    checkDirectory($path);
}

echo "<h2>Recent Error Log</h2>";
$log = file_exists('C:/xampp/php/logs/php_error.log')
    ? tail('C:/xampp/php/logs/php_error.log', 20)
    : 'No error log found';
echo "<pre>$log</pre>";

function tail($filename, $lines = 10)
{
    $file = file($filename);
    return implode("", array_slice($file, -$lines));
}
