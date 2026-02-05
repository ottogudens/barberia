<?php
session_start();

// IF THE USER HAS ALREADY LOGGED IN
if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {
	header('Location: index.php');
	exit();
}
// ELSE
$pageTitle = 'Peluquería ConfiguroWeb';
include 'connect.php';
include 'Includes/functions/functions.php';
include '../Includes/csrf.php';
include '../Includes/tenant_context.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Panel Administrativo</title>
	<!-- FONTS FILE -->
	<link href="Design/fonts/css/all.min.css" rel="stylesheet" type="text/css">

	<!-- Nunito FONT FAMILY FILE -->
	<link
		href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
		rel="stylesheet">

	<!-- CSS FILES -->
	<link href="Design/css/sb-admin-2.min.css" rel="stylesheet">
	<link href="Design/css/main.css" rel="stylesheet">
</head>

<body>
	<div class="login">
		<form class="login-container validate-form" name="login-form" method="POST"
			action="login.php<?php echo isset($_GET['tenant_slug']) ? '?tenant_slug=' . htmlspecialchars($_GET['tenant_slug']) : ''; ?>"
			onsubmit="return validateLogInForm()">
			<?php csrfInput(); ?>
			<span class="login100-form-title p-b-32">
				Panel Administrativo Peluquería
			</span>

			<!-- PHP SCRIPT WHEN SUBMIT -->

			<?php

			if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signin-button'])) {
				if (!verifyCsrfToken($_POST['csrf_token'])) {
					die("CSRF Token Verification Failed");
				}
				$username = test_input($_POST['username']);
				$password = test_input($_POST['password']);
				$hashedPass = sha1($password);

				$tenant_id = getCurrentTenantId($con);

				$stmt = $con->prepare("Select admin_id, username, password from barber_admin where username = ? AND tenant_id = ?");
				$stmt->execute(array($username, $tenant_id));
				$row = $stmt->fetch();
				$count = $stmt->rowCount();

				if ($count > 0) {
					$db_password = $row['password'];
					$loginSuccess = false;

					// 1. Check if password is using legacy SHA1
					if (sha1($password) === $db_password) {
						// Upgrade to Bcrypt
						$newHash = password_hash($password, PASSWORD_DEFAULT);
						$upd = $con->prepare("UPDATE barber_admin SET password = ? WHERE admin_id = ?");
						$upd->execute(array($newHash, $row['admin_id']));
						$loginSuccess = true;
					}
					// 2. Check if password is using modern Hash
					elseif (password_verify($password, $db_password)) {
						$loginSuccess = true;
					}

					if ($loginSuccess) {
						$_SESSION['username_barbershop_Xw211qAAsq4'] = $username;
						$_SESSION['password_barbershop_Xw211qAAsq4'] = $password; // Keeping for session isset check
						$_SESSION['admin_id_barbershop_Xw211qAAsq4'] = $row['admin_id'];
						header('Location: index.php');
						die();
					} else {
						// Password incorrect
						?>
						<div class="alert alert-danger">
							<button data-dismiss="alert" class="close close-sm" type="button">
								<span aria-hidden="true">×</span>
							</button>
							<div class="messages">
								<div>¡El nombre de usuario y/o la contraseña son incorrectos!</div>
							</div>
						</div>
						<?php
					}
				} else {
					?>

					<div class="alert alert-danger">
						<button data-dismiss="alert" class="close close-sm" type="button">
							<span aria-hidden="true">×</span>
						</button>
						<div class="messages">
							<div>¡El nombre de usuario y/o la contraseña son incorrectos!</div>
						</div>
					</div>

					<?php
				}
			}

			?>

			<!-- USERNAME INPUT -->

			<div class="form-input">
				<span class="txt1">Usuario</span>
				<input type="text" name="username" class="form-control"
					oninput="getElementById('required_username').style.display = 'none'" autocomplete="off">
				<span class="invalid-feedback" id="required_username">¡Se requiere nombre de usuario!</span>
			</div>

			<!-- PASSWORD INPUT -->

			<div class="form-input">
				<span class="txt1">Contraseña</span>
				<input type="password" name="password" class="form-control"
					oninput="getElementById('required_password').style.display = 'none'" autocomplete="new-password">
				<span class="invalid-feedback" id="required_password">¡Se requiere contraseña!</span>
			</div>

			<!-- SIGN IN BUTTON -->

			<p>
				<button type="submit" name="signin-button">Acceder</button>
			</p>


		</form>
	</div>

	<!-- Footer -->
	<footer class="sticky-footer bg-white">
		<div class="container my-auto">
			<div class="copyright text-center my-auto">
				<span><a
						href="https://www.configuroweb.com/46-aplicaciones-gratuitas-en-php-python-y-javascript/#Aplicaciones-gratuitas-en-PHP,-Python-y-Javascript">Para
						más desarrollos ConfiguroWeb</a></span>
			</div>
		</div>
	</footer>
	<!-- End of Footer -->

	<!-- INCLUDE JS SCRIPTS -->
	<script src="Design/js/jquery.min.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script src="Design/js/bootstrap.bundle.min.js"></script>
	<script src="Design/js/sb-admin-2.min.js"></script>
	<script src="Design/js/main.js"></script>
</body>

</html>