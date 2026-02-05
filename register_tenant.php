<?php
include "connect.php";
include "Includes/functions/functions.php";
include "Includes/csrf.php";

$pageTitle = "Registro de Barbería";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registra tu Barbería - Barberia SaaS</title>
    <link rel="stylesheet" href="Design/css/bootstrap.min.css">
    <link rel="stylesheet" href="Design/css/main.css">
    <style>
        body {
            background-color: #000;
            background-image: url('Design/images/barber_bg_pattern.png');
            /* If available, or just dark */
            color: #ccc;
            font-family: 'Nunito', sans-serif;
        }

        .register-container {
            max-width: 600px;
            margin: 50px auto;
            background: #111;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
            border: 1px solid #333;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            color: #D4AF37;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-header p {
            color: #888;
        }

        .form-control {
            background-color: #222;
            border: 1px solid #444;
            color: #fff;
        }

        .form-control:focus {
            background-color: #222;
            border-color: #D4AF37;
            color: #fff;
            box-shadow: 0 0 5px rgba(212, 175, 55, 0.5);
        }

        label {
            color: #D4AF37;
            font-weight: 600;
        }

        .btn-gold {
            background-color: #D4AF37;
            color: black;
            border: none;
            font-weight: bold;
            text-transform: uppercase;
            padding: 15px;
            transition: all 0.3s;
        }

        .btn-gold:hover {
            background-color: #bfa345;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }

        .input-group-text {
            background-color: #333;
            border: 1px solid #444;
            color: #aaa;
        }

        a {
            color: #D4AF37;
        }

        a:hover {
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="register-container">
            <div class="form-header">
                <h2>Únete a Barberia SaaS</h2>
                <p>Comienza a gestionar tu negocio hoy mismo.</p>
            </div>

            <?php
            if (isset($_SESSION['register_error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['register_error'] . '</div>';
                unset($_SESSION['register_error']);
            }
            ?>

            <form action="register_tenant_script.php" method="POST">
                <?php csrfInput(); ?>

                <h4 class="mb-3">Datos del Negocio</h4>
                <div class="form-group">
                    <label for="shop_name">Nombre de la Barbería</label>
                    <input type="text" class="form-control" name="shop_name" id="shop_name" required
                        placeholder="Ej. Barbería El Bigote">
                </div>

                <div class="form-group">
                    <label for="slug">URL Personalizada (Slug)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="slug" id="slug" required placeholder="el-bigote"
                            pattern="[a-z0-9-]+" title="Solo letras minúsculas, números y guiones">
                        <div class="input-group-append">
                            <span class="input-group-text">.barberia.com</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">Esta será la dirección web de tu negocio.</small>
                </div>

                <h4 class="mb-3 mt-4">Datos del Administrador</h4>
                <div class="form-group">
                    <label for="admin_username">Usuario Administrador</label>
                    <input type="text" class="form-control" name="admin_username" id="admin_username" required
                        placeholder="admin">
                </div>

                <div class="form-group">
                    <label for="owner_email">Correo Electrónico</label>
                    <input type="email" class="form-control" name="owner_email" id="owner_email" required
                        placeholder="tu@email.com">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" required
                        minlength="6">
                </div>

                <button type="submit" class="btn btn-gold btn-block btn-lg mt-4">Registrar Barbería</button>
            </form>
            <div class="text-center mt-3">
                <a href="index.php">Volver al inicio</a>
            </div>
        </div>
    </div>

</body>

</html>