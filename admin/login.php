<?php
session_start();
include 'connect.php';
include 'Includes/functions/functions.php';
include '../Includes/csrf.php';
include '../Includes/tenant_context.php';

// IF THE USER HAS ALREADY LOGGED IN
if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['admin_id_barbershop_Xw211qAAsq4'])) {
	header('Location: index.php');
	exit();
}

$pageTitle = 'Panel Administrativo - Acceso';
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>
		<?php echo $pageTitle; ?>
	</title>
	<link href="Design/fonts/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
	<link href="Design/css/sb-admin-2.min.css" rel="stylesheet">
	<link href="Design/css/main.css" rel="stylesheet">
	<link href="../Design/css/premium.css" rel="stylesheet">
	<style>
		body {
			background: #000;
			height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.login-bg {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: linear-gradient(45deg, #111 0%, #000 100%);
			z-index: -1;
		}
	</style>
</head>

<body>
	<div class="login-bg"></div>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-5 col-lg-6 col-md-9">
				<div class="card o-hidden border-0 shadow-lg my-5 glass-card animate-fade-in"
					style="background: rgba(17,17,17,0.8);">
					<div class="card-body p-0">
						<div class="p-5">
							<div class="text-center mb-4">
								<i class="fas fa-cut fa-3x text-gold mb-3 glow-text"></i>
								<h1 class="h4 text-white text-uppercase font-weight-bold letter-spacing-1">Panel
									Administrativo</h1>
								<p class="text-white-50 small">SaaS Barbershop Management</p>
							</div>

							<?php
							if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_login_btn'])) {
								if (!verifyCsrfToken($_POST['csrf_token'])) {
									echo '<div class="alert alert-danger small">Error de seguridad (CSRF).</div>';
								} else {
									$username = test_input($_POST['username']);
									$password = test_input($_POST['password']);
									$tenant_id = getCurrentTenantId($con);

									$stmt = $con->prepare("SELECT admin_id, username, password FROM barber_admin WHERE username = ? AND tenant_id = ?");
									$stmt->execute([$username, $tenant_id]);
									$row = $stmt->fetch();

									if ($row) {
										$db_password = $row['password'];
										$loginSuccess = false;

										if (sha1($password) === $db_password) {
											$newHash = password_hash($password, PASSWORD_DEFAULT);
											$upd = $con->prepare("UPDATE barber_admin SET password = ? WHERE admin_id = ?");
											$upd->execute([$newHash, $row['admin_id']]);
											$loginSuccess = true;
										} elseif (password_verify($password, $db_password)) {
											$loginSuccess = true;
										}

										if ($loginSuccess) {
											$_SESSION['username_barbershop_Xw211qAAsq4'] = $username;
											$_SESSION['admin_id_barbershop_Xw211qAAsq4'] = $row['admin_id'];
											header('Location: index.php');
											exit();
										} else {
											echo '<div class="alert alert-danger small text-center">Usuario o contraseña incorrectos.</div>';
										}
									} else {
										echo '<div class="alert alert-danger small text-center">Usuario o contraseña incorrectos.</div>';
									}
								}
							}
							?>

							<form class="user" method="POST" action="">
								<?php csrfInput(); ?>
								<div class="form-group">
									<input type="text" name="username"
										class="form-control form-control-user glass-card text-white border-0 py-4"
										style="background: rgba(255,255,255,0.05);" placeholder="Usuario" required>
								</div>
								<div class="form-group">
									<input type="password" name="password"
										class="form-control form-control-user glass-card text-white border-0 py-4"
										style="background: rgba(255,255,255,0.05);" placeholder="Contraseña" required>
								</div>
								<button type="submit" name="admin_login_btn"
									class="btn btn-gold-premium btn-user btn-block shadow-lg mt-4 font-weight-bold">
									INICIAR SESIÓN
								</button>
							</form>
							<hr class="border-secondary my-4">
							<div class="text-center">
								<a class="small text-gold" href="../index.php">Volver al Sitio Web</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="Design/js/jquery.min.js"></script>
	<script src="Design/js/bootstrap.bundle.min.js"></script>
	<script src="Design/js/sb-admin-2.min.js"></script>
	<script src="../Design/js/premium.js"></script>
</body>

</html>