<?php

require_once __DIR__ . '/Includes/env_loader.php';
loadEnv(__DIR__ . '/.env');

// Fallback to Railway default variable names if our custom ones aren't set
$host = getenv('DB_HOST') ?: getenv('PGHOST');
$port = getenv('DB_PORT') ?: getenv('PGPORT') ?: '5432';
$user = getenv('DB_USER') ?: getenv('PGUSER');
$pass = getenv('DB_PASS') ?: getenv('PGPASSWORD');
$dbname = getenv('DB_NAME') ?: getenv('PGDATABASE');

if (!$host || !$user || !$dbname) {
    // If we are on Railway but variables aren't set, this will help debugging
    die("Error: Database configuration missing. Please check your Railway environment variables (DB_HOST/PGHOST, etc.).");
}

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
$option = array(
    PDO::ATTR_ERRMODE => PDO::EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);

try {
    $con = new PDO($dsn, $user, $pass, $option);

    // Auto-initialization check: if 'tenants' table doesn't exist, redirect to the initializer
    $stmt = $con->query("SELECT 1 FROM information_schema.tables WHERE table_name = 'tenants' LIMIT 1");
    if ($stmt->fetch() === false) {
        // Only redirect if we are NOT already on the initializer
        if (basename($_SERVER['PHP_SELF']) !== 'init_railway_db.php') {
            header("Location: init_railway_db.php");
            exit();
        }
    }
} catch (PDOException $ex) {
    echo "Failed to connect with database! " . $ex->getMessage();
    die();
}