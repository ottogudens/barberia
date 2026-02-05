<?php
session_start();
include 'connect.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

if (!$tenant_id) {
    die("Acceso denegado.");
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// FETCH COMPLETED PAYMENTS (INCOME)
$stmtPaid = $con->prepare("SELECT a.*, c.first_name as c_name, c.last_name as c_lname, e.first_name as e_name, e.last_name as e_lname 
                FROM appointments a 
                JOIN clients c ON a.client_id = c.client_id 
                JOIN employees e ON a.employee_id = e.employee_id 
                WHERE a.tenant_id = ? AND a.is_paid = 1 AND DATE(a.paid_at) BETWEEN ? AND ?
                ORDER BY a.paid_at DESC");
$stmtPaid->execute([$tenant_id, $start_date, $end_date]);
$paid_appts = $stmtPaid->fetchAll(PDO::FETCH_ASSOC);

// CSV Headers
$filename = "ingresos_" . $start_date . "_al_" . $end_date . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Add Column Headers
fputcsv($output, array('Fecha Pago', 'Cliente', 'Empleado', 'Metodo Pago', 'Monto'));

// Add Rows
foreach ($paid_appts as $appt) {
    fputcsv($output, array(
        $appt['paid_at'],
        $appt['c_name'] . ' ' . $appt['c_lname'],
        $appt['e_name'] . ' ' . $appt['e_lname'],
        $appt['payment_method'],
        $appt['total_amount']
    ));
}

fclose($output);
exit();
