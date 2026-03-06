<?php
session_start();
if (!isset($_SESSION['super_admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../connect.php';
include '../Includes/functions/functions.php';

$pageTitle = 'Nueva Barbería';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_tenant'])) {
    $name = test_input($_POST['name']);
    $email = test_input($_POST['owner_email']);
    $slug = test_input($_POST['slug']);
    $city = test_input($_POST['city']);
    $address = test_input($_POST['address']);

    $admin_full_name = test_input($_POST['admin_full_name']);
    $admin_phone = test_input($_POST['admin_phone']);
    $admin_user = test_input($_POST['admin_username']);
    $admin_pass = password_hash(test_input($_POST['admin_password']), PASSWORD_DEFAULT);

    try {
        $con->beginTransaction();

        // 1. Create Tenant
        $stmt = $con->prepare("INSERT INTO tenants (name, slug, owner_email, city, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $email, $city, $address]);
        $tenant_id = $con->lastInsertId();

        // 2. Create Initial Admin for this Tenant
        $stmt_admin = $con->prepare("INSERT INTO barber_admin (username, password, email, full_name, phone_number, tenant_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_admin->execute([$admin_user, $admin_pass, $email, $admin_full_name, $admin_phone, $tenant_id]);

        $con->commit();
        $success = "Barbería y administrador creados con éxito.";
    } catch (PDOException $e) {
        $con->rollBack();
        $error = "Error al crear: " . $e->getMessage();
    }
}

include 'Includes/templates/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 animate-fade-in">
        <h1 class="h3 mb-0 text-white-50">Crear <span class="text-gold">Nueva Barbería</span></h1>
        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Volver al Dashboard
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10 animate-fade-in" style="animation-delay: 0.1s;">
            <div class="card glass-card glass-card shadow mb-4 border-0">
                <div class="card-header py-3 bg-transparent border-secondary">
                    <h6 class="m-0 font-weight-bold text-gold text-uppercase">Configuración Inicial del SaaS</h6>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 bg-danger text-white small mb-4">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success border-0 bg-success text-white small mb-4">
                            <i class="fas fa-check-circle mr-2"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                                    <?php if(function_exists("csrfInput")) csrfInput(); ?>
                        <h5 class="text-white mb-4 border-left-gold pl-3">Datos de la Barbería</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-gold small font-weight-bold text-uppercase">Ciudad</label>
                                    <input type="text" class="form-control bg-dark border-secondary text-white"
                                        name="city" placeholder="Ej: Santiago"
                                        style="background: rgba(255,255,255,0.05) !important;" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-gold small font-weight-bold text-uppercase">Dirección</label>
                                    <input type="text" class="form-control bg-dark border-secondary text-white"
                                        name="address" placeholder="Ej: Av. Providencia 1234"
                                        style="background: rgba(255,255,255,0.05) !important;" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-gold small font-weight-bold text-uppercase">Nombre del
                                        Negocio</label>
                                    <input type="text" class="form-control bg-dark border-secondary text-white"
                                        name="name" placeholder="Ej: Barbería Real"
                                        style="background: rgba(255,255,255,0.05) !important;" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-gold small font-weight-bold text-uppercase">Slug (URL /
                                        Dominio)</label>
                                    <input type="text" class="form-control bg-dark border-secondary text-white"
                                        name="slug" placeholder="ej: barberia-real"
                                        style="background: rgba(255,255,255,0.05) !important;" required>
                                    <small class="text-white-50">Será accesible en <code>/nombre-slug</code></small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-gold small font-weight-bold text-uppercase">Email del Propietario</label>
                            <input type="email" class="form-control bg-dark border-secondary text-white"
                                name="owner_email" placeholder="propietario@email.com"
                                style="background: rgba(255,255,255,0.05) !important;" required>
                        </div>

                        <h5 class="text-white mt-5 mb-4 border-left-gold pl-3">Credenciales de Administración del Tenant
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-gold small font-weight-bold text-uppercase">Nombre y Apellido del
                                        Admin</label>
                                    <input type="text" class="form-control bg-dark border-secondary text-white"
                                        name="admin_full_name" placeholder="Ej: Juan Pérez"
                                        style="background: rgba(255,255,255,0.05) !important;" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-gold small font-weight-bold text-uppercase">Teléfono</label>
                                    <input type="text" class="form-control bg-dark border-secondary text-white"
                                        name="admin_phone" placeholder="Ej: +56 9 1234 5678"
                                        style="background: rgba(255,255,255,0.05) !important;" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-gold small font-weight-bold text-uppercase">Usuario
                                        Administrador</label>
                                    <input type="text" class="form-control bg-dark border-secondary text-white"
                                        name="admin_username" placeholder="admin"
                                        style="background: rgba(255,255,255,0.05) !important;" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-gold small font-weight-bold text-uppercase">Contraseña
                                        Inicial</label>
                                    <input type="password" class="form-control bg-dark border-secondary text-white"
                                        name="admin_password" placeholder="••••••••"
                                        style="background: rgba(255,255,255,0.05) !important;" required>
                                </div>
                            </div>
                        </div>

                        <hr class="border-secondary mt-5">

                        <div class="d-flex justify-content-end">
                            <button type="submit" name="create_tenant"
                                class="btn btn-gold-premium px-5 font-weight-bold text-uppercase">
                                <i class="fas fa-rocket mr-2"></i> Lanzar Nueva Barbería
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'Includes/templates/footer.php'; ?>