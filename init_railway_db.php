<?php
/**
 * Script de inicialización de base de datos para Railway
 * Úsalo solo una vez y bórralo después por seguridad.
 */

require_once __DIR__ . '/Includes/env_loader.php';
loadEnv(__DIR__ . '/.env');

// Intentar obtener credenciales de variables de entorno (ya configuradas en Railway)
$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '5432';
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

if (!$host || !$user || !$pass || !$dbname) {
    die("Error: Variables de entorno de base de datos no configuradas en Railway. Revisa la pestaña 'Variables'.");
}

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "<h1>Conectado a PostgreSQL exitosamente</h1>";

    $files = [
        'database_pg.sql',
        'saas_migration_pg.sql',
        'financial_migration_pg.sql',
        'offers_migration_pg.sql',
        'gallery_pg.sql',
        'settings_pg.sql'
    ];

    echo "<ul>";
    foreach ($files as $file) {
        if (!file_exists(__DIR__ . '/' . $file)) {
            echo "<li style='color:red'>Error: Archivo $file no encontrado.</li>";
            continue;
        }

        echo "<li>Ejecutando $file... ";
        $sql = file_get_contents(__DIR__ . '/' . $file);
        $pdo->exec($sql);
        echo "<span style='color:green'>Completado</span></li>";
    }
    echo "</ul>";

    echo "<h2 style='color:blue'>¡Base de datos inicializada correctamente!</h2>";
    echo "<p style='color:orange'><strong>IMPORTANTE:</strong> Por seguridad, borra este archivo (init_railway_db.php) de tu repositorio ahora.</p>";

} catch (PDOException $e) {
    echo "<h2 style='color:red'>Error de Conexión:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<p>Asegúrate de haber instalado la extensión <code>pdo_pgsql</code> vía NIXPACKS_PHP_EXTENSION_INSTALL.</p>";
}
