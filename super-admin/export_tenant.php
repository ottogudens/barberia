<?php
session_start();
if (!isset($_SESSION['super_admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../connect.php';

if (!isset($_GET['tenant_id'])) {
    header('Location: dashboard.php');
    exit();
}

$tenant_id = $_GET['tenant_id'];

// Fetch tenant info for filename
$stmt = $con->prepare("SELECT name, slug FROM tenants WHERE tenant_id = ?");
$stmt->execute([$tenant_id]);
$tenant = $stmt->fetch();

if (!$tenant) {
    die("Tenant no encontrado.");
}

$filename = "backup_" . $tenant['slug'] . "_" . date('Y-m-d') . ".sql";

header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo "-- Backup para la Barbería: " . $tenant['name'] . "\n";
echo "-- Generado el: " . date('Y-m-d H:i:s') . "\n";
echo "-- Tenant ID: " . $tenant_id . "\n\n";

// List of tables to export (those that have tenant_id)
$tables = [
    'tenants' => 'tenant_id',
    'barber_admin' => 'tenant_id',
    'service_categories' => 'tenant_id',
    'services' => 'tenant_id',
    'employees' => 'tenant_id',
    'clients' => 'tenant_id',
    'appointments' => 'tenant_id',
    'employee_payouts' => 'tenant_id',
    'offers' => 'tenant_id',
    'gallery_images' => 'tenant_id',
    'website_settings' => 'tenant_id'
];

foreach ($tables as $table => $column) {
    echo "-- Estructura y Datos para la tabla `$table` --\n";

    // Fetch data for this tenant
    $stmt = $con->prepare("SELECT * FROM $table WHERE $column = ?");
    $stmt->execute([$tenant_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) > 0) {
        foreach ($rows as $row) {
            $cols = implode(", ", array_keys($row));
            $vals = array_values($row);
            $placeholders = implode(", ", array_fill(0, count($vals), "?"));

            // Format values for SQL
            $escaped_vals = [];
            foreach ($vals as $v) {
                if ($v === null) {
                    $escaped_vals[] = "NULL";
                } elseif (is_numeric($v)) {
                    $escaped_vals[] = $v;
                } else {
                    $escaped_vals[] = "'" . str_replace("'", "''", $v) . "'";
                }
            }
            $final_vals = implode(", ", $escaped_vals);

            echo "INSERT INTO $table ($cols) VALUES ($final_vals);\n";
        }
    }
    echo "\n";
}
?>