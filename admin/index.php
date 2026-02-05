<?php
session_start();

//Check If user is already logged in
if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {
    //Page Title
    $pageTitle = 'Dashboard';

    //Includes
    include 'connect.php';
    include 'Includes/functions/functions.php';
    include 'Includes/templates/header.php';
    include '../Includes/tenant_context.php';

    $tenant_id = getCurrentTenantId($con);

    ?>
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->

        <!-- Content Row -->
        <div class="row">

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total de Clientes
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo countItems("client_id", "clients", $tenant_id) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bs bs-boy fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total de Servicios
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo countItems("service_id", "services", $tenant_id) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bs bs-scissors-1 fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Empleados
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            <?php echo countItems("employee_id", "employees", $tenant_id) ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bs bs-man fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Citas
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo countItems("appointment_id", "appointments", $tenant_id) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ventas del Día -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Ventas Hoy
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php
                                    $stmt = $con->prepare("SELECT SUM(total_amount) FROM appointments WHERE tenant_id = ? AND is_paid = 1 AND DATE(paid_at) = CURDATE()");
                                    $stmt->execute([$tenant_id]);
                                    echo "$" . number_format($stmt->fetchColumn() ?: 0, 2);
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ventas de la Semana -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Ventas Semana
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php
                                    $stmt = $con->prepare("SELECT SUM(total_amount) FROM appointments WHERE tenant_id = ? AND is_paid = 1 AND YEARWEEK(paid_at, 1) = YEARWEEK(CURDATE(), 1)");
                                    $stmt->execute([$tenant_id]);
                                    echo "$" . number_format($stmt->fetchColumn() ?: 0, 2);
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <!-- Top Services -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4 h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Servicios Más Solicitados</h6>
                    </div>
                    <div class="card-body">

                        <?php
                        $stmtTop = $con->prepare("SELECT s.service_name, COUNT(sb.service_id) as usage_count 
                                                    FROM services s 
                                                    JOIN services_booked sb ON s.service_id = sb.service_id 
                                                    JOIN appointments a ON sb.appointment_id = a.appointment_id 
                                                    WHERE a.tenant_id = ? 
                                                    GROUP BY s.service_id 
                                                    ORDER BY usage_count DESC 
                                                    LIMIT 5");
                        $stmtTop->execute([$tenant_id]);
                        $topServices = $stmtTop->fetchAll();

                        if (count($topServices) > 0) {
                            echo '<ul class="list-group">';
                            foreach ($topServices as $svc) {
                                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                echo $svc['service_name'];
                                echo '<span class="badge badge-primary badge-pill">' . $svc['usage_count'] . '</span>';
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo "<p class='text-muted'>No hay datos de servicios aún.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Employee of the Month -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4 h-100">
                    <div class="card-header py-3">

                        <h6 class="m-0 font-weight-bold text-success">Empleado del Mes</h6>
                    </div>
                    <div class="card-body text-center">
                        <?php
                        $stmtEmpMonth = $con->prepare("SELECT e.first_name, e.last_name, e.image, COUNT(a.appointment_id) as appt_count 
                                                        FROM employees e 
                                                        JOIN appointments a ON e.employee_id = a.employee_id 
                                                        WHERE a.tenant_id = ? AND a.canceled = 0 
                                                        AND MONTH(a.start_time) = MONTH(CURRENT_DATE()) AND YEAR(a.start_time) = YEAR(CURRENT_DATE())
                                                        GROUP BY e.employee_id 
                                                        ORDER BY appt_count DESC 
                                                        LIMIT 1");
                        $stmtEmpMonth->execute([$tenant_id]);
                        $empOfTheMonth = $stmtEmpMonth->fetch();

                        if ($empOfTheMonth) {
                            $img = !empty($empOfTheMonth['image']) ? $empOfTheMonth['image'] : 'Design/images/default_employee.png';
                            echo "<img src='../$img' class='img-fluid rounded-circle mb-3' style='width: 150px; height: 150px; object-fit: cover;'>";
                            echo "<h4>" . $empOfTheMonth['first_name'] . " " . $empOfTheMonth['last_name'] . "</h4>";
                            echo "<p class='lead text-gray-800'>Atenciones: <strong>" . $empOfTheMonth['appt_count'] . "</strong></p>";
                        } else {
                            echo "<p class='text-muted'>Aún no hay datos suficientes este mes.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Tables -->
        <div class="card shadow mb-4">
            <div class="card-header tab" style="padding: 0px !important;background: #1984bc!important">
                <button class="tablinks active" onclick="openTab(event, 'Upcoming')">
                    Reservas Próximas
                </button>
                <button class="tablinks" onclick="openTab(event, 'All')">
                    Todas las Reservas
                </button>
                <button class="tablinks" onclick="openTab(event, 'Canceled')">
                    Reservas Canceladas
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered tabcontent" id="Upcoming" style="display:table" width="100%"
                        cellspacing="0">
                        <thead>
                            <tr>
                                <th>
                                    Hora de inicio
                                </th>
                                <th>
                                    Servicios reservados
                                </th>
                                <th>
                                    Hora de finalización prevista
                                </th>
                                <th>
                                    Cliente
                                </th>
                                <th>
                                    Empleado
                                </th>
                                <th>
                                    Administrar
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            // UPCOMING APPOINTMENTS
                            $stmt = $con->prepare("SELECT a.*, c.first_name as c_fname, c.last_name as c_lname 
                                                    FROM appointments a 
                                                    JOIN clients c ON a.client_id = c.client_id
                                                    where a.start_time >= ?
                                                    and a.canceled = 0
                                                    and a.tenant_id = ?
                                                    order by a.start_time");
                            $stmt->execute(array(date('Y-m-d H:i:s'), $tenant_id));
                            $rows = $stmt->fetchAll();
                            $count = $stmt->rowCount();

                            if ($count == 0) {
                                echo "<tr><td colspan='6' class='text-center'>La lista de sus próximas reservas se presentará aquí</td></tr>";
                            } else {
                                foreach ($rows as $row) {
                                    echo "<tr>";
                                    echo "<td>" . $row['start_time'] . "</td>";
                                    echo "<td>";
                                    $stmtServices = $con->prepare("SELECT service_name from services s, services_booked sb where s.service_id = sb.service_id and appointment_id = ?");
                                    $stmtServices->execute(array($row['appointment_id']));
                                    $rowsServices = $stmtServices->fetchAll();
                                    foreach ($rowsServices as $rowsService) {
                                        echo "- " . $rowsService['service_name'] . "<br>";
                                    }
                                    echo "</td>";
                                    echo "<td>" . $row['end_time_expected'] . "</td>";
                                    echo "<td>" . $row['c_fname'] . " " . $row['c_lname'] . "</td>"; // Full Name fixed
                                    echo "<td>";
                                    $stmtEmployees = $con->prepare("SELECT first_name,last_name from employees e, appointments a where e.employee_id = a.employee_id and a.appointment_id = ?");
                                    $stmtEmployees->execute(array($row['appointment_id']));
                                    $rowsEmployees = $stmtEmployees->fetchAll();
                                    foreach ($rowsEmployees as $rowsEmployee) {
                                        echo $rowsEmployee['first_name'] . " " . $rowsEmployee['last_name'];
                                    }
                                    echo "</td>";
                                    echo "<td>";
                                    $cancel_data = "cancel_appointment_" . $row["appointment_id"];
                                    ?>
                                    <ul class="list-inline m-0">
                                        
                                        <!-- EDIT BUTTON -->
                                        <li class="list-inline-item" data-toggle="tooltip" title="Editar Reserva">
                                            <a href="edit_appointment.php?appointment_id=<?php echo $row['appointment_id']; ?>" class="btn btn-primary btn-sm rounded-0">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </li>

                                        <!-- CANCEL BUTTON -->
                                        <li class="list-inline-item" data-toggle="tooltip" title="Cancelar Cita">
                                            <button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="modal" data-target="#<?php echo $cancel_data; ?>" data-placement="top">
                                                <i class="fas fa-calendar-times"></i>
                                            </button>
                                            <div class="modal fade" id="<?php echo $cancel_data; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cancelar cita</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                                    <div class="modal-body">
                                                                        <p>¿Deseas cancelar esta cita?</p>
                                                                        <div class="form-group">
                                                                            <label>¿Dinos por qué?</label>
                                                                            <textarea class="form-control" id=<?php echo "appointment_cancellation_reason_" . $row['appointment_id'] ?>></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                                                        <button type="button" data-id="<?php echo $row['appointment_id']; ?>" class="btn btn-danger cancel_appointment_button">Sí, Cancelar</button>
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
                            }
                            ?>
                            </tbody>
                        </table>
                    
                        <!-- ALL APPOINTMENTS -->
                        <table class="table table-bordered tabcontent" id="All" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Hora de inicio</th>
                                    <th>Servicios reservados</th>
                                    <th>Hora de finalización</th>
                                    <th>Cliente</th>
                                    <th>Empleado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $con->prepare("SELECT a.*, c.first_name as c_fname, c.last_name as c_lname 
                                                FROM appointments a 
                                                JOIN clients c ON a.client_id = c.client_id
                                                where a.tenant_id = ?
                                                order by start_time");
                                $stmt->execute(array($tenant_id));
                                $rows = $stmt->fetchAll();
                                $count = $stmt->rowCount();

                                if ($count == 0) {
                                    echo "<tr><td colspan='5' class='text-center'>La lista de todas sus reservas se presentará aquí</td></tr>";
                                } else {
                                    foreach ($rows as $row) {
                                        echo "<tr>";
                                        echo "<td>" . $row['start_time'] . "</td>";
                                        echo "<td>";
                                        $stmtServices = $con->prepare("SELECT service_name from services s, services_booked sb where s.service_id = sb.service_id and appointment_id = ?");
                                        $stmtServices->execute(array($row['appointment_id']));
                                        $rowsServices = $stmtServices->fetchAll();
                                        foreach ($rowsServices as $rowsService) {
                                            echo $rowsService['service_name'] . " + ";
                                        }
                                        echo "</td>";
                                        echo "<td>" . $row['end_time_expected'] . "</td>";
                                        echo "<td>" . $row['c_fname'] . " " . $row['c_lname'] . "</td>"; // Full Name fixed
                                        echo "<td>";
                                        $stmtEmployees = $con->prepare("SELECT first_name,last_name from employees e, appointments a where e.employee_id = a.employee_id and a.appointment_id = ?");
                                        $stmtEmployees->execute(array($row['appointment_id']));
                                        $rowsEmployees = $stmtEmployees->fetchAll();
                                        foreach ($rowsEmployees as $rowsEmployee) {
                                            echo $rowsEmployee['first_name'] . " " . $rowsEmployee['last_name'];
                                        }
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    
                        <!-- CANCELED APPOINTMENTS -->
                        <table class="table table-bordered tabcontent" id="Canceled" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Hora de inicio</th>
                                    <th>Cliente</th>
                                    <th>Razon de cancelación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $con->prepare("SELECT a.*, c.first_name as c_fname, c.last_name as c_lname 
                                                FROM appointments a 
                                                JOIN clients c ON a.client_id = c.client_id
                                                where canceled = 1
                                                and a.tenant_id = ?");
                                $stmt->execute(array($tenant_id));
                                $rows = $stmt->fetchAll();
                                $count = $stmt->rowCount();

                                if ($count == 0) {
                                    echo "<tr><td colspan='3' class='text-center'>La lista de sus reservas canceladas se presentará aquí</td></tr>";
                                } else {
                                    foreach ($rows as $row) {
                                        echo "<tr>";
                                        echo "<td>" . $row['start_time'] . "</td>";
                                        echo "<td>" . $row['c_fname'] . " " . $row['c_lname'] . "</td>"; // Full Name fixed
                                        echo "<td>" . $row['cancellation_reason'] . "</td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    <?php

    //Include Footer
    include 'Includes/templates/footer.php';
} else {
    header('Location: login.php');
    exit();
}

?>