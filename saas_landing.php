<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barberia SaaS - Crea tu Barbería</title>
    <link rel="stylesheet" href="Design/css/bootstrap.min.css">
    <link rel="stylesheet" href="Design/css/main.css">
    <style>
        body {
            background-color: #111;
            color: white;
        }

        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }

        .btn-gold {
            background-color: #D4AF37;
            color: black;
            border: none;
            padding: 10px 30px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .btn-gold:hover {
            background-color: #bfa345;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container hero">
        <h1>Bienvenido a Barberia SaaS</h1>
        <p class="lead">La mejor plataforma para gestionar tu barbería.</p>
        <div class="mt-4">
            <a href="register_tenant.php" class="btn btn-gold btn-lg">Registrar mi Barbería</a>
            <a href="super-admin/login.php" class="btn btn-outline-light btn-lg ml-3">Admin del Sistema</a>
        </div>
    </div>
</body>

</html>