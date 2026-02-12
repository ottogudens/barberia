<?php
/**
 * Script de inicialización de base de datos para Railway
 * Se ejecuta automáticamente al iniciar el contenedor.
 */

// Intentar cargar variables de entorno locales si existen
if (file_exists(__DIR__ . '/Includes/env_loader.php')) {
    require_once __DIR__ . '/Includes/env_loader.php';
    if (file_exists(__DIR__ . '/.env')) {
        loadEnv(__DIR__ . '/.env');
    }
}

// Obtener credenciales de variables de entorno
// Railway proporciona variables estándar PG* para PostgreSQL
$host = getenv('PGHOST') ?: getenv('DB_HOST');
$port = getenv('PGPORT') ?: getenv('DB_PORT') ?: '5432';
$user = getenv('PGUSER') ?: getenv('DB_USER');
$pass = getenv('PGPASSWORD') ?: getenv('DB_PASS');
$dbname = getenv('PGDATABASE') ?: getenv('DB_NAME');

if (!$host || !$user || !$dbname) {
    error_log("DB INIT SKIPPED: Database environment variables not set.");
    exit(0); // No fallar, solo saltar si no hay configuración
}

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    echo "Connecting to PostgreSQL database...\n";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $initFile = 'all_pg_init.sql';
    $filePath = __DIR__ . '/' . $initFile;

    if (!file_exists($filePath)) {
        echo "Error: SQL file $initFile not found.\n";
        exit(1);
    }

    echo "Executing $initFile...\n";
    $sql = file_get_contents($filePath);

    // Ejecutar el SQL completo
    $pdo->exec($sql);

    echo "Database initialization completed successfully.\n";

} catch (PDOException $e) {
    echo "DB Connection/Init Error: " . $e->getMessage() . "\n";
    // No hacer exit(1) para no detener el despliegue si la base de datos ya está en uso o hay un error transitorio
    // Pero si es crítico, debería fallar. Para inicialización, dejémoslo pasar con error log.
    exit(1);
}
