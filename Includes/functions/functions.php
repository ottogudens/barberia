<?php
/*
	Title Function That Echo The Page Title In Case The Page Has The Variable $pageTitle And Echo Default Title For Other Pages
*/
function getTitle()
{
	global $pageTitle;
	if (isset($pageTitle))
		echo $pageTitle . " | Barbershop Website";
	else
		echo "Barbershop Website";
}

/*
	This function returns the number of items in a given table.
	Uses a whitelist of allowed table/column names to prevent SQL injection.
	Filters by tenant_id for multi-tenant data isolation.
*/

// Whitelist of allowed table/column combinations
function _getAllowedCountTargets()
{
	return [
		'clients' => 'client_id',
		'employees' => 'employee_id',
		'services' => 'service_id',
		'appointments' => 'appointment_id',
		'service_categories' => 'category_id',
		'offers' => 'offer_id',
		'gallery_images' => 'image_id',
		'expenses' => 'expense_id',
		'employee_payouts' => 'payout_id',
	];
}

function countItems($item, $table, $tenant_id = null)
{
	global $con;

	// Whitelist validation
	$allowed = _getAllowedCountTargets();
	if (!isset($allowed[$table]) || $allowed[$table] !== $item) {
		error_log("countItems() blocked: invalid table/column '$table'/'$item'");
		return 0;
	}

	if ($tenant_id !== null) {
		$stat_ = $con->prepare("SELECT COUNT($item) FROM $table WHERE tenant_id = ?");
		$stat_->execute([$tenant_id]);
	} else {
		$stat_ = $con->prepare("SELECT COUNT($item) FROM $table");
		$stat_->execute();
	}

	return $stat_->fetchColumn();
}

/*
 ** Check Items Function — Whitelisted version
 ** Checks if a value exists in an allowed table/column combination.
 */

function _getAllowedCheckTargets()
{
	return [
		'clients' => ['client_email', 'client_id'],
		'employees' => ['email', 'employee_id'],
		'services' => ['service_name', 'service_id'],
		'barber_admin' => ['username', 'admin_id'],
		'tenants' => ['slug', 'tenant_id'],
		'super_admins' => ['username'],
	];
}

function checkItem($select, $from, $value, $tenant_id = null)
{
	global $con;

	// Whitelist validation
	$allowed = _getAllowedCheckTargets();
	if (!isset($allowed[$from]) || !in_array($select, $allowed[$from])) {
		error_log("checkItem() blocked: invalid table/column '$from'/'$select'");
		return 0;
	}

	if ($tenant_id !== null) {
		$statment = $con->prepare("SELECT $select FROM $from WHERE $select = ? AND tenant_id = ?");
		$statment->execute(array($value, $tenant_id));
	} else {
		$statment = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
		$statment->execute(array($value));
	}
	$count = $statment->rowCount();

	return $count;
}


/*
  ==============================================
  TEST INPUT FUNCTION, IS USED FOR SANITIZING USER INPUTS
  AND REMOVE SUSPICIOUS CHARS and Remove Extra Spaces
  ==============================================
*/

function test_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
	return $data;
}

/**
 * Formats a numeric value as Chilean Pesos (CLP) with no decimals.
 * Example: 15000 -> $15.000
 */
function formatCurrency($value)
{
	$value = (float) $value;
	return '$' . number_format($value, 0, ',', '.');
}

