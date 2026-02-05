<?php
$url = "postgresql://postgres:RUYrOzEYFVONlBddCVWRoDckoAZRhTkA@postgres.railway.internal:5432/railway";
$parsedUrl = parse_url($url);

$host = $parsedUrl['host'];
$port = $parsedUrl['port'] ?? 5432;
$user = $parsedUrl['user'];
$pass = $parsedUrl['pass'];
$dbname = ltrim($parsedUrl['path'], '/');

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Connected successfully to $host\n";

    $files = [
        'database_pg.sql',
        'saas_migration_pg.sql',
        'financial_migration_pg.sql',
        'offers_migration_pg.sql',
        'gallery_pg.sql',
        'settings_pg.sql'
    ];

    foreach ($files as $file) {
        echo "Executing $file... ";
        $sql = file_get_contents(__DIR__ . '/' . $file);
        $pdo->exec($sql);
        echo "Done.\n";
    }

    echo "Database initialized successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), 'getaddrinfo failed') !== false || strpos($e->getMessage(), 'Could not resolve host') !== false) {
        echo "TIP: It seems you provided the 'Internal' URL. Please provide the 'Public' Connection String if this is being run from outside Railway.\n";
    }
}
