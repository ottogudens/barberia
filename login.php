<?php
session_start();
include "connect.php";
include "Includes/functions/functions.php";
include "Includes/tenant_context.php";

$tenant_id = getCurrentTenantId($con);

// Fetch settings for branding (fallback defaults if needed)
$settings = [];
$stmt = $con->prepare("SELECT setting_key, setting_value FROM website_settings WHERE tenant_id = ?");
$stmt->execute([$tenant_id]);
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
$shop_name = isset($settings['shop_name']) ? $settings['shop_name'] : 'Gold Luk Barbershop';

if (isset($_SESSION['client_logged_in'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_client'])) {
    $email = test_input($_POST['email']);
    $password = $_POST['password'];

    $stmt = $con->prepare("SELECT * FROM clients WHERE client_email = ? AND tenant_id = ?");
    $stmt->execute([$email, $tenant_id]);
    $client = $stmt->fetch();

    if ($client && password_verify($password, $client['password'])) {
        $_SESSION['client_logged_in'] = true;
        $_SESSION['client_id'] = $client['client_id'];
        $_SESSION['client_name'] = $client['first_name'] . " " . $client['last_name'];
        header("Location: appointment.php");
        exit();
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?php echo $shop_name; ?></title>

    <!-- FONTS & CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Prata&family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="Design/css/bootstrap.min.css">
    <link rel="stylesheet" href="Design/fonts/css/all.min.css">
    <link rel="stylesheet" href="Design/css/main.css"> <!-- Gold Luk Theme Styles -->

    <style>
        body {
            background: url('Design/images/barbershop_image_1.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            /* Dark overlay */
            z-index: 1;
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
            padding: 15px;
        }

        .login-card {
            background: rgba(17, 17, 17, 0.85);
            /* Dark semi-transparent */
            border: 1px solid var(--gold);
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            /* Glassmorphism */
        }

        .login-title {
            color: var(--gold);
            font-family: 'Prata', serif;
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid #444;
            color: #fff;
            height: 50px;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--gold);
            color: #fff;
            box-shadow: none;
        }

        .form-label {
            color: #ccc;
            margin-bottom: 10px;
        }

        .btn-gold-block {
            width: 100%;
            padding: 12px;
            font-size: 1.1rem;
            margin-top: 10px;
        }

        .login-footer {
            margin-top: 25px;
            text-align: center;
            color: #ccc;
        }

        .login-footer a {
            color: var(--gold);
            transition: color 0.3s;
        }

        .login-footer a:hover {
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Iniciar Sesión</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group mb-4">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-dark border-secondary text-gold"><i
                                    class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" name="email" class="form-control" placeholder="ejemplo@correo.com" required>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-dark border-secondary text-gold"><i
                                    class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="password" class="form-control" placeholder="********" required>
                    </div>
                </div>

                <button type="submit" name="login_client" class="btn btn-gold btn-gold-block shadow-sm">
                    Ingresar
                </button>

                <div class="login-footer">
                    <p class="mb-2">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
                    <a href="index.php" class="small text-muted"><i class="fas fa-arrow-left mr-1"></i> Volver al
                        Inicio</a>
                </div>
            </form>
        </div>
    </div>

</body>

</html>