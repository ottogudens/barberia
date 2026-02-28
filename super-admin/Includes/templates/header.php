<?php
// super-admin/Includes/templates/header.php
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $pageTitle; ?> | Super Admin SKBarber
    </title>

    <!-- FONTS -->
    <link href="../Design/fonts/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- CSS -->
    <link href="../Design/css/sb-admin-2.min.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../Design/css/main.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../Design/css/premium.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        .sidebar {
            background: #000 !important;
            border-right: 1px solid rgba(212, 175, 55, 0.15);
        }

        .sidebar-brand-text {
            color: var(--premium-gold) !important;
            letter-spacing: 2.5px;
            font-weight: 800 !important;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.6) !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-item.active .nav-link {
            color: var(--premium-gold) !important;
            background: rgba(212, 175, 55, 0.05);
        }

        .nav-link i {
            color: inherit !important;
            margin-right: 10px;
        }

        .bg-gold-gradient {
            background: linear-gradient(135deg, var(--premium-gold), #C5A028);
            border: none;
            color: black !important;
            font-weight: 700;
        }

        #content-wrapper {
            background: #050505 !important;
        }

        .topbar {
            background: rgba(10, 10, 10, 0.95) !important;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-cut text-gold"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SKBarber <small>Master</small></div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item <?php if ($pageTitle == 'Dashboard')
                echo 'active'; ?>">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading text-white-50">Administración</div>

            <li class="nav-item <?php if ($pageTitle == 'Tenants')
                echo 'active'; ?>">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Gestionar Tenants</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="clear_cache.php">
                    <i class="fas fa-fw fa-broom"></i>
                    <span>Limpiar Caché</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <li class="nav-item">
                <a class="nav-link" href="change_password.php">
                    <i class="fas fa-fw fa-key"></i>
                    <span>Seguridad</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-fw fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </li>

            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline mt-4">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars text-gold"></i>
                    </button>
                    <h1 class="h4 mb-0 text-white ml-2 glow-text text-uppercase font-weight-bold">Panel Maestro</h1>
                </nav>