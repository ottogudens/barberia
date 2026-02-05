<?php
// Tenant Context Helper
// In the future, this will resolve tenant based on domain or URL parameter

function getCurrentTenantId($con)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $slug = null;

    // 1. Check for manual override (useful for testing)
    if (isset($_GET['tenant_slug'])) {
        $slug = $_GET['tenant_slug'];
    } else {
        // 2. Extract from Host
        $host = $_SERVER['HTTP_HOST'];

        // Remove port if present
        $host = explode(':', $host)[0];

        $parts = explode('.', $host);

        if (count($parts) > 2 || (count($parts) == 2 && $parts[1] == 'localhost')) {
            $slug = $parts[0];
        } elseif ($host === 'localhost') {
            // Fallback for localhost
            // We can check session before falling back to 1
            if (isset($_SESSION['tenant_id'])) {
                return $_SESSION['tenant_id'];
            }
            // Else use default
            return 1;
        }
    }

    if ($slug) {
        $stmt = $con->prepare("SELECT tenant_id FROM tenants WHERE slug = ?");
        $stmt->execute([$slug]);
        $tenant = $stmt->fetch();

        if ($tenant) {
            $_SESSION['tenant_id'] = $tenant['tenant_id'];
            return $tenant['tenant_id'];
        }
    } else {
        // No slug found, check session
        if (isset($_SESSION['tenant_id'])) {
            return $_SESSION['tenant_id'];
        }
    }

    return null;
}
?>