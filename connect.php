<?php

// Prevent .env from overwriting Railway variables
require_once __DIR__ . '/Includes/env_loader.php';
// Only load .env if we are not on Railway or if specific vars are missing
if (!getenv('RAILWAY_STATIC_URL') && !getenv('PGHOST')) {
    loadEnv(__DIR__ . '/.env');
}

// Robust connection logic with verbose error reporting for Debugging
$host = getenv('DB_HOST') ?: getenv('PGHOST');
$port = getenv('DB_PORT') ?: getenv('PGPORT') ?: '5432';
$user = getenv('DB_USER') ?: getenv('PGUSER');
$pass = getenv('DB_PASS') ?: getenv('PGPASSWORD');
$dbname = getenv('DB_NAME') ?: getenv('PGDATABASE');

// Diagnostic check (helpful for the user to see in logs/browser if it fails)
if (!$host || !$user || !$dbname) {
    if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
        echo "<!-- Debug info: Host=$host, User=$user, DB=$dbname -->";
    }
}

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
$option = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);

try {
    $con = new PDO($dsn, $user, $pass, $option);

    // Check if tables exist
    $checkSql = "SELECT count(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'tenants'";
    $stmt = $con->query($checkSql);
    $tableExists = $stmt->fetchColumn() > 0;

    if (!$tableExists) {
        $initFile = __DIR__ . '/all_pg_init.sql';
        if (file_exists($initFile)) {
            $sql = file_get_contents($initFile);
            // We use a try-catch specifically for the exec to see if it fails
            try {
                $con->exec($sql);
                // Success - we might want to log this or just let it be
            } catch (Exception $initEx) {
                // If tables fail to create, we need to know why
                error_log("Database Initialization Failed: " . $initEx->getMessage());
                if (isset($_GET['debug_init'])) {
                    echo "Database Initialization Failed: " . $initEx->getMessage();
                }
            }
        } else {
            error_log("Initialization file not found: $initFile");
        }
    }
} catch (PDOException $ex) {
    // Serious connection error
    $msg = "Failed to connect with database! Host: $host. Error: " . $ex->getMessage();
    error_log($msg);
    if (!headers_sent()) {
        echo $msg;
    }
    die();
}