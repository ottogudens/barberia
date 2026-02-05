<?php
session_start();
include 'connect.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

if (!$tenant_id) {
    die("Acceso denegado.");
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// FETCH APPOINTMENTS
$stmtAppts = $con->prepare("SELECT a.*, c.first_name as c_fname, c.last_name as c_lname, e.first_name as e_fname, e.last_name as e_lname
                            FROM appointments a
                            JOIN clients c ON a.client_id = c.client_id
                            JOIN employees e ON a.employee_id = e.employee_id
                            WHERE a.tenant_id = ? 
                            AND DATE(a.start_time) BETWEEN ? AND ?
                            ORDER BY a.start_time DESC");
$stmtAppts->execute([$tenant_id, $start_date, $end_date]);
$rows = $stmtAppts->fetchAll(PDO::FETCH_ASSOC);

// CSV Headers
$filename = "historial_atenciones_" . $start_date . "_al_" . $end_date . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Add Column Headers
fputcsv($output, array('ID', 'Fecha', 'Hora', 'Cliente', 'Empleado', 'Servicios', 'Estado', 'Monto'));

// Add Rows
foreach ($rows as $row) {
    // Determine Status
    $status = 'Pendiente';
    if ($row['canceled'] == 1)
        $status = 'Cancelada';
    elseif ($row['is_paid'] == 1)
        $status = 'Pagada';

    // Get Services
    $stmtSvc = $con->prepare("SELECT s.service_name FROM services s JOIN services_booked sb ON s.service_id = sb.service_id WHERE sb.appointment_id = ?");
    $stmtSvc->execute([$row['appointment_id']]);
    $svcs = $stmtSvc->fetchAll(PDO::FETCH_COLUMN);
    $service_list = implode(", ", $svcs);

    fputcsv($output, array(
        $row['appointment_id'],
        date('Y-m-d', strtotime($row['start_time'])),
        date('H:i', strtotime($row['start_time'])),
        $row['c_fname'] . " " . $row['c_lname'],
        $row['e_fname'] . " " . $row['e_lname'],
        $service_list,
        $status,
        $row['total_amount']
    ));
}

fclose($output);
exit();
