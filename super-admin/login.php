<?php
session_start();
include '../connect.php';
include '../Includes/functions/functions.php';
include '../Includes/csrf.php';

// If already logged in
if (isset($_SESSION['super_admin_username'])) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Super Admin Login</title>
    <link href="../Design/fonts/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="../Design/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../Design/css/main.css" rel="stylesheet">
</head>

<body>
    <div class="login">
        <form class="login-container" method="POST" action="login.php">
            <?php csrfInput(); ?>
            <h2 class="text-center mb-4" style="color:#D4AF37;">Super Admin Access</h2>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!verifyCsrfToken($_POST['csrf_token'])) {
                    die("CSRF Validation Failed");
                }
                $username = test_input($_POST['username']);
                $password = test_input($_POST['password']);

                $stmt = $con->prepare("SELECT id, username, password FROM super_admins WHERE username = ?");
                $stmt->execute([$username]);
                $row = $stmt->fetch();

                $stmt = $con->prepare("SELECT id, username, password FROM super_admins WHERE username = ?");
                $stmt->execute([$username]);
                $row = $stmt->fetch();

                if ($row && password_verify($password, $row['password'])) {
                    $_SESSION['super_admin_username'] = $username;
                    $_SESSION['super_admin_id'] = $row['id'];
                    header('Location: dashboard.php');
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Usuario o contraseña incorrectos.</div>";
                }
            }
            ?>

            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block"
                style="background-color: #D4AF37; border-color: #D4AF37; color: black;">Ingresar</button>
        </form>
    </div>
</body>

</html>