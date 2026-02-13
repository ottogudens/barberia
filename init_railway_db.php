<?php
// init_railway_db.php
// This script initializes the PostgreSQL database on Railway

// Include connect.php to reuse the connection logic
require_once __DIR__ . '/connect.php';

// $con is available from connect.php (PDO object)

if (!isset($con)) {
    echo "Database connection failed (variable \$con not set).\n";
    exit(1);
}

// Check if all_pg_init.sql exists
$sqlFile = __DIR__ . '/all_pg_init.sql';
if (!file_exists($sqlFile)) {
    echo "SQL Init file not found at $sqlFile.\n";
    exit(1);
}

try {
    echo "Reading SQL file: $sqlFile\n";
    $sql = file_get_contents($sqlFile);

    echo "Executing SQL commands...\n";
    // We use exec() because $sql contains multiple statements
    $con->exec($sql);

    echo "Database initialized successfully.\n";

    // VERIFICATION
    $stmt = $con->query("SELECT username, password FROM super_admins WHERE username = 'superadmin'");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo "VERIFICATION SUCCESS: User 'superadmin' found in DB. Hash starts with: " . substr($user['password'], 0, 10) . "...\n";
    } else {
        echo "VERIFICATION FAILED: User 'superadmin' NOT found in DB after Insert!\n";
    }

} catch (PDOException $e) {
    echo "DB Init Error: " . $e->getMessage() . "\n";
    // Do not exit with error to avoid crashing the container on transient errors
}
?>