<?php
// init_railway_db.php
// This script initializes the PostgreSQL database on Railway

$host = getenv('PGHOST');
$port = getenv('PGPORT');
$dbname = getenv('PGDATABASE');
$user = getenv('PGUSER');
$password = getenv('PGPASSWORD');

if (!$host) {
    echo "PGHOST not set, skipping DB init.\n";
    exit(0);
}

// Check if all_pg_init.sql exists
$sqlFile = '/app/all_pg_init.sql';
if (!file_exists($sqlFile)) {
    echo "SQL Init file not found at $sqlFile. Checking local directory...\n";
    if (file_exists('all_pg_init.sql')) {
        $sqlFile = 'all_pg_init.sql';
    } else {
        echo "Error: all_pg_init.sql not found.\n";
        exit(1);
    }
}

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    echo "Connecting to database at $host...\n";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "Reading SQL file: $sqlFile\n";
    $sql = file_get_contents($sqlFile);

    echo "Executing SQL commands...\n";
    $pdo->exec($sql);

    echo "Database initialized successfully.\n";

} catch (PDOException $e) {
    echo "DB Init Error: " . $e->getMessage() . "\n";
    // We do NOT exit with failure here to allow the container to start 
    // even if there are transient DB connection issues.
    // However, if the query fails (e.g. syntax error), it will also be caught here.
}
?>