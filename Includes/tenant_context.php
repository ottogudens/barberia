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
        // 2. Extract from URL PATH (e.g., barberia.skale.cl/slug)
        $uri = $_SERVER['REQUEST_URI'];
        $path = parse_url($uri, PHP_URL_PATH);

        // Skip static files (CSS, JS, images, etc)
        $staticExtensions = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.eot'];
        foreach ($staticExtensions as $ext) {
            if (str_ends_with($path, $ext)) {
                return null; // Not a tenant request, it's a static file
            }
        }

        $segments = array_filter(explode('/', $path));

        // If first segment exists and is not a system path, use it as slug
        if (!empty($segments)) {
            $firstSegment = reset($segments);

            // Exclude system paths
            $systemPaths = ['super-admin', 'admin', 'register_tenant.php', 'landing.php', 'Design', 'Includes', 'admin_login.php'];

            if (!in_array($firstSegment, $systemPaths) && !str_ends_with($firstSegment, '.php')) {
                $slug = $firstSegment;
            }
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