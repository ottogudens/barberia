<?php
ob_start();
session_start();
//Page Title
$pageTitle = 'Gestionar Ofertas';
//Includes
include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

//Check If user is already logged in
if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {

    $do = '';
    if (isset($_GET['do']) && in_array($_GET['do'], array('Add', 'Edit', 'Delete'))) {
        $do = $_GET['do'];
    } else {
        $do = 'Manage';
    }

    echo '<div class="container-fluid">';

    if ($do == 'Manage') {
        $stmt = $con->prepare("SELECT * FROM offers WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$tenant_id]);
        $offers = $stmt->fetchAll();
        ?>
        <h1 class="h3 mb-4 text-gray-800">Ofertas Especiales</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <a href="offers.php?do=Add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Nueva Oferta</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Título</th>
                                <th>Descuento</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($offers as $offer): ?>
                                <tr>
                                    <td>
                                        <?php if ($offer['image_path']): ?>
                                            <img src="../<?php echo $offer['image_path']; ?>" style="height: 50px;">
                                        <?php else: ?>
                                            No img
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($offer['title']); ?></td>
                                    <td><?php echo $offer['discount_percentage']; ?>%</td>
                                    <td><?php echo $offer['active'] ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-secondary">Inactivo</span>'; ?>
                                    </td>
                                    <td>
                                        <a href="offers.php?do=Edit&id=<?php echo $offer['offer_id']; ?>"
                                            class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                        <a href="offers.php?do=Delete&id=<?php echo $offer['offer_id']; ?>"
                                            class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta oferta?')"><i
                                                class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    } elseif ($do == 'Add') {
        ?>
        <h1 class="h3 mb-4 text-gray-800">Nueva Oferta</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="POST" action="offers.php?do=Add" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Porcentaje Descuento (%)</label>
                                <input type="number" name="discount" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Imagen</label>
                                <input type="file" name="image" class="form-control-file">
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="add_offer" class="btn btn-success">Crear Oferta</button>
                    <a href="offers.php" class="btn btn-secondary">Cancelar</a>
                </form>
                <?php
                if (isset($_POST['add_offer'])) {
                    $title = test_input($_POST['title']);
                    $desc = test_input($_POST['description']);
                    $discount = intval($_POST['discount']);

                    $image_path_db = null;

                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = "../img/uploads/";
                        if (!file_exists($upload_dir))
                            mkdir($upload_dir, 0777, true);

                        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                        $newFileName = md5(time() . $title . $tenant_id) . '.' . $fileExtension;
                        $dest_path = $upload_dir . $newFileName;

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest_path)) {
                            $image_path_db = "img/uploads/" . $newFileName;
                        }
                    }

                    $stmt = $con->prepare("INSERT INTO offers (tenant_id, title, description, image_path, discount_percentage) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$tenant_id, $title, $desc, $image_path_db, $discount]);
                    header('Location: offers.php');
                    exit();
                }
                ?>
            </div>
        </div>
        <?php
    } elseif ($do == 'Delete') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id) {
            $stmt = $con->prepare("DELETE FROM offers WHERE offer_id = ? AND tenant_id = ?");
            $stmt->execute([$id, $tenant_id]);
        }
        header('Location: offers.php');
        exit();
    }

    echo '</div>';
    include 'Includes/templates/footer.php';

} else {
    header('Location: login.php');
    exit();
}
?>