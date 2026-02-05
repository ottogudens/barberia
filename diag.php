<?php
echo "<h1>Diagnóstico de Conexión Railway</h1>";

echo "<h2>Variables Detectadas:</h2>";
$vars = ['DB_HOST', 'DB_PORT', 'DB_USER', 'DB_PASS', 'DB_NAME', 'PGHOST', 'PGUSER', 'PGPASSWORD', 'PGPORT', 'PGDATABASE', 'DATABASE_URL'];

echo "<table border='1'>";
echo "<tr><th>Variable</th><th>Valor (primeros 3 caracteres)</th><th>Estado</th></tr>";
foreach ($vars as $v) {
    $val = getenv($v);
    $display = $val ? substr($val, 0, 3) . "..." : "VACÍO";
    $status = $val ? "✅" : "❌";
    echo "<tr><td>$v</td><td>$display</td><td>$status</td></tr>";
}
echo "</table>";

echo "<h2>Prueba de Conexión:</h2>";
$host = getenv('DB_HOST') ?: getenv('PGHOST');
$port = getenv('DB_PORT') ?: getenv('PGPORT') ?: '5432';
$user = getenv('DB_USER') ?: getenv('PGUSER');
$pass = getenv('DB_PASS') ?: getenv('PGPASSWORD');
$dbname = getenv('DB_NAME') ?: getenv('PGDATABASE');

echo "Intentando conectar a: <b>$host:$port</b> con base de datos <b>$dbname</b> e usuario <b>$user</b>...<br>";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $con = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::EXCEPTION]);
    echo "<h3 style='color:green'>✅ Conexión Exitosa!</h3>";

    // Probar si las tablas existen
    $stmt = $con->query("SELECT count(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'tenants'");
    $exists = $stmt->fetchColumn() > 0;
    echo "Existencia de tabla 'tenants': " . ($exists ? "✅ SÍ" : "❌ NO") . "<br>";

    if (!$exists) {
        echo "Intentando inicialización manual desde este script... ";
        $initFile = __DIR__ . '/all_pg_init.sql';
        if (file_exists($initFile)) {
            $sql = file_get_contents($initFile);
            $con->exec($sql);
            echo "<b style='color:blue'>Éxito en creación!</b> Al recargar la web ya deberías ver las tablas.";
        } else {
            echo "<b style='color:red'>Archivo all_pg_init.sql no encontrado!</b>";
        }
    }

} catch (PDOException $e) {
    echo "<h3 style='color:red'>❌ Error de Conexión:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";

    if (strpos($e->getMessage(), 'could not find driver') !== false) {
        echo "<b>TIP:</b> La extensión pdo_pgsql no parece estar instalada. Verifica NIXPACKS_PHP_EXTENSION_INSTALL.";
    }
}
