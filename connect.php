<?php

require_once __DIR__ . '/Includes/env_loader.php';
loadEnv(__DIR__ . '/.env');

$dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$option = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
);
try {
    $con = new PDO($dsn, $user, $pass, $option);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo 'Good Very Good !';
} catch (PDOException $ex) {
    echo "Failed to connect with database ! " . $ex->getMessage();
    die();
}