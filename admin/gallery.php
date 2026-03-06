<?php
ob_start();//Page Title
$pageTitle = 'Gestionar Galería';
//Includes
include 'connect.php';
include 'Includes/functions/functions.php';
include '../Includes/csrf.php';
include 'Includes/templates/header.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

include 'Includes/auth_check.php';

$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

echo '<div class="container-fluid">';

if ($do == 'Manage') {
    $stmt = $con->prepare("SELECT * FROM gallery_images WHERE tenant_id = ? ORDER BY created_at DESC");
    $stmt->execute([$tenant_id]);
    $images = $stmt->fetchAll();
    ?>
    <h1 class="h3 mb-4 text-gold font-weight-bold uppercase">Galería de Trabajos</h1>

    <!-- Upload Form -->
    <div class="card glass-card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Subir Nueva Imagen</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="gallery.php?do=Upload" enctype="multipart/form-data">
                <?php if (function_exists("csrfInput"))
                    csrfInput(); ?>
                <div class="form-row align-items-end">
                    <div class="col-auto">
                        <label class="sr-only">Imagen</label>
                        <input type="file" name="image" class="form-control mb-2" required>
                    </div>
                    <div class="col-auto">
                        <label class="sr-only">Descripción</label>
                        <input type="text" name="caption" class="form-control mb-2"
                            placeholder="Descripción breve (Opcional)">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary mb-2">Subir Imagen</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="row">
        <?php foreach ($images as $img): ?>
            <div class="col-md-3 mb-4">
                <div class="card shadow">
                    <img src="../<?php echo $img['image_path']; ?>" class="card-img-top"
                        style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <p class="card-text text-muted small"><?php echo htmlspecialchars($img['caption']); ?></p>
                        <form method="POST" action="gallery.php?do=Delete" onsubmit="return confirm('¿Eliminar imagen?');">
                            <?php if (function_exists("csrfInput"))
                                csrfInput(); ?>
                            <?php csrfInput(); ?>
                            <input type="hidden" name="id" value="<?php echo $img['image_id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm btn-block">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (count($images) == 0): ?>
            <div class="col-12 text-center text-muted">No hay imágenes en la galería.</div>
        <?php endif; ?>
    </div>

    <?php
} elseif ($do == 'Upload') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        require_once 'Includes/functions/upload_helper.php';

        $uploadResult = handleImageUpload($_FILES['image'], $tenant_id, "../img/uploads/", "img/uploads/");
        if ($uploadResult['success']) {
            $caption = test_input($_POST['caption']);
            $db_path = $uploadResult['path'];
            $stmt = $con->prepare("INSERT INTO gallery_images (tenant_id, image_path, caption) VALUES (?, ?, ?)");
            $stmt->execute([$tenant_id, $db_path, $caption]);
        } else {
            $_SESSION['error_msg'] = $uploadResult['error'];
        }
    }
    header('Location: gallery.php');
    exit();

} elseif ($do == 'Delete') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die("Error de seguridad (CSRF).");
    }
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id) {
        $stmt = $con->prepare("DELETE FROM gallery_images WHERE image_id = ? AND tenant_id = ?");
        $stmt->execute([$id, $tenant_id]);
    }
    header('Location: gallery.php');
    exit();
}

echo '</div>';
include 'Includes/templates/footer.php';
?>