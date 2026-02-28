<?php
session_start();
if (!isset($_SESSION['super_admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../connect.php';
include '../Includes/functions/functions.php';

if (!isset($_GET['tenant_id'])) {
    header('Location: dashboard.php');
    exit();
}

$tenant_id = $_GET['tenant_id'];
$stmt = $con->prepare("SELECT * FROM tenants WHERE tenant_id = ?");
$stmt->execute([$tenant_id]);
$tenant = $stmt->fetch();

if (!$tenant) {
    header('Location: dashboard.php');
    exit();
}

$pageTitle = 'Editar Tenant';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_tenant'])) {
    $name = test_input($_POST['name']);
    $email = test_input($_POST['email']);
    $slug = test_input($_POST['slug']);
    $status = test_input($_POST['status']);

    // Update tenant
    $updateStmt = $con->prepare("UPDATE tenants SET name = ?, owner_email = ?, slug = ?, status = ? WHERE tenant_id = ?");
    try {
        $updateStmt->execute([$name, $email, $slug, $status, $tenant_id]);
        $success = "Tenant actualizado con éxito.";
        // Refresh data
        $stmt->execute([$tenant_id]);
        $tenant = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Error al actualizar: " . $e->getMessage();
    }
}

include 'Includes/templates/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 animate-fade-in">
        <h1 class="h3 mb-0 text-white-50">Editar Barbería: <span
                class="text-gold"><?php echo htmlspecialchars($tenant['name']); ?></span></h1>
        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Volver al Dashboard
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 animate-fade-in" style="animation-delay: 0.1s;">
            <div class="card glass-card shadow mb-4 border-0">
                <div class="card-header py-3 bg-transparent border-secondary">
                    <h6 class="m-0 font-weight-bold text-gold text-uppercase">Información del Tenant</h6>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 bg-danger text-white small mb-4">
                            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success border-0 bg-success text-white small mb-4">
                            <i class="fas fa-check-circle mr-2"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-gold small font-weight-bold text-uppercase">Nombre de la
                                        Barbería</label>
                                    <input type="text" class="form-control bg-dark border-secondary text-white"
                                        name="name" value="<?php echo htmlspecialchars($tenant['name']); ?>"
                                        style="background: rgba(255,255,255,0.05) !important;" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-gold small font-weight-bold text-uppercase">Slug (URL /
                                        Dominio)</label>
                                    <input type="text" class="form-control bg-dark border-secondary text-white"
                                        name="slug" value="<?php echo htmlspecialchars($tenant['slug']); ?>"
                                        style="background: rgba(255,255,255,0.05) !important;" required>
                                    <small class="text-white-50">Ej:
                                        <code>barberia.skale.cl/<?php echo $tenant['slug']; ?></code></small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-gold small font-weight-bold text-uppercase">Email del Propietario</label>
                            <input type="email" class="form-control bg-dark border-secondary text-white" name="email"
                                value="<?php echo htmlspecialchars($tenant['owner_email']); ?>"
                                style="background: rgba(255,255,255,0.05) !important;" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-gold small font-weight-bold text-uppercase">Estado de la Cuenta</label>
                            <select name="status" class="form-control bg-dark border-secondary text-white"
                                style="background: rgba(255,255,255,0.05) !important;">
                                <option value="active" <?php if ($tenant['status'] == 'active')
                                    echo 'selected'; ?>>ACTIVO
                                </option>
                                <option value="suspended" <?php if ($tenant['status'] == 'suspended')
                                    echo 'selected'; ?>>
                                    SUSPENDIDO</option>
                                <option value="inactive" <?php if ($tenant['status'] == 'inactive')
                                    echo 'selected'; ?>>
                                    INACTIVO</option>
                            </select>
                        </div>

                        <hr class="border-secondary mt-5">

                        <div class="d-flex justify-content-end">
                            <button type="submit" name="save_tenant"
                                class="btn btn-gold-premium px-5 font-weight-bold text-uppercase">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'Includes/templates/footer.php'; ?>