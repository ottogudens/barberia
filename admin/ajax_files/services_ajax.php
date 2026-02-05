<?php include '../connect.php'; ?>
<?php include '../Includes/functions/functions.php'; ?>
<?php include '../../Includes/tenant_context.php'; ?>


<?php

$tenant_id = getCurrentTenantId($con);

if (isset($_POST['do']) && $_POST['do'] == "Delete") {
	$service_id = $_POST['service_id'];

	$stmt = $con->prepare("DELETE from services where service_id = ? AND tenant_id = ?");
	$stmt->execute(array($service_id, $tenant_id));
}

?>