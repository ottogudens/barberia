<?php
session_start();
include "connect.php";
include "Includes/functions/functions.php";
include "Includes/csrf.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $_SESSION['register_error'] = "Error de validación de seguridad (CSRF). Intente nuevamente.";
        header('Location: register_tenant.php');
        exit();
    }

    $shop_name = test_input($_POST['shop_name']);
    $slug = strtolower(test_input($_POST['slug']));
    $admin_username = test_input($_POST['admin_username']);
    $owner_email = test_input($_POST['owner_email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Validation
    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = "Las contraseñas no coinciden.";
        header('Location: register_tenant.php');
        exit();
    }

    // Check Slug Availability
    $stmt = $con->prepare("SELECT tenant_id FROM tenants WHERE slug = ?");
    $stmt->execute([$slug]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['register_error'] = "La URL '$slug' ya está en uso. Por favor elija otra.";
        header('Location: register_tenant.php');
        exit();
    }

    try {
        $con->beginTransaction();

        // 2. Create Tenant
        $stmt = $con->prepare("INSERT INTO tenants (name, slug, owner_email, status, created_at) VALUES (?, ?, ?, 'active', NOW())");
        $stmt->execute([$shop_name, $slug, $owner_email]);
        $tenant_id = $con->lastInsertId();

        // 3. Create Admin User
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        // Assuming full_name is required in barber_admin? Schema said YES.
        $full_name = "Administrador";

        $stmt = $con->prepare("INSERT INTO barber_admin (username, password, email, full_name, tenant_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$admin_username, $hashed_password, $owner_email, $full_name, $tenant_id]);

        // 4. Create Default Data (Optional but recommended)

        // Default Service Category
        $stmt = $con->prepare("INSERT INTO service_categories (category_name, tenant_id) VALUES ('Cortes Generales', ?)");
        $stmt->execute([$tenant_id]);
        $category_id = $con->lastInsertId();

        // Default Service
        $stmt = $con->prepare("INSERT INTO services (service_name, service_description, service_price, service_duration, category_id, tenant_id) VALUES ('Corte Clásico', 'Corte de cabello estándar', 15.00, 30, ?, ?)");
        $stmt->execute([$category_id, $tenant_id]);

        $con->commit();

        // Redirect to Success or directly to Login with slug param
        // For localhost testing:
        $login_url = "admin/login.php?tenant_slug=$slug";

        // Show success message
        echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Registro Exitoso</title><link rel='stylesheet' href='Design/css/bootstrap.min.css'></head><body>";
        echo "<div class='container mt-5'><div class='jumbotron text-center'>";
        echo "<h1 class='display-4'>¡Felicidades!</h1>";
        echo "<p class='lead'>Tu barbería <strong>$shop_name</strong> ha sido creada exitosamente.</p>";
        echo "<hr class='my-4'>";
        echo "<p>Puedes acceder a tu panel administrativo ahora.</p>";
        echo "<a class='btn btn-primary btn-lg' href='$login_url' role='button'>Ir al Panel Admin</a>";
        echo "</div></div></body></html>";
        exit();

    } catch (Exception $e) {
        $con->rollBack();
        $_SESSION['register_error'] = "Error del sistema: " . $e->getMessage();
        header('Location: register_tenant.php');
        exit();
    }

} else {
    header('Location: register_tenant.php');
    exit();
}
?>