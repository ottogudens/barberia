<?php
ob_start();
session_start();
//Page Title
$pageTitle = 'Finanzas y Pagos';
//Includes
include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

//Check If user is already logged in
if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {

    // ACTION HANDLERS
    if (isset($_POST['mark_paid'])) {
        $appt_id = intval($_POST['appointment_id']);
        $total_amount = floatval($_POST['total_amount']);
        $payment_method = $_POST['payment_method'];

        $stmt = $con->prepare("UPDATE appointments SET is_paid = 1, total_amount = ?, payment_method = ?, paid_at = NOW() WHERE appointment_id = ? AND tenant_id = ?");
        $stmt->execute([$total_amount, $payment_method, $appt_id, $tenant_id]);
        header("Location: payments.php?tab=services");
        exit();
    }

    if (isset($_POST['record_payout'])) {
        $emp_id = intval($_POST['employee_id']);
        $amount = floatval($_POST['amount']);
        $notes = $_POST['notes'];

        $stmt = $con->prepare("INSERT INTO employee_payouts (tenant_id, employee_id, amount, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$tenant_id, $emp_id, $amount, $notes]);
        header("Location: payments.php?tab=payroll");
        exit();
    }

    $activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'services';
    ?>

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Módulo Financiero</h1>
        </div>

        <!-- TABS -->
        <div class="card shadow mb-4">
            <div class="card-header border-bottom-0 p-0">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activeTab == 'services') ? 'active' : ''; ?>"
                            href="payments.php?tab=services">Cobros de Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activeTab == 'payroll') ? 'active' : ''; ?>"
                            href="payments.php?tab=payroll">Nómina y Comisiones</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">

                <!-- SERVICES PENDING PAYMENT -->
                <?php if ($activeTab == 'services'):
                    // Date Filtering
                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
                    ?>

                    <!-- Date Filter Form -->
                    <form method="GET" class="form-inline mb-4">
                        <input type="hidden" name="tab" value="services">
                        <label class="mr-2">Desde:</label>
                        <input type="date" name="start_date" class="form-control mr-3" value="<?php echo $start_date; ?>">
                        <label class="mr-2">Hasta:</label>
                        <input type="date" name="end_date" class="form-control mr-3" value="<?php echo $end_date; ?>">
                        <button type="submit" class="btn btn-primary">Filtrar Ganancias</button>
                        <a href="export_payments.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
                            class="btn btn-success ml-2">
                            <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                        </a>
                    </form>

                    <div class="row">
                        <!-- PENDING PAYMENTS -->
                        <div class="col-md-12 mb-5">
                            <h5 class="text-warning font-weight-bold">Citas Pendientes de Cobro</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Fecha/Hora</th>
                                            <th>Cliente</th>
                                            <th>Empleado</th>
                                            <th>Servicios</th>
                                            <th>Total Estimado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $con->prepare("SELECT a.*, c.first_name as c_name, c.last_name as c_lname, e.first_name as e_name, e.last_name as e_lname 
                                                        FROM appointments a 
                                                        JOIN clients c ON a.client_id = c.client_id 
                                                        JOIN employees e ON a.employee_id = e.employee_id 
                                                        WHERE a.tenant_id = ? AND a.canceled = 0 AND a.is_paid = 0 
                                                        ORDER BY a.start_time DESC");
                                        $stmt->execute([$tenant_id]);
                                        $unpaid_appts = $stmt->fetchAll();

                                        foreach ($unpaid_appts as $appt):
                                            $stmtPrice = $con->prepare("SELECT SUM(s.service_price) as total FROM services s JOIN services_booked sb ON s.service_id = sb.service_id WHERE sb.appointment_id = ?");
                                            $stmtPrice->execute([$appt['appointment_id']]);
                                            $price = $stmtPrice->fetchColumn() ?: 0;
                                            ?>
                                            <tr>
                                                <td><?php echo $appt['start_time']; ?></td>
                                                <td><?php echo $appt['c_name'] . ' ' . $appt['c_lname']; ?></td>
                                                <td><?php echo $appt['e_name'] . ' ' . $appt['e_lname']; ?></td>
                                                <td>
                                                    <?php
                                                    $stmtSru = $con->prepare("SELECT s.service_name FROM services s JOIN services_booked sb ON s.service_id = sb.service_id WHERE sb.appointment_id = ?");
                                                    $stmtSru->execute([$appt['appointment_id']]);
                                                    $services = $stmtSru->fetchAll(PDO::FETCH_COLUMN);
                                                    echo implode(", ", $services);
                                                    ?>
                                                </td>
                                                <td>$<?php echo number_format($price, 2); ?></td>
                                                <td>
                                                    <button class="btn btn-success btn-sm" data-toggle="modal"
                                                        data-target="#payModal<?php echo $appt['appointment_id']; ?>">
                                                        <i class="fas fa-dollar-sign"></i> Cobrar
                                                    </button>

                                                    <!-- Pay Modal -->
                                                    <div class="modal fade" id="payModal<?php echo $appt['appointment_id']; ?>"
                                                        tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form method="POST" action="payments.php">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Registrar Cobro</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="appointment_id"
                                                                            value="<?php echo $appt['appointment_id']; ?>">
                                                                        <div class="form-group">
                                                                            <label>Monto Total</label>
                                                                            <input type="number" step="0.01" name="total_amount"
                                                                                class="form-control" value="<?php echo $price; ?>"
                                                                                required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Método de Pago</label>
                                                                            <select name="payment_method" class="form-control">
                                                                                <option value="Efectivo">Efectivo</option>
                                                                                <option value="Tarjeta">Tarjeta</option>
                                                                                <option value="Transferencia">Transferencia</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Cancelar</button>
                                                                        <button type="submit" name="mark_paid"
                                                                            class="btn btn-primary">Confirmar Pago</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- PAID HISTORY (EARNINGS) -->
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-success font-weight-bold">Historial de Ganancias (Pagados)</h5>
                                <?php
                                // Calc Total for Period
                                $stmtTotal = $con->prepare("SELECT SUM(total_amount) FROM appointments WHERE tenant_id = ? AND is_paid = 1 AND DATE(paid_at) BETWEEN ? AND ?");
                                $stmtTotal->execute([$tenant_id, $start_date, $end_date]);
                                $total_period = $stmtTotal->fetchColumn() ?: 0;
                                ?>
                                <h4 class="font-weight-bold text-success">Total: $<?php echo number_format($total_period, 2); ?>
                                </h4>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha Pago</th>
                                            <th>Cliente</th>
                                            <th>Empleado</th>
                                            <th>Método</th>
                                            <th>Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmtPaid = $con->prepare("SELECT a.*, c.first_name as c_name, c.last_name as c_lname, e.first_name as e_name, e.last_name as e_lname 
                                                        FROM appointments a 
                                                        JOIN clients c ON a.client_id = c.client_id 
                                                        JOIN employees e ON a.employee_id = e.employee_id 
                                                        WHERE a.tenant_id = ? AND a.is_paid = 1 AND DATE(a.paid_at) BETWEEN ? AND ?
                                                        ORDER BY a.paid_at DESC");
                                        $stmtPaid->execute([$tenant_id, $start_date, $end_date]);
                                        $paid_appts = $stmtPaid->fetchAll();

                                        foreach ($paid_appts as $appt):
                                            ?>
                                            <tr>
                                                <td><?php echo $appt['paid_at']; ?></td>
                                                <td><?php echo $appt['c_name'] . ' ' . $appt['c_lname']; ?></td>
                                                <td><?php echo $appt['e_name'] . ' ' . $appt['e_lname']; ?></td>
                                                <td><?php echo $appt['payment_method']; ?></td>
                                                <td>$<?php echo number_format($appt['total_amount'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- PAYROLL & COMMISSIONS -->
                <?php if ($activeTab == 'payroll'):
                    $filter_emp = isset($_GET['employee_id']) ? $_GET['employee_id'] : '';
                    ?>
                    <!-- Employee Filter -->
                    <form method="GET" class="form-inline mb-4">
                        <input type="hidden" name="tab" value="payroll">
                        <label class="mr-2">Filtrar por Empleado:</label>
                        <select name="employee_id" class="form-control mr-3">
                            <option value="">-- Todos --</option>
                            <?php
                            $stmtAllEmp = $con->prepare("SELECT * FROM employees WHERE tenant_id = ?");
                            $stmtAllEmp->execute([$tenant_id]);
                            while ($e = $stmtAllEmp->fetch()) {
                                $selected = ($filter_emp == $e['employee_id']) ? 'selected' : '';
                                echo "<option value='" . $e['employee_id'] . "' $selected>" . $e['first_name'] . " " . $e['last_name'] . "</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-info">Ver Comisiones</button>
                    </form>

                    <h5 class="mb-3">Balance de Empleados</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Comisión %</th>
                                    <th>Total Vendido (Pagado)</th>
                                    <th>Comisión Ganada</th>
                                    <th>Pagos Recibidos</th>
                                    <th>Balance Pendiente</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM employees WHERE tenant_id = ?";
                                $params = [$tenant_id];
                                if ($filter_emp) {
                                    $sql .= " AND employee_id = ?";
                                    $params[] = $filter_emp;
                                }

                                $stmtEmp = $con->prepare($sql);
                                $stmtEmp->execute($params);
                                $employees = $stmtEmp->fetchAll();

                                foreach ($employees as $emp):
                                    // 1. Calculate Total Sales (Paid Appointments Only)
                                    $stmtSales = $con->prepare("SELECT SUM(total_amount) FROM appointments WHERE employee_id = ? AND is_paid = 1 AND canceled = 0");
                                    $stmtSales->execute([$emp['employee_id']]);
                                    $total_sales = $stmtSales->fetchColumn() ?: 0;

                                    // 2. Calculate Commission Earned
                                    $commission_rate = $emp['commission_percentage'] / 100;
                                    $commission_earned = $total_sales * $commission_rate;

                                    // 3. Calculate Payouts Already Made
                                    $stmtPayouts = $con->prepare("SELECT SUM(amount) FROM employee_payouts WHERE employee_id = ?");
                                    $stmtPayouts->execute([$emp['employee_id']]);
                                    $total_payouts = $stmtPayouts->fetchColumn() ?: 0;

                                    // 4. Balance
                                    $balance = $commission_earned - $total_payouts;
                                    ?>
                                    <tr>
                                        <td><?php echo $emp['first_name'] . ' ' . $emp['last_name']; ?></td>
                                        <td><?php echo $emp['commission_percentage']; ?>%</td>
                                        <td>$<?php echo number_format($total_sales, 2); ?></td>
                                        <td>$<?php echo number_format($commission_earned, 2); ?></td>
                                        <td>$<?php echo number_format($total_payouts, 2); ?></td>
                                        <td class="<?php echo ($balance > 0) ? 'text-danger font-weight-bold' : 'text-success'; ?>">
                                            $<?php echo number_format($balance, 2); ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#payoutModal<?php echo $emp['employee_id']; ?>">
                                                <i class="fas fa-hand-holding-usd"></i> Pagar
                                            </button>

                                            <!-- Payout Modal -->
                                            <div class="modal fade" id="payoutModal<?php echo $emp['employee_id']; ?>"
                                                tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="payments.php">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Registrar Pago a
                                                                    <?php echo $emp['first_name']; ?>
                                                                </h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="employee_id"
                                                                    value="<?php echo $emp['employee_id']; ?>">
                                                                <div class="form-group">
                                                                    <label>Monto a Pagar</label>
                                                                    <input type="number" step="0.01" name="amount"
                                                                        class="form-control" value="<?php echo max(0, $balance); ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Notas</label>
                                                                    <textarea name="notes" class="form-control"
                                                                        placeholder="Ej. Pago de comisiones semana 1"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancelar</button>
                                                                <button type="submit" name="record_payout"
                                                                    class="btn btn-primary">Registrar Pago</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <hr>
                    <h6 class="mb-3">Historial de Pagos a Empleados</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Empleado</th>
                                    <th>Monto</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmtHist = $con->prepare("SELECT ep.*, e.first_name, e.last_name FROM employee_payouts ep JOIN employees e ON ep.employee_id = e.employee_id WHERE ep.tenant_id = ? ORDER BY ep.created_at DESC LIMIT 50");
                                $stmtHist->execute([$tenant_id]);
                                $payouts = $stmtHist->fetchAll();
                                foreach ($payouts as $p):
                                    ?>
                                    <tr>
                                        <td><?php echo $p['created_at']; ?></td>
                                        <td><?php echo $p['first_name'] . ' ' . $p['last_name']; ?></td>
                                        <td>$<?php echo number_format($p['amount'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($p['notes']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <?php
    include 'Includes/templates/footer.php';
} else {
    header('Location: login.php');
    exit();
}
?>