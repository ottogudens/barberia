<?php
ob_start();
session_start();
//Page Title
$pageTitle = 'Gestionar Galería';
//Includes
include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

//Check If user is already logged in
if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    echo '<div class="container-fluid">';

    if ($do == 'Manage') {
        $stmt = $con->prepare("SELECT * FROM gallery_images WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$tenant_id]);
        $images = $stmt->fetchAll();
        ?>
        <h1 class="h3 mb-4 text-gray-800">Galería de Trabajos</h1>

        <!-- Upload Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Subir Nueva Imagen</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="gallery.php?do=Upload" enctype="multipart/form-data">
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
                            <a href="gallery.php?do=Delete&id=<?php echo $img['image_id']; ?>"
                                class="btn btn-danger btn-sm btn-block" onclick="return confirm('¿Eliminar imagen?')">Eliminar</a>
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
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $caption = test_input($_POST['caption']);
                $upload_dir = "../img/uploads/";
                if (!file_exists($upload_dir))
                    mkdir($upload_dir, 0777, true);

                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $newFileName = md5(time() . uniqid() . $tenant_id) . '.' . $fileExtension;
                $dest_path = $upload_dir . $newFileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest_path)) {
                    $db_path = "img/uploads/" . $newFileName;
                    $stmt = $con->prepare("INSERT INTO gallery_images (tenant_id, image_path, caption) VALUES (?, ?, ?)");
                    $stmt->execute([$tenant_id, $db_path, $caption]);
                }
            }
        }
        header('Location: gallery.php');
        exit();

    } elseif ($do == 'Delete') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id) {
            $stmt = $con->prepare("DELETE FROM gallery_images WHERE image_id = ? AND tenant_id = ?");
            $stmt->execute([$id, $tenant_id]);
        }
        header('Location: gallery.php');
        exit();
    }

    echo '</div>';
    include 'Includes/templates/footer.php';

} else {
    header('Location: login.php');
    exit();
}
?>