<?php

require_once __DIR__ . '/Includes/env_loader.php';
loadEnv(__DIR__ . '/.env');

$dsn = 'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$option = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);
try {
    $con = new PDO($dsn, $user, $pass, $option);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo 'Good Very Good !';
} catch (PDOException $ex) {
    echo "Failed to connect with database ! " . $ex->getMessage();
    die();
}