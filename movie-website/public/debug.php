<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>PHP Error Log</h2>";
echo "<pre>";
echo file_get_contents("C:/xampp/php/logs/php_error.log");
echo "</pre>";

echo "<h2>Apache Error Log</h2>";
echo "<pre>";
echo file_get_contents("C:/xampp/apache/logs/error.log");
echo "</pre>";
