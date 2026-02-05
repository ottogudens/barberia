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

// FETCH Data
$stmt = $con->prepare("SELECT * FROM expenses WHERE tenant_id = ? AND expense_date BETWEEN ? AND ? ORDER BY expense_date DESC");
$stmt->execute([$tenant_id, $start_date, $end_date]);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// CSV Headers
$filename = "egresos_" . $start_date . "_al_" . $end_date . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Add Column Headers
fputcsv($output, array('ID', 'Fecha', 'Categoria', 'Descripcion', 'Monto'));

// Add Rows
foreach ($expenses as $row) {
    fputcsv($output, array(
        $row['expense_id'],
        $row['expense_date'],
        $row['category'],
        $row['description'],
        $row['amount']
    ));
}

fclose($output);
exit();
