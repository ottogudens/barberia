<?php
include 'connect.php';

echo "<h1>Actualizando Restricciones de Cascada (SaaS)</h1>";

$migrations = [
    ['table' => 'barber_admin', 'fk' => 'fk_admin_tenant'],
    ['table' => 'service_categories', 'fk' => 'fk_categories_tenant'],
    ['table' => 'services', 'fk' => 'fk_services_tenant'],
    ['table' => 'employees', 'fk' => 'fk_employees_tenant'],
    ['table' => 'clients', 'fk' => 'fk_clients_tenant'],
    ['table' => 'appointments', 'fk' => 'fk_appointments_tenant'],
    ['table' => 'employee_payouts', 'fk' => 'employee_payouts_tenant_id_fkey'],
    ['table' => 'offers', 'fk' => 'offers_tenant_id_fkey'],
    ['table' => 'gallery_images', 'fk' => 'gallery_images_tenant_id_fkey'],
    ['table' => 'website_settings', 'fk' => 'website_settings_tenant_id_fkey']
];

foreach ($migrations as $m) {
    $table = $m['table'];
    $fk = $m['fk'];

    echo "Procesando tabla: <b>$table</b>... ";

    try {
        // Drop existing constraint if it exists
        $con->exec("ALTER TABLE $table DROP CONSTRAINT IF EXISTS $fk");

        // Re-add with ON DELETE CASCADE
        $con->exec("ALTER TABLE $table ADD CONSTRAINT $fk FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE");

        echo "<span style='color:green;'>EXITO</span><br>";
    } catch (Exception $e) {
        echo "<span style='color:red;'>ERROR: " . $e->getMessage() . "</span><br>";
    }
}

echo "<hr><p>Migración completada. Ahora las eliminaciones de Tenants funcionarán en cascada.</p>";
?>