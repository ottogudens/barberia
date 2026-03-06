<?php
ob_start();//Page Title
$pageTitle = 'Configuración Web';
//Includes
include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

include 'Includes/auth_check.php';

// Helper to get setting
$settings = [];
$stmt = $con->prepare("SELECT setting_key, setting_value FROM website_settings WHERE tenant_id = ?");
$stmt->execute([$tenant_id]);
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Default values if missing
$defaults = [
    'hero_title' => 'Un Corte de Cabello para cada Ocasión',
    'hero_subtitle' => 'Somos la mejor peluquería del Mundo...',
    'about_title' => 'Somos tu Peluquería Desde 1982',
    'about_text' => 'Somos una peluquería enfocada en nuestros clientes...',
    'contact_address' => 'Dirección predeterminada',
    'contact_email' => 'info@barberia.com',
    'contact_phone' => '+123 456 7890',
    'primary_color' => '#D4AF37'
];

foreach ($defaults as $key => $val) {
    if (!isset($settings[$key]))
        $settings[$key] = $val;
}

?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gold font-weight-bold uppercase">Gestionar Sitio Web</h1>

    <div class="card glass-card glass-card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Personalización General</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="website_settings.php" enctype="multipart/form-data">
                <?php if (function_exists("csrfInput"))
                    csrfInput(); ?>

                <hr>

                <h4 class="mb-3">Información General</h4>
                <div class="form-group">
                    <label>Logo del Sitio</label>
                    <input type="file" class="form-control-file" name="website_logo">
                    <small class="text-muted">Dimensiones recomendadas: 200x60 px (PNG Transparente)</small>
                    <?php if (isset($settings['website_logo'])): ?>
                        <img src="../<?php echo $settings['website_logo']; ?>" class="img-thumbnail mt-2"
                            style="max-height: 100px;">
                    <?php endif; ?>
                </div>

                <h4 class="mb-3">Sección Hero (Inicio)</h4>
                <div class="form-group">
                    <label>Título Principal</label>
                    <input type="text" class="form-control" name="hero_title"
                        value="<?php echo htmlspecialchars($settings['hero_title']); ?>">
                </div>
                <div class="form-group">
                    <label>Subtítulo</label>
                    <textarea class="form-control" name="hero_subtitle"
                        rows="3"><?php echo htmlspecialchars($settings['hero_subtitle']); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label>Imagen Slide 1</label>
                        <input type="file" class="form-control-file" name="hero_image_1">
                        <small class="text-muted">Recomendado: 1920x1080 px (JPG)</small>
                        <?php if (isset($settings['hero_image_1'])): ?>
                            <img src="../<?php echo $settings['hero_image_1']; ?>" class="img-thumbnail mt-2"
                                style="max-height: 100px;">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label>Imagen Slide 2</label>
                        <input type="file" class="form-control-file" name="hero_image_2">
                        <small class="text-muted">Recomendado: 1920x1080 px (JPG)</small>
                        <?php if (isset($settings['hero_image_2'])): ?>
                            <img src="../<?php echo $settings['hero_image_2']; ?>" class="img-thumbnail mt-2"
                                style="max-height: 100px;">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label>Imagen Slide 3</label>
                        <input type="file" class="form-control-file" name="hero_image_3">
                        <small class="text-muted">Recomendado: 1920x1080 px (JPG)</small>
                        <?php if (isset($settings['hero_image_3'])): ?>
                            <img src="../<?php echo $settings['hero_image_3']; ?>" class="img-thumbnail mt-2"
                                style="max-height: 100px;">
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <h4 class="mb-3">Sección Nosotros</h4>
                <div class="form-group">
                    <label>Título Nosotros</label>
                    <input type="text" class="form-control" name="about_title"
                        value="<?php echo htmlspecialchars($settings['about_title']); ?>">
                </div>
                <div class="form-group">
                    <label>Texto Nosotros</label>
                    <textarea class="form-control" name="about_text"
                        rows="4"><?php echo htmlspecialchars($settings['about_text']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Imagen Nosotros</label>
                    <input type="file" class="form-control-file" name="about_image">
                    <small class="text-muted">Recomendado: 800x600 px o 600x600 px</small>
                    <?php if (isset($settings['about_image'])): ?>
                        <img src="../<?php echo $settings['about_image']; ?>" class="img-thumbnail mt-2"
                            style="max-height: 100px;">
                    <?php endif; ?>
                </div>

                <hr>

                <h4 class="mb-3">Contacto</h4>
                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" class="form-control" name="contact_address"
                        value="<?php echo htmlspecialchars($settings['contact_address']); ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="contact_email"
                        value="<?php echo htmlspecialchars($settings['contact_email']); ?>">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" class="form-control" name="contact_phone"
                        value="<?php echo htmlspecialchars($settings['contact_phone']); ?>">
                </div>
                <div class="form-group">
                    <label>Mapa de Ubicación (Google Maps Embed Code)</label>
                    <textarea class="form-control" name="contact_map_iframe" rows="3"
                        placeholder='<iframe src="https://www.google.com/maps/embed...'> <?php echo htmlspecialchars($settings['contact_map_iframe'] ?? ''); ?></textarea>
                    <small class="form-text text-muted">Pega aquí el código "Insertar mapa" de Google Maps.</small>
                </div>

                <hr>
                <h4 class="mb-3">Estilo</h4>
                <hr>
                <h4 class="mb-3">Estilo General</h4>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Color Primario (Botones, Títulos)</label>
                            <input type="color" class="form-control" name="primary_color"
                                value="<?php echo htmlspecialchars($settings['primary_color']); ?>"
                                style="height: 50px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Color Secundario (Fondos alternativos)</label>
                            <input type="color" class="form-control" name="secondary_color"
                                value="<?php echo htmlspecialchars($settings['secondary_color'] ?? '#111111'); ?>"
                                style="height: 50px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Color Menú (Navbar)</label>
                            <input type="color" class="form-control" name="navbar_bg_color"
                                value="<?php echo htmlspecialchars($settings['navbar_bg_color'] ?? '#000000'); ?>"
                                style="height: 50px;">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Color Pie de Página (Footer)</label>
                            <input type="color" class="form-control" name="footer_bg_color"
                                value="<?php echo htmlspecialchars($settings['footer_bg_color'] ?? '#111111'); ?>"
                                style="height: 50px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Color de Fondo (Body)</label>
                            <input type="color" class="form-control" name="background_color"
                                value="<?php echo htmlspecialchars($settings['background_color'] ?? '#ffffff'); ?>"
                                style="height: 50px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Color de Texto</label>
                            <input type="color" class="form-control" name="text_color"
                                value="<?php echo htmlspecialchars($settings['text_color'] ?? '#333333'); ?>"
                                style="height: 50px;">
                        </div>
                    </div>
                </div>

                <button type="submit" name="save_settings" class="btn btn-primary btn-lg mt-3">Guardar Cambios</button>
            </form>

            <?php
            if (isset($_POST['save_settings'])) {
                // Save function
                $keys = ['hero_title', 'hero_subtitle', 'about_title', 'about_text', 'contact_address', 'contact_email', 'contact_phone', 'primary_color', 'contact_map_iframe', 'secondary_color', 'navbar_bg_color', 'footer_bg_color', 'background_color', 'text_color'];

                try {
                    $con->beginTransaction();

                    foreach ($keys as $key) {
                        $val = isset($_POST[$key]) ? $_POST[$key] : '';
                        upsertSetting($con, $tenant_id, $key, $val);
                    }

                    // Handle File Uploads
                    $file_keys = ['hero_image_1', 'hero_image_2', 'hero_image_3', 'about_image', 'website_logo'];
                    require_once 'Includes/functions/upload_helper.php';

                    foreach ($file_keys as $fkey) {
                        if (isset($_FILES[$fkey]) && $_FILES[$fkey]['error'] === UPLOAD_ERR_OK) {
                            $uploadResult = handleImageUpload($_FILES[$fkey], $tenant_id, "../img/uploads/", "img/uploads/");
                            if ($uploadResult['success']) {
                                $db_path = $uploadResult['path'];

                                // Update DB
                                upsertSetting($con, $tenant_id, $fkey, $db_path);
                            } else {
                                throw new Exception("Error subiendo $fkey: " . $uploadResult['error']);
                            }
                        }
                    }

                    $con->commit();
                    echo "<script>Swal.fire('Éxito', 'La configuración ha sido guardada', 'success').then((value) => { window.location.replace('website_settings.php'); });</script>";
                } catch (Exception $e) {
                    $con->rollBack();
                    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
            }
            ?>
        </div>
    </div>

</div>
<!-- End of Main Content -->

<?php
include 'Includes/templates/footer.php';
?>