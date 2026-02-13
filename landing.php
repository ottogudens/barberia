<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKBarber - Sistema de Gestión para Barberías</title>
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
            background: #0a0a0a;
            overflow-x: hidden;
        }

        /* Navbar */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(10px);
            padding: 1.5rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-container img {
            height: 50px;
            width: auto;
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #D4AF37 0%, #F4E5C3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        nav a {
            color: #e0e0e0;
            text-decoration: none;
            margin-left: 2rem;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #D4AF37;
        }

        .cta-button {
            background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%);
            color: #0a0a0a;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem 5%;
            background: linear-gradient(180deg, #0a0a0a 0%, #1a1a1a 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            50% {
                transform: translate(-100px, -100px) rotate(180deg);
            }
        }

        .hero-content {
            max-width: 900px;
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #F4E5C3 0%, #D4AF37 50%, #C5A028 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.3rem;
            color: #b0b0b0;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .button-secondary {
            background: transparent;
            color: #D4AF37;
            padding: 0.8rem 2rem;
            border: 2px solid #D4AF37;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .button-secondary:hover {
            background: #D4AF37;
            color: #0a0a0a;
        }

        /* Features Section */
        .features {
            padding: 8rem 5%;
            background: #111;
        }

        .section-title {
            text-align: center;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 4rem;
            background: linear-gradient(135deg, #F4E5C3 0%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: linear-gradient(145deg, #1a1a1a 0%, #0f0f0f 100%);
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid rgba(212, 175, 55, 0.1);
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: rgba(212, 175, 55, 0.3);
            box-shadow: 0 20px 40px rgba(212, 175, 55, 0.1);
        }

        .feature-icon {
            font-size: 3rem;
            color: #D4AF37;
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #F4E5C3;
        }

        .feature-card p {
            color: #a0a0a0;
            line-height: 1.6;
        }

        /* Pricing Section */
        .pricing {
            padding: 8rem 5%;
            background: #0a0a0a;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .pricing-card {
            background: linear-gradient(145deg, #1a1a1a 0%, #0f0f0f 100%);
            padding: 3rem 2rem;
            border-radius: 20px;
            text-align: center;
            border: 1px solid rgba(212, 175, 55, 0.1);
            transition: all 0.3s;
        }

        .pricing-card.featured {
            border: 2px solid #D4AF37;
            transform: scale(1.05);
        }

        .pricing-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 40px rgba(212, 175, 55, 0.2);
        }

        .pricing-card h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: #D4AF37;
        }

        .price {
            font-size: 3rem;
            font-weight: 700;
            margin: 1.5rem 0;
            color: #F4E5C3;
        }

        .price span {
            font-size: 1.2rem;
            color: #808080;
        }

        .pricing-features {
            list-style: none;
            margin: 2rem 0;
            text-align: left;
        }

        .pricing-features li {
            padding: 0.8rem 0;
            color: #b0b0b0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }

        .pricing-features li i {
            color: #D4AF37;
            margin-right: 0.8rem;
        }

        /* CTA Section */
        .cta-section {
            padding: 8rem 5%;
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
            text-align: center;
        }

        .cta-section h2 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #F4E5C3 0%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cta-section p {
            font-size: 1.3rem;
            color: #b0b0b0;
            margin-bottom: 2.5rem;
        }

        /* Footer */
        footer {
            background: #000;
            padding: 3rem 5%;
            text-align: center;
            border-top: 1px solid rgba(212, 175, 55, 0.1);
        }

        footer p {
            color: #808080;
        }

        footer a {
            color: #D4AF37;
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2rem;
            }

            nav {
                flex-direction: column;
                gap: 1rem;
            }

            nav a {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav>
        <div class="logo-container">
            <img src="Design/img/skbarber-logo.png" alt="SKBarber Logo">
            <span class="logo-text">SKBarber</span>
        </div>
        <div>
            <a href="#features">Características</a>
            <a href="#pricing">Precios</a>
            <a href="super-admin/login.php">Admin</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>La Plataforma Definitiva para Gestionar tu Barbería</h1>
            <p>Sistema completo de gestión multi-tenant diseñado específicamente para barberías modernas. Reservas,
                clientes, empleados y más, todo en un solo lugar.</p>
            <div class="hero-buttons">
                <a href="register_tenant.php" class="cta-button">Comenzar Gratis</a>
                <a href="#features" class="button-secondary">Ver Características</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <h2 class="section-title">Características Potentes</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-calendar-check"></i></div>
                <h3>Gestión de Citas</h3>
                <p>Sistema completo de reservas online con notificaciones automáticas y recordatorios para tus clientes.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-users"></i></div>
                <h3>Multi-Tenant</h3>
                <p>Cada barbería tiene su propio espacio aislado con datos completamente separados y seguros.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Análisis y Reportes</h3>
                <p>Visualiza el rendimiento de tu negocio con reportes detallados y análisis en tiempo real.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-user-tie"></i></div>
                <h3>Gestión de Empleados</h3>
                <p>Administra horarios, servicios y comisiones de tus empleados desde un solo panel.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>
                <h3>100% Responsive</h3>
                <p>Accede desde cualquier dispositivo: desktop, tablet o smartphone con una experiencia fluida.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-lock"></i></div>
                <h3>Seguridad Premium</h3>
                <p>Tus datos están protegidos con encriptación de nivel empresarial y backups automáticos.</p>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing">
        <h2 class="section-title">Precios Transparentes</h2>
        <div class="pricing-grid">
            <div class="pricing-card">
                <h3>Starter</h3>
                <div class="price">Gratis</div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> Hasta 50 citas/mes</li>
                    <li><i class="fas fa-check"></i> 1 Empleado</li>
                    <li><i class="fas fa-check"></i> Gestión básica de clientes</li>
                    <li><i class="fas fa-check"></i> Soporte por email</li>
                </ul>
                <a href="register_tenant.php" class="cta-button">Empezar</a>
            </div>
            <div class="pricing-card featured">
                <h3>Professional</h3>
                <div class="price">$29<span>/mes</span></div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> Citas ilimitadas</li>
                    <li><i class="fas fa-check"></i> Hasta 5 Empleados</li>
                    <li><i class="fas fa-check"></i> Reportes avanzados</li>
                    <li><i class="fas fa-check"></i> Notificaciones SMS</li>
                    <li><i class="fas fa-check"></i> Soporte prioritario</li>
                </ul>
                <a href="register_tenant.php" class="cta-button">Comenzar Prueba</a>
            </div>
            <div class="pricing-card">
                <h3>Enterprise</h3>
                <div class="price">Custom</div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> Todo en Professional</li>
                    <li><i class="fas fa-check"></i> Empleados ilimitados</li>
                    <li><i class="fas fa-check"></i> API personalizada</li>
                    <li><i class="fas fa-check"></i> Soporte 24/7</li>
                    <li><i class="fas fa-check"></i> Entrenamiento dedicado</li>
                </ul>
                <a href="#contacto" class="cta-button">Contactar</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <h2>¿Listo para Transformar tu Barbería?</h2>
        <p>Únete a cientos de barberías que ya confían en SKBarber</p>
        <a href="register_tenant.php" class="cta-button">Crear mi Cuenta Gratis</a>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 SKBarber by <a href="https://skale.cl" target="_blank">Skale IA</a>. Todos los derechos
            reservados.</p>
    </footer>
</body>

</html>