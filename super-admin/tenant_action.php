<?php
session_start();
if (!isset($_SESSION['super_admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../connect.php';

// Check if tenant_id and action are set
if (isset($_GET['tenant_id']) && isset($_GET['action'])) {
    $tenant_id = $_GET['tenant_id'];
    $action = $_GET['action'];

    if ($action == 'suspend') {
        $stmt = $con->prepare("UPDATE tenants SET status = 'suspended' WHERE tenant_id = ?");
        $stmt->execute([$tenant_id]);
        $_SESSION['msg'] = "Tenant suspended successfully.";
        $_SESSION['msg_type'] = "warning";
    } elseif ($action == 'activate') {
        $stmt = $con->prepare("UPDATE tenants SET status = 'active' WHERE tenant_id = ?");
        $stmt->execute([$tenant_id]);
        $_SESSION['msg'] = "Tenant activated successfully.";
        $_SESSION['msg_type'] = "success";
    } elseif ($action == 'delete') {
        // Perform cascading deletes manually if foreign keys cascade isn't reliable,
        // but PostgreSQL ON DELETE CASCADE should handle it.
        // Let's rely on FK constraints for now.
        try {
            $stmt = $con->prepare("DELETE FROM tenants WHERE tenant_id = ?");
            $stmt->execute([$tenant_id]);
            $_SESSION['msg'] = "Tenant deleted successfully.";
            $_SESSION['msg_type'] = "danger";
        } catch (PDOException $e) {
            $_SESSION['msg'] = "Error deleting tenant: " . $e->getMessage();
            $_SESSION['msg_type'] = "danger";
        }
    }
}

header('Location: dashboard.php');
exit();
?>