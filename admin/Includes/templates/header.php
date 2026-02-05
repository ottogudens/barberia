<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Barbershop Booking Website">

	<title><?php echo $pageTitle ?></title>

	<!-- FONTS FILE -->
	<link href="Design/fonts/css/all.min.css" rel="stylesheet" type="text/css">
	<link rel="shortcut icon" href="../templates/faviconconfiguroweb.png" type="image/x-icon">
	<!-- Nunito FONT FAMILY FILE -->
	<link
		href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
		rel="stylesheet">

	<!-- CSS FILES -->
	<link href="Design/css/sb-admin-2.min.css" rel="stylesheet">
	<link href="Design/css/main.css" rel="stylesheet">

	<!-- Barber Icons -->
	<link rel="stylesheet" type="text/css" href="Design/fonts/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="Design/css/barber-icons.css">

</head>

<body id="page-top">

	<!-- Page Wrapper -->
	<div id="wrapper">

		<!-- Sidebar -->
		<!-- Changed bg-gradient-danger to bg-gradient-dark and added style -->
		<ul class="navbar-nav bg-gradient-dark sidebar sidebar-dark accordion" id="accordionSidebar"
			style="background: #111;">

			<!-- Sidebar - Brand -->
			<a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
				<div class="sidebar-brand-icon rotate-n-10">
					<!-- Icon or Logo could go here -->
					<i class="bs bs-scissors-1" style="color: #D4AF37;"></i>
				</div>
				<?php
				// Fetch Tenant Name if not already available
				if (isset($tenant_id)) {
					$stmtBrand = $con->prepare("SELECT name FROM tenants WHERE tenant_id = ?");
					$stmtBrand->execute([$tenant_id]);
					$brand = $stmtBrand->fetch();
					$shopName = $brand['name'] ?? 'Barbería';
				} else {
					$shopName = 'Barbería SaaS';
				}
				?>
				<div class="sidebar-brand-text mx-3" style="color: #D4AF37;"><?php echo htmlspecialchars($shopName); ?>
				</div>
			</a>

			<!-- Divider -->
			<hr class="sidebar-divider my-0">

			<!-- Nav Item - Dashboard -->
			<li class="nav-item active">
				<a class="nav-link" href="index.php">
					<i class="fas fa-fw fa-tachometer-alt"></i>
					<span>Dashboard</span>
				</a>
			</li>

			<!-- Divider -->
			<hr class="sidebar-divider">

			<li class="nav-item">
				<a class="nav-link" href="reservations_stats.php">
					<i class="fas fa-history"></i>
					<span>Historial y Reportes</span>
				</a>
			</li>

			<!-- Heading -->
			<div class="sidebar-heading">
				Gestionar Los Servicios
			</div>

			<!-- Nav Item - Pages Collapse Menu -->
			<li class="nav-item">
				<a class="nav-link" href="service-categories.php">
					<i class="fas fa-list"></i>
					<span>Categorías de Servicios</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="services.php">
					<i class="bs bs-scissors-1"></i>
					<span>Servicios</span>
				</a>
			</li>
			<!-- Divider -->
			<hr class="sidebar-divider">

			<!-- Heading -->
			<div class="sidebar-heading">
				Clientes y Staff
			</div>

			<!-- Nav Item - Charts -->
			<li class="nav-item">
				<a class="nav-link" href="clients.php">
					<i class="far fa-address-card"></i>
					<span>Clientes</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="employees.php">
					<i class="far fa-user"></i>
					<span>Empleados</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="employees-schedule.php">
					<i class="fas fa-calendar-week icon-ver"></i>
					<span>Horario de Empleados</span>
				</a>
			</li>

			<!-- Divider -->
			<hr class="sidebar-divider">

			<!-- Heading -->
			<div class="sidebar-heading">
				Configuración
			</div>

			<li class="nav-item">
				<a class="nav-link" href="website_settings.php">
					<i class="fas fa-globe"></i>
					<span>Sitio Web</span>
				</a>
			</li>

			<li class="nav-item">
				<a class="nav-link" href="backup.php">
					<i class="fas fa-database"></i>
					<span>Respaldo BD</span>
				</a>
			</li>

			<li class="nav-item">
				<a class="nav-link" href="offers.php">
					<i class="fas fa-tag"></i>
					<span>Ofertas</span>
				</a>
			</li>



			<li class="nav-item">
				<a class="nav-link" href="gallery.php">
					<i class="fas fa-images"></i>
					<span>Galería</span>
				</a>
			</li>

			<!-- Divider -->
			<hr class="sidebar-divider">

			<!-- Heading -->
			<div class="sidebar-heading">
				Finanzas
			</div>

			<li class="nav-item">
				<a class="nav-link" href="payments.php">
					<i class="fas fa-hand-holding-usd"></i>
					<span>Ingresos</span>
				</a>
			</li>

			<li class="nav-item">
				<a class="nav-link" href="expenses.php">
					<i class="fas fa-file-invoice-dollar"></i>
					<span>Egresos y Pagos</span>
				</a>
			</li>

			<!-- Divider -->
			<hr class="sidebar-divider d-none d-md-block">

			<!-- Sidebar Toggler (Sidebar) -->
			<div class="text-center d-none d-md-inline">
				<button class="rounded-circle border-0" id="sidebarToggle"></button>
			</div>
		</ul>

		<!-- End of Sidebar -->

		<!-- Content Wrapper -->
		<div id="content-wrapper" class="d-flex flex-column">

			<!-- Main Content -->
			<div id="content">

				<!-- Topbar -->
				<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

					<!-- Sidebar Toggle (Topbar) -->
					<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
						<i class="fa fa-bars"></i>
					</button>

					<!-- Topbar Navbar -->
					<ul class="navbar-nav ml-auto">
						<li class="nav-item">
							<a class="nav-link" href="../" target="_blank">
								<i class="far fa-eye"></i>
								<span style="margin-left: 5px;">Ver Sitio Web</span>
							</a>
						</li>
						<div class="topbar-divider d-none d-sm-block"></div>

						<!-- Nav Item - User Information -->
						<li class="nav-item dropdown no-arrow">
							<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
								data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="mr-2 d-none d-lg-inline text-gray-600 small">
									<?php echo $_SESSION['username_barbershop_Xw211qAAsq4']; ?>
								</span>
								<i class="fas fa-caret-down"></i>
							</a>

							<!-- Dropdown - User Information -->
							<div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
								aria-labelledby="userDropdown">
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
									<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
									Cerrar Sesión
								</a>
							</div>
						</li>
					</ul>
				</nav>
				<!-- End of Topbar -->