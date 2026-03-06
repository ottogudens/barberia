<?php
ob_start();//Page Title
$pageTitle = 'Empleado';

//Includes
include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

//Extra JS FILES
echo "<script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>";

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
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 15;
        $offset = ($page - 1) * $per_page;

        $stmt_count = $con->prepare("SELECT COUNT(*) FROM employees WHERE tenant_id = ?");
        $stmt_count->execute([$tenant_id]);
        $total_rows = $stmt_count->fetchColumn();
        $total_pages = ceil($total_rows / $per_page);

        $stmt = $con->prepare("SELECT * FROM employees WHERE tenant_id = ? ORDER BY employee_id DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $tenant_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $per_page, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows_employees = $stmt->fetchAll();

        ?>
        <div class="card glass-card glass-card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Empleado</h6>
            </div>
            <div class="card-body">

                <!-- ADD NEW Employee BUTTON -->
                <a href="employees.php?do=Add" class="btn btn-success btn-sm" style="margin-bottom: 10px;">
                    <i class="fa fa-plus"></i>
                    Agregar Empleado
                </a>
                <a href="employees.php?do=Import" class="btn btn-info btn-sm" style="margin-bottom: 10px;">
                    <i class="fa fa-upload"></i> Importar Empleados
                </a>
                <a href="import_employees_template.csv" class="btn btn-secondary btn-sm" style="margin-bottom: 10px;"
                    download>
                    <i class="fa fa-download"></i> Descargar Plantilla
                </a>

                <!-- Employees Table -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Imagen</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Apellido</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Gestionar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($rows_employees as $employee) {
                                echo "<tr>";
                                echo "<td>";
                                $image_src = "../" . ($employee['image'] ?: "Design/images/default_employee.png");
                                echo "<img src='$image_src' style='width:40px;height:40px;object-fit:cover;border-radius:50%;' alt='Profile'>";
                                echo "</td>";
                                echo "<td>";
                                echo htmlspecialchars($employee['first_name']);
                                echo "</td>";
                                echo "<td>";
                                echo htmlspecialchars($employee['last_name']);
                                echo "</td>";
                                echo "<td>";
                                echo htmlspecialchars($employee['phone_number']);
                                echo "</td>";
                                echo "<td>";
                                echo htmlspecialchars($employee['email']);
                                echo "</td>";
                                echo "<td>";
                                $delete_data = "delete_employee_" . $employee["employee_id"];
                                ?>
                                <ul class="list-inline m-0">

                                    <!-- EDIT BUTTON -->

                                    <li class="list-inline-item" data-toggle="tooltip" title="Editar">
                                        <button class="btn btn-success btn-sm rounded-0">
                                            <a href="employees.php?do=Edit&employee_id=<?php echo $employee['employee_id']; ?>"
                                                style="color: white;">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </button>
                                    </li>

                                    <!-- DELETE BUTTON -->

                                    <li class="list-inline-item" data-toggle="tooltip" title="Eliminar">
                                        <button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="modal"
                                            data-target="#<?php echo $delete_data; ?>" data-placement="top"><i
                                                class="fa fa-trash"></i></button>

                                        <!-- Delete Modal -->

                                        <div class="modal fade" id="<?php echo $delete_data; ?>" tabindex="-1" role="dialog"
                                            aria-labelledby="<?php echo $delete_data; ?>" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Eliminar Empleado</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Deseas eliminar a este empleado?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cancelar</button>
                                                        <button type="button" data-id="<?php echo $employee['employee_id']; ?>"
                                                            class="btn btn-danger delete_employee_bttn">Eliminar</button>
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
                <h6 class="m-0 font-weight-bold text-primary">Agregar Nuevo Empleado</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="employees.php?do=Add" enctype="multipart/form-data">
                    <?php if (function_exists("csrfInput"))
                        csrfInput(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_fname">Nombre</label>
                                <input type="text" class="form-control"
                                    value="<?php echo (isset($_POST['employee_fname'])) ? htmlspecialchars($_POST['employee_fname']) : '' ?>"
                                    placeholder="Nombre" name="employee_fname">
                                <?php
                                $flag_add_employee_form = 0;
                                if (isset($_POST['add_new_employee'])) {
                                    if (empty(test_input($_POST['employee_fname']))) {
                                        ?>
                                        <div class="invalid-feedback" style="display: block;">
                                            Nombre es requerido
                                        </div>
                                        <?php

                                        $flag_add_employee_form = 1;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_lname">Apellido</label>
                                <input type="text" class="form-control"
                                    value="<?php echo (isset($_POST['employee_lname'])) ? htmlspecialchars($_POST['employee_lname']) : '' ?>"
                                    placeholder="Apellido" name="employee_lname">
                                <?php
                                if (isset($_POST['add_new_employee'])) {
                                    if (empty(test_input($_POST['employee_lname']))) {
                                        ?>
                                        <div class="invalid-feedback" style="display: block;">
                                            Apellido es requerido
                                        </div>
                                        <?php

                                        $flag_add_employee_form = 1;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_phone">Teléfono</label>
                                <input type="text" class="form-control"
                                    value="<?php echo (isset($_POST['employee_phone'])) ? htmlspecialchars($_POST['employee_phone']) : '' ?>"
                                    placeholder="Teléfono" name="employee_phone">
                                <?php
                                if (isset($_POST['add_new_employee'])) {
                                    if (empty(test_input($_POST['employee_phone']))) {
                                        ?>
                                        <div class="invalid-feedback" style="display: block;">
                                            Teléfono es requerido
                                        </div>
                                        <?php

                                        $flag_add_employee_form = 1;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_email">Correo</label>
                                <input type="text" class="form-control"
                                    value="<?php echo (isset($_POST['employee_email'])) ? htmlspecialchars($_POST['employee_email']) : '' ?>"
                                    placeholder="Correo" name="employee_email">
                                <?php
                                if (isset($_POST['add_new_employee'])) {
                                    if (empty(test_input($_POST['employee_email']))) {
                                        ?>
                                        <div class="invalid-feedback" style="display: block;">
                                            Correo es requerido
                                        </div>
                                        <?php
                                        $flag_add_employee_form = 1;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_commission">Comisión (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control"
                                    value="<?php echo (isset($_POST['employee_commission'])) ? htmlspecialchars($_POST['employee_commission']) : '0' ?>"
                                    placeholder="Porcentaje de Comisión" name="employee_commission">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_image">Imagen de Perfil</label>
                                <input type="file" class="form-control" name="employee_image">
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->

                    <button type="submit" name="add_new_employee" class="btn btn-primary">Agregar Empleado</button>

                </form>

                <?php

                /*** ADD NEW EMPLOYEE ***/

                if (isset($_POST['add_new_employee']) && $_SERVER['REQUEST_METHOD'] == 'POST' && $flag_add_employee_form == 0) {
                    $employee_fname = test_input($_POST['employee_fname']);
                    $employee_lname = $_POST['employee_lname'];
                    $employee_phone = test_input($_POST['employee_phone']);
                    $employee_email = test_input($_POST['employee_email']);
                    $employee_commission = isset($_POST['employee_commission']) ? floatval($_POST['employee_commission']) : 0;

                    // Check if email already exists for this tenant
                    $stmt = $con->prepare("SELECT * FROM employees WHERE email = ? AND tenant_id = ?");
                    $stmt->execute(array($employee_email, $tenant_id));
                    $email_exist = $stmt->rowCount();

                    if ($email_exist == 0) {
                        try {
                            // Image Upload Logic
                            // Image Upload Logic
                            $image_path = "Design/images/default_employee.png";
                            require_once 'Includes/functions/upload_helper.php';

                            if (isset($_FILES['employee_image']) && $_FILES['employee_image']['error'] == 0) {
                                $uploadResult = handleImageUpload($_FILES['employee_image'], $tenant_id, "../Uploads/employees/", "Uploads/employees/");
                                if ($uploadResult['success']) {
                                    $image_path = $uploadResult['path'];
                                } else {
                                    // On error, let the user know, but proceed with default image if we want
                                    echo "<div class='alert alert-warning'>{$uploadResult['error']} Se usará la imagen por defecto.</div>";
                                }
                            }

                            $stmt = $con->prepare("insert into employees(first_name,last_name,phone_number,email, tenant_id, commission_percentage, image) values(?,?,?,?,?,?,?) ");
                            $stmt->execute(array($employee_fname, $employee_lname, $employee_phone, $employee_email, $tenant_id, $employee_commission, $image_path));

                            ?>
                            <!-- SUCCESS MESSAGE -->

                            <script type="text/javascript">
                                Swal.fire("Nuevo Empleado", "El/La nuev@ emplead@ se ha insertado con éxito.", "success").then((val                             ue) => {
                                    window.location.replace("employees.php");
                                });
                            </script>

                            <?php

                        } catch (Exception $e) {
                            echo "<div class = 'alert alert-danger' style='margin:10px 0px;'>";
                            echo 'Ocurrió un error: ' . $e->getMessage();
                            echo "</div>";
                        }
                    } else {
                        echo "<div class = 'alert alert-danger' style='margin:10px 0px;'>";
                        echo 'El correo electrónico ya existe para otro empleado.';
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </div>
        <?php
    } elseif ($do == 'Edit') {
        $employee_id = (isset($_GET['employee_id']) && is_numeric($_GET['employee_id'])) ? intval($_GET['employee_id']) : 0;

        if ($employee_id) {
            $stmt = $con->prepare("Select * from employees where employee_id = ? AND tenant_id = ?");
            $stmt->execute(array($employee_id, $tenant_id));
            $employee = $stmt->fetch();
            $count = $stmt->rowCount();

            if ($count > 0) {
                ?>
                <div class="card glass-card glass-card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Editar Empleado</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="employees.php?do=Edit&employee_id=<?php echo $employee_id; ?>
                                    <?php if (function_exists("csrfInput"))
                                        csrfInput(); ?>" enctype="multipart/form-data">
                            <!-- Employee ID -->
                            <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_fname">Nombre</label>
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($employee['first_name']) ?>" placeholder="Nombre"
                                            name="employee_fname">
                                        <?php
                                        $flag_edit_employee_form = 0;
                                        if (isset($_POST['edit_employee_sbmt'])) {
                                            if (empty(test_input($_POST['employee_fname']))) {
                                                ?>
                                                <div class="invalid-feedback" style="display: block;">
                                                    Nombre es requerido
                                                </div>
                                                <?php

                                                $flag_edit_employee_form = 1;
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_lname">Apellido</label>
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($employee['last_name']) ?>" placeholder="Apellido"
                                            name="employee_lname">
                                        <?php
                                        if (isset($_POST['edit_employee_sbmt'])) {
                                            if (empty(test_input($_POST['employee_lname']))) {
                                                ?>
                                                <div class="invalid-feedback" style="display: block;">
                                                    Apellido es requerido
                                                </div>
                                                <?php

                                                $flag_edit_employee_form = 1;
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_phone">Teléfono</label>
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($employee['phone_number']) ?>" placeholder="Teléfono"
                                            name="employee_phone">
                                        <?php
                                        if (isset($_POST['edit_employee_sbmt'])) {
                                            if (empty(test_input($_POST['employee_phone']))) {
                                                ?>
                                                <div class="invalid-feedback" style="display: block;">
                                                    Teléfono es requerido
                                                </div>
                                                <?php

                                                $flag_edit_employee_form = 1;
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_email">Correo</label>
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($employee['email']) ?>" placeholder="Correo"
                                            name="employee_email">
                                        <?php
                                        if (isset($_POST['edit_employee_sbmt'])) {
                                            if (empty(test_input($_POST['employee_email']))) {
                                                ?>
                                                <div class="invalid-feedback" style="display: block;">
                                                    Correo es requerido
                                                </div>
                                                <?php
                                                $flag_edit_employee_form = 1;
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_commission">Comisión (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control"
                                            value="<?php echo htmlspecialchars($employee['commission_percentage']); ?>"
                                            placeholder="Porcentaje de Comisión" name="employee_commission">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_image">Imagen de Perfil</label>
                                        <br>
                                        <?php
                                        // Display current image if exists (fix path relative to admin/)
                                        $img_url_src = "../" . $employee['image'];
                                        // If using default, make sure we point to correct location
                                        if (empty($employee['image'])) {
                                            $img_url_src = "../Design/images/default_employee.png";
                                        }
                                        ?>
                                        <img src="<?php echo $img_url_src; ?>" alt="Employee Image"
                                            style="width:100px; height:100px; object-fit:cover; margin-bottom:10px;">
                                        <input type="file" class="form-control" name="employee_image">
                                        <small class="text-muted">Dejar vacío para mantener la imagen actual.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- SUBMIT BUTTON -->
                            <button type="submit" name="edit_employee_sbmt" class="btn btn-primary">
                                Editar empleado
                            </button>
                        </form>
                        <?php
                        /*** EDIT EMPLOYEE ***/
                        if (isset($_POST['edit_employee_sbmt']) && $_SERVER['REQUEST_METHOD'] == 'POST' && $flag_edit_employee_form == 0) {
                            $employee_fname = test_input($_POST['employee_fname']);
                            $employee_lname = $_POST['employee_lname'];
                            $employee_phone = test_input($_POST['employee_phone']);
                            $employee_email = test_input($_POST['employee_email']);
                            $employee_commission = isset($_POST['employee_commission']) ? floatval($_POST['employee_commission']) : 0;
                            $employee_id = $_POST['employee_id'];


                            try {
                                // Image Update Logic
                                // Image Update Logic
                                $image_path = $employee['image']; // Default to existing
                                require_once 'Includes/functions/upload_helper.php';

                                if (isset($_FILES['employee_image']) && $_FILES['employee_image']['error'] == 0) {
                                    $uploadResult = handleImageUpload($_FILES['employee_image'], $tenant_id, "../Uploads/employees/", "Uploads/employees/");
                                    if ($uploadResult['success']) {
                                        $image_path = $uploadResult['path'];
                                    } else {
                                        echo "<div class='alert alert-warning'>{$uploadResult['error']} Se mantendrá la imagen actual.</div>";
                                    }
                                }

                                $stmt = $con->prepare("UPDATE employees set first_name = ?, last_name = ?, phone_number = ?, email = ?, commission_percentage = ?, image = ? WHERE employee_id = ? AND tenant_id = ?");
                                $stmt->execute(array($employee_fname, $employee_lname, $employee_phone, $employee_email, $employee_commission, $image_path, $employee_id, $tenant_id));

                                ?>
                                <!-- SUCCESS MESSAGE -->

                                <script type="text/javascript">
                                    Swal.fire("Emplead@ Actualizado", "El/La emplead@ ha sido actualizada con éxito", "success").then((value) => {
                                        window.location.replace("employees.php");
                                    });
                                </script>

                                <?php

                            } catch (Exception $e) {
                                echo "<div class = 'alert alert-danger' style='margin:10px 0px;'>";
                                echo 'Ocurrió un error: ' . $e->getMessage();
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
            } else {
                header('Location: employees.php');
                exit();
            }
        } else {
            header('Location: employees.php');
            exit();
        }
    } elseif ($do == 'Import') {
        ?>
        <div class="card glass-card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Importar Empleados (CSV)</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    Sube un archivo CSV con las columnas: <b>first_name, last_name, phone_number, email,
                        commission_percentage</b>.
                    <br>Puedes descargar la <a href="import_employees_template.csv" download>plantilla aquí</a>.
                </div>
                <form method="POST" action="employees.php?do=Import" enctype="multipart/form-data">
                    <?php if (function_exists("csrfInput"))
                        csrfInput(); ?>
                    <div class="form-group">
                        <label>Seleccionar Archivo CSV</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                    </div>
                    <button type="submit" name="import_csv" class="btn btn-primary">Cargar Empleados</button>
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
                                $comm = isset($data[4]) ? floatval($data[4]) : 0;
                                $image = "Design/images/default_employee.png";

                                // Check duplicate
                                $stmtCheck = $con->prepare("SELECT * FROM employees WHERE email = ? AND tenant_id = ?");
                                $stmtCheck->execute([$email, $tenant_id]);
                                if ($stmtCheck->rowCount() == 0) {
                                    $stmt = $con->prepare("INSERT INTO employees (first_name, last_name, phone_number, email, commission_percentage, image, tenant_id) VALUES (?,?,?,?,?,?,?)");
                                    if ($stmt->execute([$fname, $lname, $phone, $email, $comm, $image, $tenant_id])) {
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
                        echo "<div class='alert alert-success mt-3'>Importación finalizada: $imported exitosos, $duplicate duplicados omitidos.</div>";
                        echo "<a href='employees.php' class='btn btn-secondary'>Volver al listado</a>";
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