<?php
//Page Title
$pageTitle = 'Clientes';

//Includes
include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

include 'Includes/auth_check.php';
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->

    <?php
    $do = '';
    if (isset($_GET['do']) && in_array($_GET['do'], array('Add', 'Edit', 'Delete', 'Import'))) {
        $do = htmlspecialchars($_GET['do']);
    } else {
        $do = 'Manage';
    }

    if ($do == 'Manage') {
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $per_page = 15;
        $offset = ($page - 1) * $per_page;

        $stmt_count = $con->prepare("SELECT COUNT(*) FROM clients WHERE tenant_id = ?");
        $stmt_count->execute([$tenant_id]);
        $total_rows = $stmt_count->fetchColumn();
        $total_pages = ceil($total_rows / $per_page);

        $stmt = $con->prepare("SELECT * FROM clients WHERE tenant_id = ? ORDER BY client_id DESC LIMIT ? OFFSET ?");
        // Workaround for PDO execute with LIMIT and string numbers issues in some versions: bind directly
        $stmt->bindValue(1, $tenant_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $per_page, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows_clients = $stmt->fetchAll();
        ?>
        <div class="card glass-card glass-card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Clientes</h6>
            </div>
            <div class="card-body">
                <!-- ADD NEW CLIENT BUTTON -->
                <a href="clients.php?do=Add" class="btn btn-success btn-sm" style="margin-bottom: 10px;">
                    <i class="fa fa-plus"></i> Agregar Cliente
                </a>
                <a href="clients.php?do=Import" class="btn btn-info btn-sm" style="margin-bottom: 10px;">
                    <i class="fa fa-upload"></i> Importar Clientes
                </a>
                <a href="import_clients_template.csv" class="btn btn-secondary btn-sm" style="margin-bottom: 10px;"
                    download>
                    <i class="fa fa-download"></i> Descargar Plantilla
                </a>

                <!-- Clients Table -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">ID#</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Apellido</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($rows_clients as $client) {
                                echo "<tr>";
                                echo "<td>" . $client['client_id'] . "</td>";
                                echo "<td>" . htmlspecialchars($client['first_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($client['last_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($client['phone_number']) . "</td>";
                                echo "<td>" . htmlspecialchars($client['client_email']) . "</td>";
                                echo "<td>";
                                $delete_data = "delete_client_" . $client["client_id"];
                                ?>
                                <ul class="list-inline m-0">
                                    <li class="list-inline-item" data-toggle="tooltip" title="Editar">
                                        <button class="btn btn-success btn-sm rounded-0">
                                            <a href="clients.php?do=Edit&client_id=<?php echo $client['client_id']; ?>"
                                                style="color: white;">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </button>
                                    </li>
                                    <li class="list-inline-item" data-toggle="tooltip" title="Eliminar">
                                        <button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="modal"
                                            data-target="#<?php echo $delete_data; ?>" data-placement="top"><i
                                                class="fa fa-trash"></i></button>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="<?php echo $delete_data; ?>" tabindex="-1" role="dialog"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Eliminar Cliente</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Deseas eliminar a este cliente?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cancelar</button>
                                                        <form method="POST" action="clients.php?do=Delete">
                                                            <?php if (function_exists("csrfInput"))
                                                                csrfInput(); ?>
                                                            <input type="hidden" name="client_id"
                                                                value="<?php echo $client['client_id']; ?>">
                                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <?php
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <?php if (isset($total_pages) && $total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?do=Manage&page=<?php echo $page - 1; ?>">Anterior</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?do=Manage&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?do=Manage&page=<?php echo $page + 1; ?>">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    } elseif ($do == 'Add') {
        ?>
        <div class="card glass-card glass-card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Agregar Nuevo Cliente</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="clients.php?do=Add">
                    <?php if (function_exists("csrfInput"))
                        csrfInput(); ?>
                    <div class="form-group"><label>Nombre</label><input type="text" class="form-control" name="first_name"
                            required></div>
                    <div class="form-group"><label>Apellido</label><input type="text" class="form-control" name="last_name"
                            required></div>
                    <div class="form-group"><label>Teléfono</label><input type="text" class="form-control"
                            name="phone_number" required></div>
                    <div class="form-group"><label>Correo</label><input type="email" class="form-control"
                            name="client_email" required></div>
                    <div class="form-group"><label>Contraseña</label><input type="password" class="form-control"
                            name="password" required></div>
                    <button type="submit" name="add_client_sbmt" class="btn btn-primary">Agregar Cliente</button>
                </form>
                <?php
                if (isset($_POST['add_client_sbmt'])) {
                    $fname = test_input($_POST['first_name']);
                    $lname = test_input($_POST['last_name']);
                    $phone = test_input($_POST['phone_number']);
                    $email = test_input($_POST['client_email']);
                    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

                    // Check duplicate
                    $stmtCheck = $con->prepare("SELECT * FROM clients WHERE client_email = ? AND tenant_id = ?");
                    $stmtCheck->execute([$email, $tenant_id]);
                    if ($stmtCheck->rowCount() > 0) {
                        echo "<div class='alert alert-danger'>El correo ya está registrado.</div>";
                    } else {
                        $stmt = $con->prepare("INSERT INTO clients (first_name, last_name, phone_number, client_email, password, tenant_id) VALUES (?,?,?,?,?,?)");
                        $stmt->execute([$fname, $lname, $phone, $email, $pass, $tenant_id]);
                        echo "<script>window.location.replace('clients.php');</script>";
                    }
                }
                ?>
            </div>
        </div>
        <?php
    } elseif ($do == 'Edit') {
        $client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;
        $stmt = $con->prepare("SELECT * FROM clients WHERE client_id = ? AND tenant_id = ?");
        $stmt->execute([$client_id, $tenant_id]);
        $client = $stmt->fetch();
        if ($stmt->rowCount() > 0) {
            ?>
            <div class="card glass-card glass-card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Editar Cliente</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="clients.php?do=Edit&client_id=<?php echo $client_id; ?>
                                    <?php if (function_exists("csrfInput"))
                                        csrfInput(); ?>">
                        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
                        <div class="form-group"><label>Nombre</label><input type="text" class="form-control" name="first_name"
                                value="<?php echo $client['first_name']; ?>" required></div>
                        <div class="form-group"><label>Apellido</label><input type="text" class="form-control" name="last_name"
                                value="<?php echo $client['last_name']; ?>" required></div>
                        <div class="form-group"><label>Teléfono</label><input type="text" class="form-control"
                                name="phone_number" value="<?php echo $client['phone_number']; ?>" required></div>
                        <div class="form-group"><label>Correo</label><input type="email" class="form-control"
                                name="client_email" value="<?php echo $client['client_email']; ?>" required></div>
                        <div class="form-group"><label>Nueva Contraseña (Dejar vacío para no cambiar)</label><input
                                type="password" class="form-control" name="password"></div>
                        <button type="submit" name="edit_client_sbmt" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                    <?php
                    if (isset($_POST['edit_client_sbmt'])) {
                        $fname = test_input($_POST['first_name']);
                        $lname = test_input($_POST['last_name']);
                        $phone = test_input($_POST['phone_number']);
                        $email = test_input($_POST['client_email']);
                        $id = $_POST['client_id'];

                        if (!empty($_POST['password'])) {
                            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
                            $stmt = $con->prepare("UPDATE clients SET first_name=?, last_name=?, phone_number=?, client_email=?, password=? WHERE client_id=? AND tenant_id=?");
                            $stmt->execute([$fname, $lname, $phone, $email, $pass, $id, $tenant_id]);
                        } else {
                            $stmt = $con->prepare("UPDATE clients SET first_name=?, last_name=?, phone_number=?, client_email=? WHERE client_id=? AND tenant_id=?");
                            $stmt->execute([$fname, $lname, $phone, $email, $id, $tenant_id]);
                        }
                        echo "<script>window.location.replace('clients.php');</script>";
                    }
                    ?>
                </div>
            </div>
            <?php
        }
    } elseif ($do == 'Delete') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = intval($_POST['client_id']);
            $stmt = $con->prepare("DELETE FROM clients WHERE client_id = ? AND tenant_id = ?");
            $stmt->execute([$id, $tenant_id]);
            echo "<script>window.location.replace('clients.php');</script>";
        }
    } elseif ($do == 'Import') {
        ?>
        <div class="card glass-card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Importar Clientes (CSV)</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    Sube un archivo CSV con las columnas: <b>first_name, last_name, phone_number, client_email</b>.
                    <br>Puedes descargar la <a href="import_clients_template.csv" download>plantilla aquí</a>.
                </div>
                <form method="POST" action="clients.php?do=Import" enctype="multipart/form-data">
                    <?php if (function_exists("csrfInput"))
                        csrfInput(); ?>
                    <div class="form-group">
                        <label>Seleccionar Archivo CSV</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                    </div>
                    <button type="submit" name="import_csv" class="btn btn-primary">Cargar Clientes</button>
                </form>

                <?php
                if (isset($_POST['import_csv']) && isset($_FILES['csv_file'])) {
                    $file = $_FILES['csv_file']['tmp_name'];
                    if (($handle = fopen($file, "r")) !== FALSE) {
                        $header = fgetcsv($handle, 1000, ","); // Skip header
                        $imported = 0;
                        $errors = 0;
                        $duplicate = 0;

                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            if (count($data) >= 4) {
                                $fname = test_input($data[0]);
                                $lname = test_input($data[1]);
                                $phone = test_input($data[2]);
                                $email = test_input($data[3]);
                                $pass = password_hash('123456', PASSWORD_DEFAULT); // Default password
            
                                // Check duplicate
                                $stmtCheck = $con->prepare("SELECT * FROM clients WHERE client_email = ? AND tenant_id = ?");
                                $stmtCheck->execute([$email, $tenant_id]);
                                if ($stmtCheck->rowCount() == 0) {
                                    $stmt = $con->prepare("INSERT INTO clients (first_name, last_name, phone_number, client_email, password, tenant_id) VALUES (?,?,?,?,?,?)");
                                    if ($stmt->execute([$fname, $lname, $phone, $email, $pass, $tenant_id])) {
                                        $imported++;
                                    } else {
                                        $errors++;
                                    }
                                } else {
                                    $duplicate++;
                                }
                            }
                        }
                        fclose($handle);
                        echo "<div class='alert alert-success mt-3'>Importación finalizada: $imported exitosos, $duplicate duplicados omitidos, $errors errores.</div>";
                        echo "<a href='clients.php' class='btn btn-secondary'>Volver al listado</a>";
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php

//Include Footer
include 'Includes/templates/footer.php';

?>