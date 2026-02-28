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

$loginError = '';

// Handle login POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $loginError = "Error de seguridad (CSRF).";
    } else {
        $username = test_input($_POST['username']);
        $password = test_input($_POST['password']);

        $stmt = $con->prepare("SELECT id, username, password FROM super_admins WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch();

        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['super_admin_username'] = $username;
            $_SESSION['super_admin_id'] = $row['id'];
            header('Location: dashboard.php');
            exit();
        } else {
            $loginError = "Usuario o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login | SKBarber</title>

    <!-- FONTS -->
    <link href="/Design/fonts/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- CSS -->
    <link href="/Design/css/bootstrap.min.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="/Design/css/sb-admin-2.min.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="/Design/css/premium.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        body {
            background: #000;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
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

        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container i {
            font-size: 3rem;
            color: var(--premium-gold);
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="login-bg"></div>

    <div class="login-card glass-card animate-fade-in">
        <div class="logo-container">
            <i class="fas fa-crown glow-text"></i>
            <h2 class="text-white font-weight-bold tracking-widest text-uppercase h4 mt-2">Super Admin</h2>
            <p class="text-white-50 small">Control Maestro de Plataforma</p>
        </div>

        <?php if ($loginError): ?>
            <div class="alert alert-danger border-0 bg-danger text-white small mb-4 animate-fade-in"
                style="background: rgba(220, 53, 69, 0.2) !important; border: 1px solid rgba(220, 53, 69, 0.3) !important;">
                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $loginError; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="mt-4">
            <?php csrfInput(); ?>
            <div class="form-group mb-4">
                <label class="text-gold small font-weight-bold text-uppercase mb-2 d-block">Usuario</label>
                <input type="text" name="username" class="form-control bg-dark border-secondary text-white px-3 py-4"
                    style="background: rgba(255,255,255,0.05) !important; border-radius: 10px;"
                    placeholder="Ingrese su usuario" required autofocus>
            </div>
            <div class="form-group mb-4">
                <label class="text-gold small font-weight-bold text-uppercase mb-2 d-block">Contraseña</label>
                <input type="password" name="password"
                    class="form-control bg-dark border-secondary text-white px-3 py-4"
                    style="background: rgba(255,255,255,0.05) !important; border-radius: 10px;" placeholder="••••••••"
                    required>
            </div>
            <button type="submit"
                class="btn btn-gold-premium btn-block py-3 font-weight-bold text-uppercase mt-4 shadow-lg">
                Ingresar al Sistema
            </button>

            <div class="text-center mt-5">
                <p class="text-white-50 x-small mb-0">&copy; <?php echo date('Y'); ?> SKBarber SaaS Platform</p>
                <p class="text-gold x-small">Powered by Skale IA</p>
            </div>
    </div>

    <script src="/Design/js/jquery.min.js?v=<?php echo time(); ?>"></script>
    <script src="/Design/js/bootstrap.bundle.min.js?v=<?php echo time(); ?>"></script>
    <script src="/Design/js/premium.js?v=<?php echo time(); ?>"></script>
</body>

</html>