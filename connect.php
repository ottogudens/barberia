<?php

require_once __DIR__ . '/Includes/env_loader.php';
loadEnv(__DIR__ . '/.env');

// Fallback to Railway default variable names
$host = getenv('DB_HOST') ?: getenv('PGHOST');
$port = getenv('DB_PORT') ?: getenv('PGPORT') ?: '5432';
$user = getenv('DB_USER') ?: getenv('PGUSER');
$pass = getenv('DB_PASS') ?: getenv('PGPASSWORD');
$dbname = getenv('DB_NAME') ?: getenv('PGDATABASE');

if (!$host || !$user || !$dbname) {
    die("Error: Database configuration missing. Please check your Railway environment variables (DB_HOST/PGHOST, etc.).");
}

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
$option = array(
    PDO::ATTR_ERRMODE => PDO::EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);

try {
    $con = new PDO($dsn, $user, $pass, $option);

    // Automatic Background Initialization
    // Check if 'tenants' table exists in the public schema
    $checkSql = "SELECT count(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'tenants'";
    $stmt = $con->query($checkSql);
    $tableExists = $stmt->fetchColumn() > 0;

    if (!$tableExists) {
        $initFile = __DIR__ . '/all_pg_init.sql';
        if (file_exists($initFile)) {
            $sql = file_get_contents($initFile);
            $con->exec($sql);
            // Optionally log success to a file or similar
        }
    }
} catch (PDOException $ex) {
    echo "Failed to connect with database! " . $ex->getMessage();
    die();
}