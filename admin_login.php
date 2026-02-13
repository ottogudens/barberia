<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SKBarber</title>
    <link href="Design/fonts/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #e0e0e0;
            background: linear-gradient(180deg, #0a0a0a 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            background: linear-gradient(145deg, #1a1a1a 0%, #0f0f0f 100%);
            padding: 3rem;
            border-radius: 20px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo-container img {
            height: 80px;
            width: auto;
            margin-bottom: 1rem;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #F4E5C3 0%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            text-align: center;
            color: #808080;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #b0b0b0;
            font-weight: 500;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 10px;
            color: #e0e0e0;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            transition: all 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            outline: none;
            border-color: #D4AF37;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .login-button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%);
            color: #0a0a0a;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border: 1px solid;
        }

        .alert-danger {
            background: rgba(255, 50, 50, 0.1);
            border-color: rgba(255, 50, 50, 0.3);
            color: #ff5050;
        }

        .links {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(212, 175, 55, 0.1);
        }

        .links a {
            color: #D4AF37;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .links a:hover {
            color: #F4E5C3;
        }

        .links p {
            color: #808080;
            font-size: 0.85rem;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <?php
    session_start();
    include "connect.php";
    include "Includes/functions/functions.php";
    include "Includes/tenant_context.php";

    $tenant_id = getCurrentTenantId($con);

    if (!$tenant_id) {
        header('Location: landing.php');
        exit();
    }

    if (isset($_SESSION['admin_logged_in'])) {
        header('Location: admin/index.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = test_input($_POST['username']);
        $password = $_POST['password'];

        $stmt = $con->prepare("SELECT * FROM barber_admin WHERE username = ? AND tenant_id = ?");
        $stmt->execute([$username, $tenant_id]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: admin/index.php');
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    }
    ?>

    <div class="login-container">
        <div class="logo-container">
            <img src="Design/img/skbarber-logo.png" alt="SKBarber Logo">
        </div>

        <h1>Panel de Administración</h1>
        <p class="subtitle">Inicia sesión para gestionar tu barbería</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin_login.php">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="login-button">Iniciar Sesión</button>
        </form>

        <div class="links">
            <a href="index.php">← Volver a la página principal</a>
            <p>¿Eres cliente? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>

</html>