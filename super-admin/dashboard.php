<?php
session_start();
if (!isset($_SESSION['super_admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../connect.php';
include '../Includes/functions/functions.php';

// Fetch Tenants
$stmt = $con->prepare("SELECT * FROM tenants ORDER BY created_at DESC");
$stmt->execute();
$tenants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Super Admin Dashboard</title>
    <link href="../Design/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../Design/fonts/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <style>
        /* Custom SKBarber Super Admin Styles */
        .bg-gradient-gold {
            background: linear-gradient(180deg, #D4AF37 10%, #C5A028 100%);
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
            margin: 0 2px;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }

        .sidebar-brand-text {
            color: #000 !important;
            font-weight: 700;
        }

        .nav-item .nav-link {
            color: rgba(0, 0, 0, .8);
        }

        .nav-item .nav-link:hover {
            color: rgba(0, 0, 0, 1);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-gold sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-text mx-3">SKBarber</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Tenants</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="change_password.php">
                    <i class="fas fa-fw fa-key"></i>
                    <span>Change Password</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-fw fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h1 class="h3 mb-0 text-gray-800">Gestionar Barberías</h1>
                </nav>

                <div class="container-fluid">
                    <?php if (isset($_SESSION['msg'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show"
                            role="alert">
                            <?php echo $_SESSION['msg']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php
                        unset($_SESSION['msg']);
                        unset($_SESSION['msg_type']);
                        ?>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Listado de Barberías</h6>
                            <a href="#" class="btn btn-sm btn-primary shadow-sm"
                                style="background-color: #D4AF37; border-color: #D4AF37; color: black;"><i
                                    class="fas fa-plus fa-sm text-gray-50"></i> Nueva Barbería</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Slug (URL)</th>
                                            <th>Email Propietario</th>
                                            <th>Estado</th>
                                            <th>Creado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tenants as $tenant): ?>
                                            <tr>
                                                <td><?php echo $tenant['tenant_id']; ?></td>
                                                <td><?php echo htmlspecialchars($tenant['name']); ?></td>
                                                <td>
                                                    <a href="https://barberia.skale.cl/<?php echo $tenant['slug']; ?>"
                                                        target="_blank">
                                                        <?php echo $tenant['slug']; ?>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($tenant['owner_email']); ?></td>
                                                <td>
                                                    <?php if ($tenant['status'] == 'active'): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php elseif ($tenant['status'] == 'suspended'): ?>
                                                        <span class="badge badge-warning">Suspended</span>
                                                    <?php else: ?>
                                                        <span
                                                            class="badge badge-secondary"><?php echo $tenant['status']; ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $tenant['created_at']; ?></td>
                                                <td>
                                                    <a href="edit_tenant.php?tenant_id=<?php echo $tenant['tenant_id']; ?>"
                                                        class="btn btn-info btn-sm">Editar</a>

                                                    <?php if ($tenant['status'] == 'suspended'): ?>
                                                        <a href="tenant_action.php?tenant_id=<?php echo $tenant['tenant_id']; ?>&action=activate"
                                                            class="btn btn-success btn-sm">Activar</a>
                                                    <?php else: ?>
                                                        <a href="tenant_action.php?tenant_id=<?php echo $tenant['tenant_id']; ?>&action=suspend"
                                                            class="btn btn-warning btn-sm"
                                                            onclick="return confirm('¿Seguro que quieres suspender esta barbería?');">Suspender</a>
                                                    <?php endif; ?>

                                                    <a href="tenant_action.php?tenant_id=<?php echo $tenant['tenant_id']; ?>&action=delete"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('¡ADVERTENCIA! Esto eliminará TODOS los datos de esta barbería permanentemente. ¿Estás seguro?');">Eliminar</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>