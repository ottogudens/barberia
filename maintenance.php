<?php
include "connect.php";

// Security Check
$secret_key = "skale_maintenance_2026";
if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) {
    die("Unauthorized access.");
}

try {
    $con->beginTransaction();

    echo "Cleaning up tenants and related data...\n";
    // CASCADE will handle appointments, clients, services, etc.
    $con->exec("TRUNCATE tenants RESTART IDENTITY CASCADE");

    echo "Resetting super admins...\n";
    $con->exec("TRUNCATE super_admins RESTART IDENTITY CASCADE");

    echo "Creating default super admin...\n";
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $email = 'admin@skale.cl';

    $stmt = $con->prepare("INSERT INTO super_admins (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $email]);

    $con->commit();
    echo "Maintenance completed successfully. Tenants cleared and Super Admin 'admin' created.";

} catch (Exception $e) {
    $con->rollBack();
    echo "Error during maintenance: " . $e->getMessage();
}
?>