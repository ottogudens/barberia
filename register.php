<?php
session_start();
include "connect.php";
include "Includes/functions/functions.php";
include "Includes/tenant_context.php";

$tenant_id = getCurrentTenantId($con);

// Fetch settings for branding
$settings = [];
$stmt = $con->prepare("SELECT setting_key, setting_value FROM website_settings WHERE tenant_id = ?");
$stmt->execute([$tenant_id]);
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
$primary_color = isset($settings['primary_color']) ? $settings['primary_color'] : '#D4AF37';
$secondary_color = isset($settings['secondary_color']) ? $settings['secondary_color'] : '#111111';

if (isset($_SESSION['client_logged_in'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_client'])) {
    $first_name = test_input($_POST['first_name']);
    $last_name = test_input($_POST['last_name']);
    $phone_number = test_input($_POST['phone_number']);
    $email = test_input($_POST['email']);
    $password = $_POST['password'];

    // Check if email exists
    $stmt = $con->prepare("SELECT * FROM clients WHERE client_email = ? AND tenant_id = ?");
    $stmt->execute([$email, $tenant_id]);

    if ($stmt->rowCount() > 0) {
        $error = "El correo electrónico ya está registrado.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("INSERT INTO clients (first_name, last_name, phone_number, client_email, password, tenant_id) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$first_name, $last_name, $phone_number, $email, $hashed_password, $tenant_id])) {
            $_SESSION['client_logged_in'] = true;
            $_SESSION['client_id'] = $con->lastInsertId();
            $_SESSION['client_name'] = $first_name . " " . $last_name;
            header("Location: appointment.php");
            exit();
        } else {
            $error = "Error al registrarse. Intente nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Barbería</title>
    <link rel="stylesheet" href="Design/css/bootstrap.min.css">
    <link rel="stylesheet" href="Design/css/main.css">
    <style>
        body {
            background-color:
                <?php echo $secondary_color; ?>
            ;
            color: #fff;
        }

        .login-card {
            background-color: #222;
            border: 1px solid
                <?php echo $primary_color; ?>
            ;
            padding: 40px;
            border-radius: 10px;
            margin-top: 50px;
        }

        .btn-primary {
            background-color:
                <?php echo $primary_color; ?>
            ;
            border-color:
                <?php echo $primary_color; ?>
            ;
        }

        .form-control {
            background-color: #333;
            color: #fff;
            border: 1px solid #555;
        }

        .form-control:focus {
            background-color: #333;
            color: #fff;
            border-color:
                <?php echo $primary_color; ?>
            ;
            box-shadow: none;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card">
                    <h2 class="text-center mb-4">Registrarse</h2>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="register.php">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" name="first_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Apellido</label>
                                    <input type="text" name="last_name" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" name="phone_number" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="register_client" class="btn btn-primary btn-block">Crear
                            Cuenta</button>
                        <div class="mt-3 text-center">
                            <a href="login.php" style="color: <?php echo $primary_color; ?>">¿Ya tienes cuenta? Ingresa
                                aquí</a>
                            <br>
                            <a href="index.php" style="color: #ccc;">Volver al Inicio</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>