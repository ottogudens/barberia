<?php
// super-admin/clear_cache.php
session_start();
if (!isset($_SESSION['super_admin_id'])) {
    die("Acceso denegado.");
}

echo "<h1>Limpieza Profunda de Caché - SKBarber</h1>";

// 1. Reset PHP OpCache
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<p style='color:green;'>✅ PHP OpCache ha sido reiniciado con éxito.</p>";
    } else {
        echo "<p style='color:red;'>❌ Error al reiniciar PHP OpCache.</p>";
    }
} else {
    echo "<p style='color:orange;'>⚠️ OpCache no está habilitado o no es accesible.</p>";
}

// 2. Clear Temporary Files
$tmpDir = sys_get_temp_dir();
echo "<p>Limpiando directorio temporal: <code>$tmpDir</code></p>";

$files = glob($tmpDir . '/*');
foreach ($files as $file) {
    if (is_file($file)) {
        @unlink($file);
    }
}
echo "<p style='color:green;'>✅ Archivos temporales eliminados.</p>";

// 3. Clear PHP Sessions (Optional/Caution)
echo "<p style='color:blue;'>ℹ️ Las sesiones activas se mantienen por seguridad.</p>";

echo "<hr><a href='dashboard.php'>Volver al Dashboard</a>";
?>