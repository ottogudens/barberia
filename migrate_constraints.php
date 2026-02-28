<?php
include "connect.php";

try {
    echo "Starting migration: update barber_admin constraints...\n";

    // 1. Drop existing unique constraint on username
    // We need to find the constraint name. In PG it's usually barber_admin_username_key
    $con->exec("ALTER TABLE barber_admin DROP CONSTRAINT IF EXISTS barber_admin_username_key");

    // 2. Add new composite unique constraint
    $con->exec("ALTER TABLE barber_admin ADD CONSTRAINT unique_username_tenant UNIQUE (username, tenant_id)");

    echo "Migration successful: username is now unique per tenant.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>