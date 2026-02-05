<?php include '../connect.php'; ?>
<?php include '../Includes/functions/functions.php'; ?>
<?php include '../../Includes/tenant_context.php'; ?>


<?php

$tenant_id = getCurrentTenantId($con);

if (isset($_POST['do']) && $_POST['do'] == "Delete") {
	$employee_id = $_POST['employee_id'];

	$stmt = $con->prepare("DELETE from employees where employee_id = ? AND tenant_id = ?");
	$stmt->execute(array($employee_id, $tenant_id));
}

?>