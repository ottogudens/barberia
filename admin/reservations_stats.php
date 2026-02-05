<?php
ob_start();
session_start();

$pageTitle = 'Historial y Reportes';

include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {

    // Date Filter Logic
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // Default to first of current month
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'); // Default to last of current month

    ?>
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Historial de Reservas y Reportes</h1>

        <!-- DATE FILTER FORM -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filtrar por Fecha</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="reservations_stats.php" class="form-inline">
                    <div class="form-group mb-2">
                        <label for="start_date" class="mr-2">Desde:</label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>"
                            required>
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="end_date" class="mr-2">Hasta:</label>
                        <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
                    <a href="reservations_stats.php" class="btn btn-secondary mb-2 ml-2">Limpiar</a>
                    <a href="export_appointments.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
                        class="btn btn-success mb-2 ml-2">
                        <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                    </a>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- TOP SERVICES -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Servicios Más Populares
                            (<?php echo $start_date . " al " . $end_date; ?>)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th>Reservas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmtServ = $con->prepare("SELECT s.service_name, COUNT(sb.service_id) as total_bookings
                                                                FROM services_booked sb
                                                                JOIN services s ON sb.service_id = s.service_id
                                                                JOIN appointments a ON sb.appointment_id = a.appointment_id
                                                                WHERE a.tenant_id = ? 
                                                                AND DATE(a.start_time) BETWEEN ? AND ?
                                                                AND a.canceled = 0
                                                                GROUP BY s.service_id
                                                                ORDER BY total_bookings DESC
                                                                LIMIT 5");
                                    $stmtServ->execute([$tenant_id, $start_date, $end_date]);
                                    $services = $stmtServ->fetchAll();

                                    foreach ($services as $svc) {
                                        echo "<tr>";
                                        echo "<td>" . $svc['service_name'] . "</td>";
                                        echo "<td><span class='badge badge-success'>" . $svc['total_bookings'] . "</span></td>";
                                        echo "</tr>";
                                    }
                                    if (count($services) == 0)
                                        echo "<tr><td colspan='2'>No hay datos en este rango.</td></tr>";
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOP EMPLOYEES -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Empleados con Más Reservas
                            (<?php echo $start_date . " al " . $end_date; ?>)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Empleado</th>
                                        <th>Total Reservas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmtEmp = $con->prepare("SELECT e.first_name, e.last_name, COUNT(a.appointment_id) as total_bookings
                                                                FROM employees e
                                                                JOIN appointments a ON e.employee_id = a.employee_id
                                                                WHERE a.tenant_id = ? 
                                                                AND DATE(a.start_time) BETWEEN ? AND ?
                                                                AND a.canceled = 0
                                                                GROUP BY e.employee_id
                                                                ORDER BY total_bookings DESC
                                                                LIMIT 5");
                                    $stmtEmp->execute([$tenant_id, $start_date, $end_date]);
                                    $employees = $stmtEmp->fetchAll();

                                    foreach ($employees as $emp) {
                                        echo "<tr>";
                                        echo "<td>" . $emp['first_name'] . " " . $emp['last_name'] . "</td>";
                                        echo "<td><span class='badge badge-primary'>" . $emp['total_bookings'] . "</span></td>";
                                        echo "</tr>";
                                    }
                                    if (count($employees) == 0)
                                        echo "<tr><td colspan='2'>No hay datos en este rango.</td></tr>";
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAILED APPOINTMENTS LIST -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Historial Detallado</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Empleado</th>
                                <th>Servicios</th>
                                <th>Estado</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmtAppts = $con->prepare("SELECT a.*, c.first_name as c_fname, c.last_name as c_lname, e.first_name as e_fname, e.last_name as e_lname
                                                        FROM appointments a
                                                        JOIN clients c ON a.client_id = c.client_id
                                                        JOIN employees e ON a.employee_id = e.employee_id
                                                        WHERE a.tenant_id = ? 
                                                        AND DATE(a.start_time) BETWEEN ? AND ?
                                                        ORDER BY a.start_time DESC");
                            $stmtAppts->execute([$tenant_id, $start_date, $end_date]);
                            $rows = $stmtAppts->fetchAll();

                            foreach ($rows as $row) {
                                $status = $row['canceled'] == 1 ? '<span class="badge badge-danger">Cancelada</span>' : ($row['is_paid'] == 1 ? '<span class="badge badge-success">Pagada</span>' : '<span class="badge badge-warning">Pendiente</span>');

                                // Get Services
                                $stmtSvc = $con->prepare("SELECT s.service_name FROM services s JOIN services_booked sb ON s.service_id = sb.service_id WHERE sb.appointment_id = ?");
                                $stmtSvc->execute([$row['appointment_id']]);
                                $svcs = $stmtSvc->fetchAll(PDO::FETCH_COLUMN);
                                $service_list = implode(", ", $svcs);

                                echo "<tr>";
                                echo "<td>" . date('Y-m-d', strtotime($row['start_time'])) . "</td>";
                                echo "<td>" . date('H:i', strtotime($row['start_time'])) . "</td>";
                                echo "<td>" . $row['c_fname'] . " " . $row['c_lname'] . "</td>";
                                echo "<td>" . $row['e_fname'] . " " . $row['e_lname'] . "</td>";
                                echo "<td>" . $service_list . "</td>";
                                echo "<td>" . $status . "</td>";
                                echo "<td>$" . number_format($row['total_amount'], 2) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
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