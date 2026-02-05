<?php
echo "<h1>Barberia Deployment Status</h1>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Extensions Loaded:<br><ul>";
$exts = ['pdo_pgsql', 'pgsql', 'pdo_mysql', 'mysqli'];
foreach ($exts as $ext) {
    $status = extension_loaded($ext) ? "✅ LOADED" : "❌ MISSING";
    echo "<li>$ext: $status</li>";
}
echo "</ul>";

echo "<h2>Environment Check:</h2>";
echo "PGHOST: " . (getenv('PGHOST') ? "SET ✅" : "NOT SET ❌") . "<br>";
echo "DB_HOST: " . (getenv('DB_HOST') ? "SET ✅" : "NOT SET ❌") . "<br>";
echo "PORT: " . (getenv('PORT') ?: "Default (8080?)") . "<br>";

echo "<h2>File Existence:</h2>";
echo "all_pg_init.sql: " . (file_exists(__DIR__ . '/all_pg_init.sql') ? "Found ✅" : "NOT FOUND ❌") . "<br>";
echo "index.php: " . (file_exists(__DIR__ . '/index.php') ? "Found ✅" : "NOT FOUND ❌") . "<br>";
?>