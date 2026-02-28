<?php
include 'connect.php';

echo "<h1>Actualizando Esquema para Registro Detallado</h1>";

try {
    // 1. Add columns to tenants
    echo "Actualizando tabla <b>tenants</b>... ";
    $con->exec("ALTER TABLE tenants ADD COLUMN IF NOT EXISTS city VARCHAR(100)");
    $con->exec("ALTER TABLE tenants ADD COLUMN IF NOT EXISTS address TEXT");
    echo "<span style='color:green;'>EXITO</span><br>";

    // 2. Add columns to barber_admin
    echo "Actualizando tabla <b>barber_admin</b>... ";
    $con->exec("ALTER TABLE barber_admin ADD COLUMN IF NOT EXISTS phone_number VARCHAR(20)");
    echo "<span style='color:green;'>EXITO</span><br>";

    echo "<hr><p>Migración de esquema completada. Ahora puedes usar el nuevo flujo de registro.</p>";
} catch (Exception $e) {
    echo "<span style='color:red;'>ERROR: " . $e->getMessage() . "</span><br>";
}
?>