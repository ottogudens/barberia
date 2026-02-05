<?php
include "connect.php";
include "Includes/tenant_context.php";

$tenant_id = getCurrentTenantId($con);

if (!$tenant_id) {
    include "saas_landing.php";
    exit();
}

// FETCH SETTINGS
$settings = [];
$stmt = $con->prepare("SELECT setting_key, setting_value FROM website_settings WHERE tenant_id = ?");
$stmt->execute([$tenant_id]);
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// FETCH SERVICES
$stmtServices = $con->prepare("SELECT * FROM services WHERE tenant_id = ? LIMIT 6");
$stmtServices->execute([$tenant_id]);
$services = $stmtServices->fetchAll();

// FETCH EMPLOYEES
$stmtEmployees = $con->prepare("SELECT * FROM employees WHERE tenant_id = ?");
$stmtEmployees->execute([$tenant_id]);
$employees = $stmtEmployees->fetchAll();

// FETCH GALLERY
$stmtGallery = $con->prepare("SELECT * FROM gallery_images WHERE tenant_id = ? LIMIT 8");
$stmtGallery->execute([$tenant_id]);
$gallery = $stmtGallery->fetchAll();

include "Includes/templates/header.php";
include "Includes/templates/navbar.php";
?>

<!-- HERO SECTION -->
<section class="hero-section" id="home-section">
    <div class="hero-overlay"></div>
    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-10">
                <h1 class="hero-title text-white mb-4">Bienvenidos a <span
                        class="text-gold"><?php echo isset($settings['shop_name']) ? $settings['shop_name'] : 'Gold Luk Barbershop'; ?></span>
                </h1>
                <p class="hero-subtitle text-white-50 mb-5">Estilo, elegancia y atención personalizada para el hombre
                    moderno.</p>
                <a href="appointment.php" class="btn btn-gold btn-lg">Reservar Cita</a>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES SECTION -->
<section class="services-section py-5" id="services">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2 class="text-uppercase">Nuestros Servicios</h2>
            <div class="divider mx-auto bg-gold"></div>
        </div>
        <div class="row">
            <?php foreach ($services as $service): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-box text-center p-4 h-100 shadow-sm">
                        <div class="service-icon mb-3">
                            <i class="fas fa-cut fa-3x text-gold"></i>
                        </div>
                        <h4 class="mb-3"><?php echo htmlspecialchars($service['service_name']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($service['service_description']); ?></p>
                        <h5 class="text-gold mt-3">$<?php echo number_format($service['service_price'], 2); ?></h5>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section class="about-section py-5 bg-light" id="about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="Design/images/about-1.jpg" alt="About Us" class="img-fluid rounded shadow highlight-img">
            </div>
            <div class="col-lg-6 pl-lg-5">
                <h2 class="text-uppercase mb-4">Sobre Nosotros</h2>
                <div class="divider bg-gold mb-4" style="width: 60px; height: 3px;"></div>
                <p class="lead text-muted mb-4">
                    Somos más que una barbería; somos un espacio dedicado al cuidado y estilo del caballero actual.
                    Combinamos técnicas tradicionales con tendencias modernas para ofrecerte la mejor experiencia.
                </p>
                <p class="text-muted mb-5">
                    Nuestro equipo de barberos expertos se asegurará de que salgas luciendo y sintiéndote impecable.
                    Utilizamos productos de la más alta calidad y nos enfocamos en cada detalle.
                </p>
                <a href="#contact-us" class="btn btn-dark">Contáctanos</a>
            </div>
        </div>
    </div>
</section>

<!-- TEAM SECTION -->
<section class="team-section py-5" id="team">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2 class="text-uppercase">Nuestro Equipo</h2>
            <div class="divider mx-auto bg-gold"></div>
        </div>
        <div class="row justify-content-center">
            <?php foreach ($employees as $employee): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="team-member text-center">
                        <div class="member-img mb-3">
                            <?php
                            $imgSrc = !empty($employee['image']) ? $employee['image'] : 'Design/images/default_employee.png';
                            ?>
                            <img src="<?php echo $imgSrc; ?>" alt="<?php echo $employee['first_name']; ?>"
                                class="img-fluid rounded-circle shadow-lg"
                                style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        <h5 class="mb-1"><?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?></h5>
                        <p class="text-gold text-uppercase small">Barbero Experto</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- GALLERY SECTION -->
<?php if (count($gallery) > 0): ?>
    <section class="gallery-section py-5 bg-dark" id="gallery">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="text-uppercase text-white">Nuestra Galería</h2>
                <div class="divider mx-auto bg-gold"></div>
            </div>
            <div class="row">
                <?php foreach ($gallery as $img): ?>
                    <div class="col-md-3 col-6 mb-4">
                        <div class="gallery-item">
                            <img src="<?php echo $img['image_path']; ?>" alt="Gallery"
                                class="img-fluid rounded border border-secondary"
                                style="height: 200px; width: 100%; object-fit: cover;">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- CONTACT SECTION -->
<section class="contact-section py-5" id="contact-us">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <h2 class="text-uppercase mb-4">Visítanos</h2>
                <div class="divider bg-gold mb-4" style="width: 40px; height: 3px;"></div>
                <ul class="list-unstyled contact-info">
                    <li class="mb-3"><i class="fas fa-map-marker-alt text-gold mr-3"></i>
                        <?php echo isset($settings['contact_address']) ? htmlspecialchars($settings['contact_address']) : '123 Calle Principal'; ?>
                    </li>
                    <li class="mb-3"><i class="fas fa-phone text-gold mr-3"></i>
                        <?php echo isset($settings['contact_phone']) ? htmlspecialchars($settings['contact_phone']) : '+1 234 567 890'; ?>
                    </li>
                    <li class="mb-3"><i class="fas fa-envelope text-gold mr-3"></i>
                        <?php echo isset($settings['contact_email']) ? htmlspecialchars($settings['contact_email']) : 'contacto@goldluk.com'; ?>
                    </li>
                    <li class="mb-3"><i class="fas fa-clock text-gold mr-3"></i> Lun - Sáb: 9:00 AM - 8:00 PM</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <!-- Map -->
                <?php
                $mapCode = isset($settings['contact_map_iframe']) && !empty($settings['contact_map_iframe']) ? $settings['contact_map_iframe'] : '<iframe src="https://www.google.com/maps/embed?pb=..." width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
                // Ensure the iframe has the correct classes/styles if needed, or just output raw
                echo $mapCode;
                ?>
            </div>
        </div>
    </div>
</section>

<?php include "./Includes/templates/footer.php"; ?>