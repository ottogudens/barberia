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
$debug = getenv('APP_DEBUG') === 'true';

require_once __DIR__ . '/Includes/csrf.php';

// Global CSRF Protection for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $script_name = $_SERVER['SCRIPT_NAME'];
    // Exclude AJAX scripts and scripts that handle CSRF manually
    if (strpos($script_name, 'ajax') === false && strpos($script_name, 'appointment.php') === false && strpos($script_name, '/login.php') === false) {
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            die("Error de seguridad (CSRF). Por favor recargue la página o re-inicie sesión.");
        }
    }
}

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
$option = array(
    PDO::ATTR_ERRMODE => $debug ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);

try {
    $con = new PDO($dsn, $user, $pass, $option);

    // DEBUG: Log successful connection
    error_log("DB Connected: Host=$host, DB=$dbname");

    // Check if tables exist
    $checkSql = "SELECT count(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'tenants'";
    $stmt = $con->query($checkSql);
    $tableExists = $stmt->fetchColumn() > 0;

    if (!$tableExists) {
        $initFile = __DIR__ . '/all_pg_init.sql';
        if (file_exists($initFile)) {
            $sql = file_get_contents($initFile);
            try {
                $con->exec($sql);
            } catch (Exception $initEx) {
                error_log("Database Initialization Failed: " . $initEx->getMessage());
                if ($debug) {
                    echo "Database Initialization Failed: " . $initEx->getMessage();
                }
            }
        } else {
            error_log("Initialization file not found: $initFile");
        }
    }
} catch (PDOException $ex) {
    // Serious connection error
    $msg = "Database connection error.";
    if ($debug) {
        $msg .= " Host: $host. Error: " . $ex->getMessage();
    }
    error_log($msg);
    if (!headers_sent()) {
        echo $msg;
    }
    die();
}
