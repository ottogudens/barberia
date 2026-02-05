<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/Includes/env_loader.php';
loadEnv(__DIR__ . '/.env');

echo "<h1>Debug DB Connection</h1>";
echo "DB_HOST env: '" . getenv('DB_HOST') . "'<br>";
echo "DB_USER env: '" . getenv('DB_USER') . "'<br>";
echo "DB_NAME env: '" . getenv('DB_NAME') . "'<br>";

$dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME');
echo "DSN: '" . $dsn . "'<br>";

try {
    $con = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    ]);
    echo "<h2>Connection Successful!</h2>";
} catch (PDOException $e) {
    echo "<h2>Connection Failed!</h2>";
    echo "Error: " . $e->getMessage();
}
