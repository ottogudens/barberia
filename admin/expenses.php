<?php
session_start();
$pageTitle = 'Finanzas - Egresos y Pagos';
include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

// HANDLE ADD EXPENSE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_expense'])) {

    $amount = test_input($_POST['amount']);
    $category = test_input($_POST['category']);
    $description = test_input($_POST['description']);
    $expense_date = test_input($_POST['expense_date']);

    if (!empty($amount) && !empty($category) && !empty($expense_date)) {
        try {
            $stmt = $con->prepare("INSERT INTO expenses (tenant_id, amount, category, description, expense_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tenant_id, $amount, $category, $description, $expense_date]);
            echo "<script>swal('Éxito', 'Gasto registrado correctamente', 'success');</script>";
        } catch (PDOException $e) {
            echo "<script>swal('Error', 'Ocurrió un error al registrar el gasto', 'error');</script>";
        }
    } else {
        echo "<script>swal('Error', 'Por favor complete todos los campos obligatorios', 'error');</script>";
    }
}

// HANDLE DELETE EXPENSE
if (isset($_GET['do']) && $_GET['do'] == 'delete' && isset($_GET['id'])) {
    $expense_id = $_GET['id'];
    $stmt = $con->prepare("DELETE FROM expenses WHERE expense_id = ? AND tenant_id = ?");
    $stmt->execute([$expense_id, $tenant_id]);
    echo "<script>window.location.href = 'expenses.php';</script>";
    exit();
}

// FILTERS
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// FETCH EXPENSES
$stmt = $con->prepare("SELECT * FROM expenses WHERE tenant_id = ? AND expense_date BETWEEN ? AND ? ORDER BY expense_date DESC");
$stmt->execute([$tenant_id, $start_date, $end_date]);
$expenses = $stmt->fetchAll();

// CALCULATE TOTAL
$total_expenses = 0;
foreach ($expenses as $exp) {
    $total_expenses += $exp['amount'];
}

?>

<!-- BEGIN PAGE CONTENT -->
<div class="container-fluid">

    <!-- PAGE HEADING -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Finanzas: Egresos y Pagos</h1>
        <div>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal"
                data-target="#addExpenseModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Gasto
            </a>
            <!-- EXPORT BUTTON (Placeholder for now, logic to be implemented) -->
            <a href="export_expenses.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
                class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Exportar Excel
            </a>
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Egresos (Periodo)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php echo number_format($total_expenses, 2); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtrar por Fecha</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="expenses.php" class="form-inline">
                <label class="mr-2">Desde:</label>
                <input type="date" name="start_date" class="form-control mr-3" value="<?php echo $start_date; ?>">
                <label class="mr-2">Hasta:</label>
                <input type="date" name="end_date" class="form-control mr-3" value="<?php echo $end_date; ?>">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>
    </div>

    <!-- EXPENSES TABLE -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Historial de Gastos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($expense['expense_date'])); ?></td>
                                <td><span
                                        class="badge badge-secondary"><?php echo htmlspecialchars($expense['category']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                <td class="text-danger font-weight-bold">
                                    -$<?php echo number_format($expense['amount'], 2); ?></td>
                                <td>
                                    <a href="expenses.php?do=delete&id=<?php echo $expense['expense_id']; ?>"
                                        class="btn btn-danger btn-sm btn-circle"
                                        onclick="return confirm('¿Está seguro de eliminar este gasto?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($expenses) == 0): ?>
                            <tr>
                                <td colspan="5" class="text-center">No se encontraron gastos en este rango de fechas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ADD EXPENSE MODAL -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Registrar Nuevo Gasto</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="expenses.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Categoría</label>
                        <select name="category" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <option value="Servicios Básicos">Servicios Básicos (Luz, Agua, Internet)</option>
                            <option value="Arriendo">Arriendo</option>
                            <option value="Sueldos">Sueldos y Comisiones</option>
                            <option value="Proveedores">Proveedores / Insumos</option>
                            <option value="Mantenimiento">Mantenimiento y Reparaciones</option>
                            <option value="Publicidad">Marketing y Publicidad</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Monto</label>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Detalles del gasto..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" type="submit" name="add_expense">Registrar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'Includes/templates/footer.php'; ?>