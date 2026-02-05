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
// Defaults
$defaults = [
    'hero_title' => 'Un Corte de Cabello para cada Ocasión',
    'hero_subtitle' => 'Somos la mejor peluquería del Mundo, te queremos bien !!\nAcá te puedes sentir muy cómodo.',
    'about_title' => 'Somos tu Peluquería Desde 1982',
    'about_text' => 'Somos una peluquería enfocada en nuestros clientes...',
    'contact_address' => 'Calle 90 N 23 24\nCali, Colombia',
    'contact_email' => 'hola@configuroweb.com',
    'contact_phone' => '+57 316 243 00 81',
    'primary_color' => '#D4AF37',
    'secondary_color' => '#111111',
    'navbar_bg_color' => '#000000',
    'footer_bg_color' => '#111111',
    'background_color' => '#ffffff',
    'text_color' => '#333333',
    'hero_image_1' => 'Design/images/barbershop_image_1.jpg',
    'hero_image_2' => 'Design/images/barbershop_image_2.jpg',
    'hero_image_3' => 'Design/images/barbershop_image_3.jpg',
    'about_image' => 'Design/images/about-1.jpg',
    'website_logo' => 'Design/images/barbershop_logo.png'
];
foreach ($defaults as $key => $val) {
    if (!isset($settings[$key]) || empty($settings[$key]))
        $settings[$key] = $val;
}

// FETCH OFFERS
$stmt = $con->prepare("SELECT * FROM offers WHERE tenant_id = ? AND active = 1 ORDER BY created_at DESC");
$stmt->execute([$tenant_id]);
$offers = $stmt->fetchAll();

// FETCH GALLERY
$stmt = $con->prepare("SELECT * FROM gallery_images WHERE tenant_id = ? ORDER BY created_at DESC");
$stmt->execute([$tenant_id]);
$gallery_images = $stmt->fetchAll();


include "Includes/templates/header.php";
?>
<style>
    :root {
        --primary-color:
            <?php echo $settings['primary_color']; ?>
        ;
        --secondary-color:
            <?php echo $settings['secondary_color']; ?>
        ;
        --navbar-bg:
            <?php echo $settings['navbar_bg_color']; ?>
        ;
        --footer-bg:
            <?php echo $settings['footer_bg_color']; ?>
        ;
        --body-bg:
            <?php echo $settings['background_color']; ?>
        ;
        --text-color:
            <?php echo $settings['text_color']; ?>
        ;
    }

    body {
        background-color: var(--body-bg) !important;
        color: var(--text-color) !important;
    }

    .navbar {
        background-color: var(--navbar-bg) !important;
    }

    .footer_widget,
    .widget_section {
        background-color: var(--footer-bg) !important;
    }

    .default_btn,
    .section_heading h3,
    .service_box i,
    code,
    .offer-badge {
        color: var(--primary-color) !important;
    }

    .default_btn {
        background-color: var(--primary-color) !important;
        color: #fff !important;
    }

    ::selection {
        background: var(--primary-color);
        color: #fff;
    }

    .offer_section {
        border-bottom: 5px solid var(--primary-color);
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        color: var(--text-color);
    }

    .service_box h3 {
        color: #333 !important;
    }

    /* Keep service titles dark/readable within box */
</style>

<!-- START NAVBAR SECTION -->
<?php include "Includes/templates/navbar.php"; ?>
<!-- END NAVBAR SECTION -->
<div class="header-height" style="height: 80px;"></div>


<!-- HOME SECTION -->

<section class="home-section" id="home-section">
    <div class="home-section-content">
        <div id="home-section-carousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <!-- FIRST SLIDE -->
                <div class="carousel-item active">
                    <img class="d-block w-100" src="<?php echo $settings['hero_image_1']; ?>" alt="First slide">
                    <div class="carousel-caption d-md-block">
                        <h3 style="color: #fff !important"><?php echo htmlspecialchars($settings['hero_title']); ?></h3>
                        <p style="color: #fff"><?php echo nl2br(htmlspecialchars($settings['hero_subtitle'])); ?></p>
                    </div>
                </div>
                <?php if ($settings['hero_image_2']): ?>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="<?php echo $settings['hero_image_2']; ?>" alt="Second slide">
                        <div class="carousel-caption d-md-block">
                            <h3 style="color: #fff !important"><?php echo htmlspecialchars($settings['hero_title']); ?></h3>
                            <p style="color: #fff"><?php echo nl2br(htmlspecialchars($settings['hero_subtitle'])); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- OFFERS SECTION -->
<?php if (count($offers) > 0): ?>
    <section class="offer_section">
        <div class="container">
            <div class="section_heading">
                <h3>No te pierdas de nada</h3>
                <h2>Ofertas Especiales</h2>
                <div class="heading-line"></div>
            </div>
            <div class="row">
                <?php foreach ($offers as $offer): ?>
                    <div class="col-md-4 mb-4">
                        <div class="offer-card">
                            <div style="position: relative;">
                                <?php if ($offer['image_path']): ?>
                                    <img src="<?php echo $offer['image_path']; ?>" class="w-100"
                                        style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div
                                        style="height: 200px; background: #ddd; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-tag fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>

                                <?php if ($offer['discount_percentage'] > 0): ?>
                                    <div class="offer-badge"
                                        style="background-color: var(--primary-color) !important; color: white !important;">
                                        -<?php echo $offer['discount_percentage']; ?>%</div>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h4 style="color: #333 !important"><?php echo htmlspecialchars($offer['title']); ?></h4>
                                <p class="text-muted"><?php echo htmlspecialchars($offer['description']); ?></p>
                                <a href="appointment.php" class="default_btn">Reservar con Oferta</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>


<!-- ABOUT SECTION -->

<section id="about" class="about_section">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="about_content" style="text-align: center;">
                    <h3>Estamos para ti</h3>
                    <h2><?php echo htmlspecialchars($settings['about_title']); ?></h2>
                    <img src="Design/images/about-logo.png" alt="logo">
                    <p>
                        <?php echo nl2br(htmlspecialchars($settings['about_text'])); ?>
                    </p>
                    <a href="#" class="default_btn" style="opacity: 1;">Más acerda de nosotros</a>
                </div>
            </div>
            <div class="col-md-6  d-none d-md-block">
                <div class="about_img" style="overflow:hidden">
                    <img class="about_img_1" src="<?php echo $settings['about_image']; ?>" alt="about-1">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES SECTION -->

<section class="services_section" id="services">
    <div class="container">
        <div class="section_heading">
            <h3>Somos tu Peluquería</h3>
            <h2>Nuestros Servicios</h2>
            <div class="heading-line"></div>
        </div>
        <div class="row">
            <?php
            // Use existing logic for services but include prices here since Pricing section is gone
            $stmt = $con->prepare("Select * from service_categories WHERE tenant_id = ?");
            $stmt->execute(array($tenant_id));
            $categories = $stmt->fetchAll();

            foreach ($categories as $category) {
                $stmt = $con->prepare("Select * from services where category_id = ? AND tenant_id = ?");
                $stmt->execute(array($category['category_id'], $tenant_id));
                $services = $stmt->fetchAll();

                foreach ($services as $service) {
                    ?>
                    <div class="col-lg-3 col-md-6 padd_col_res text-center mb-4">
                        <div class="service_box">
                            <i class="bs bs-scissors-1"></i>
                            <h3><?php echo $service['service_name']; ?></h3>
                            <p><?php echo $service['service_description']; ?></p>
                            <h4 style="color: var(--primary-color) !important">$<?php echo $service['service_price']; ?></h4>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- GALLERY SECTION (DYNAMIC) -->
<?php if (count($gallery_images) > 0): ?>
    <section class="gallery-section" id="gallery">
        <div class="section_heading">
            <h3>Nuestro Trabajo</h3>
            <h2>Galería</h2>
            <div class="heading-line"></div>
        </div>
        <div class="container">
            <div class="row">
                <?php foreach ($gallery_images as $img): ?>
                    <div class="col-lg-3 col-md-6 gallery-column">
                        <div style="height: 230px">
                            <div class="gallery-img" style="background-image: url('<?php echo $img['image_path']; ?>');"> </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>


<!-- CONTACT SECTION -->

<section class="contact-section" id="contact-us">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 sm-padding">
                <div class="contact-info">
                    <h2>
                        Si tienes alguna duda
                        <br>envianos un mensaje hoy!
                    </h2>
                    <p>
                        Estamos muy pendientes de nuestros clientes y sus consultas o sugerencias son muy importantes
                        para nosotros.
                    </p>
                    <h3>
                        <?php echo nl2br(htmlspecialchars($settings['contact_address'])); ?>
                    </h3>
                    <h4>
                        <span style="font-weight: bold">Email:</span>
                        <?php echo htmlspecialchars($settings['contact_email']); ?>
                        <br>
                        <span style="font-weight: bold">Phone:</span>
                        <?php echo htmlspecialchars($settings['contact_phone']); ?>
                        <br>
                    </h4>
                </div>
            </div>
            <div class="col-lg-6 sm-padding">
                <div class="contact-form">
                    <div id="contact_ajax_form" class="contactForm">
                        <div class="form-group colum-row row">
                            <div class="col-sm-6">
                                <input type="text" id="contact_name" name="name" class="form-control"
                                    placeholder="Tu nombre">
                            </div>
                            <div class="col-sm-6">
                                <input type="email" id="contact_email" name="email" class="form-control"
                                    placeholder="Correo">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input type="text" id="contact_subject" name="subject" class="form-control"
                                    placeholder="Asunto">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <textarea id="contact_message" name="message" cols="30" rows="5"
                                    class="form-control message" placeholder="Mensaje"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button id="contact_send" class="default_btn">Enviar tu mensaje</button>
                            </div>
                        </div>
                        <img src="Design/images/ajax_loader_gif.gif" id="contact_ajax_loader" style="display: none">
                        <div id="contact_status_message"></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($settings['contact_map_iframe']) && !empty($settings['contact_map_iframe'])): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <div class="embed-responsive embed-responsive-16by9">
                        <?php echo $settings['contact_map_iframe']; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- WIDGET SECTION / FOOTER -->

<section class="widget_section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="footer_widget">
                    <img src="<?php echo $settings['website_logo']; ?>" alt="Brand" style="max-height: 70px;">
                    <p>
                        Somos tu mejor opción, nuestro mayor capital tu comodidad y satisfacción
                    </p>
                    <ul class="widget_social">
                        <li><a href="#" data-toggle="tooltip" title="Facebook"><i
                                    class="fab fa-facebook-f fa-2x"></i></a></li>
                        <li><a href="#" data-toggle="tooltip" title="Twitter"><i class="fab fa-twitter fa-2x"></i></a>
                        </li>
                        <li><a href="#" data-toggle="tooltip" title="Instagram"><i
                                    class="fab fa-instagram fa-2x"></i></a></li>
                        <li><a href="#" data-toggle="tooltip" title="LinkedIn"><i class="fab fa-linkedin fa-2x"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer_widget">
                    <h3>Dirección</h3>
                    <p>
                        <?php echo nl2br(htmlspecialchars($settings['contact_address'])); ?>
                    </p>
                    <p>
                        <?php echo htmlspecialchars($settings['contact_email']); ?>
                        <br>
                        <?php echo htmlspecialchars($settings['contact_phone']); ?>
                    </p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer_widget">
                    <h3>
                        Horarios Disponibles
                    </h3>
                    <ul class="opening_time">
                        <li>Lunes - Viernes 11:30am - 2:00 - 8:00 pm</li>
                        <li>Lunes - Viernes 11:30am - 2:00 - 8:00 pm</li>
                        <li>Lunes - Viernes 11:30am - 2:00 - 8:00 pm</li>
                        <li>Lunes - Viernes 11:30am - 2:00 - 8:00 pm</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer_widget">
                    <h3>Subscribete a nuestro contenido</h3>
                    <div class="subscribe_form">
                        <form action="#" class="subscribe_form" novalidate="true">
                            <input type="email" name="EMAIL" id="subs-email" class="form_input"
                                placeholder="Tu correo...">
                            <button type="submit" class="submit"
                                style="background-color: var(--primary-color)">Suscríbete</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER  -->
<?php include "./Includes/templates/footer.php"; ?>