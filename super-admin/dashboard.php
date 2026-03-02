<?php
session_start();
if (!isset($_SESSION['super_admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../connect.php';
include '../Includes/functions/functions.php';

$pageTitle = 'Dashboard';
include 'Includes/templates/header.php';

// Fetch Summary Stats
$stmt_total = $con->query("SELECT COUNT(*) FROM tenants");
$total_tenants = $stmt_total->fetchColumn();

$stmt_active = $con->query("SELECT COUNT(*) FROM tenants WHERE status = 'active'");
$active_tenants = $stmt_active->fetchColumn();

$stmt_suspended = $con->query("SELECT COUNT(*) FROM tenants WHERE status = 'suspended'");
$suspended_tenants = $stmt_suspended->fetchColumn();

// Fetch Tenants List
$stmt = $con->prepare("SELECT * FROM tenants ORDER BY created_at DESC");
$stmt->execute();
$tenants = $stmt->fetchAll();
?>

<div class="container-fluid">
    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show animate-fade-in"
            role="alert">
            <?php echo $_SESSION['msg']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['msg']);
        unset($_SESSION['msg_type']); ?>
    <?php endif; ?>

    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.03) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 175, 55, 0.1) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            border-color: rgba(212, 175, 55, 0.3) !important;
        }

        .table-dark {
            background: transparent !important;
        }

        .table thead th {
            border-bottom: 2px solid rgba(212, 175, 55, 0.3) !important;
            background: rgba(212, 175, 55, 0.05);
        }

        .badge-success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }

        .badge-danger {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
    </style>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Total Tenants -->
        <div class="col-xl-4 col-md-6 mb-4 animate-fade-in">
            <div class="card glass-card border-left-primary shadow h-100 py-2 border-0">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Tenants
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white"><?php echo $total_tenants; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-city fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Tenants -->
        <div class="col-xl-4 col-md-6 mb-4 animate-fade-in" style="animation-delay: 0.1s;">
            <div class="card glass-card border-left-success shadow h-100 py-2 border-0">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tenants Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-white"><?php echo $active_tenants; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suspended Tenants -->
        <div class="col-xl-4 col-md-6 mb-4 animate-fade-in" style="animation-delay: 0.2s;">
            <div class="card glass-card border-left-danger shadow h-100 py-2 border-0">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tenants Suspendidos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white"><?php echo $suspended_tenants; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenants Table -->
    <div class="card glass-card glass-card shadow mb-4 border-0 animate-fade-in" style="animation-delay: 0.3s;">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-transparent border-secondary">
            <h6 class="m-0 font-weight-bold text-gold text-uppercase">Listado Maestro de Barberías</h6>
            <a href="create_tenant.php" class="btn btn-gold-premium btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-plus fa-sm mr-1"></i> NUEVA BARBERÍA
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0" width="100%" cellspacing="0">
                    <thead class="text-gold">
                        <tr>
                            <th>Tenant ID</th>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Email Propietario</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-white-50">
                        <?php foreach ($tenants as $tenant): ?>
                            <tr>
                                <td class="font-weight-bold text-white"><?php echo $tenant['tenant_id']; ?></td>
                                <td><?php echo htmlspecialchars($tenant['name']); ?></td>
                                <td>
                                    <span class="badge badge-dark p-2 border border-secondary">
                                        /<?php echo $tenant['slug']; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($tenant['owner_email']); ?></td>
                                <td>
                                    <?php if ($tenant['status'] == 'active'): ?>
                                        <span class="badge badge-success px-3 py-2">ACTIVO</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger px-3 py-2">SUSPENDIDO</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($tenant['created_at'])); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="export_tenant.php?tenant_id=<?php echo $tenant['tenant_id']; ?>"
                                            class="btn btn-outline-success btn-sm" title="Respaldar Datos">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="edit_tenant.php?tenant_id=<?php echo $tenant['tenant_id']; ?>"
                                            class="btn btn-outline-info btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($tenant['status'] == 'active'): ?>
                                            <a href="tenant_action.php?tenant_id=<?php echo $tenant['tenant_id']; ?>&action=suspend"
                                                class="btn btn-outline-warning btn-sm" title="Suspender"
                                                onclick="return confirm('¿Suspender esta barbería?');">
                                                <i class="fas fa-pause"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="tenant_action.php?tenant_id=<?php echo $tenant['tenant_id']; ?>&action=activate"
                                                class="btn btn-outline-success btn-sm" title="Activar">
                                                <i class="fas fa-play"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="tenant_action.php?tenant_id=<?php echo $tenant['tenant_id']; ?>&action=delete"
                                            class="btn btn-outline-danger btn-sm" title="Eliminar"
                                            onclick="return confirm('¡ADVERTENCIA! Eliminación permanente. Se recomienda realizar un RESPALDO primero. ¿Desea continuar con la eliminación?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'Includes/templates/footer.php'; ?>