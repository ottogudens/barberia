<?php

/*
	=========================================================================================================================
	Title Function That Echo The Page Title In Case The Page Has The Variable $pageTitle And Echo Default Title For Other Pages
	=========================================================================================================================
*/

function getTitle()
{
	global $pageTitle;
	if (isset($pageTitle))
		echo $pageTitle . ' | Barbershop Salon';
	else
		echo "Barbershop | Barbershop Salon";
}

/*
	=============================================================
	** Count Items Function — Whitelisted version
	** Uses a whitelist to prevent SQL injection.
	** Filters by tenant_id for multi-tenant data isolation.
	==============================================================
*/

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
		$stat_->execute(array($tenant_id));
	} else {
		$stat_ = $con->prepare("SELECT COUNT($item) FROM $table");
		$stat_->execute();
	}

	return $stat_->fetchColumn();
}

/*
	=============================================================
	** Check Items Function — Whitelisted version
	** Checks if a value exists in an allowed table/column combination.
	==============================================================
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

	if ($tenant_id) {
		$statment = $con->prepare("SELECT $select FROM $from WHERE $select = ? AND tenant_id = ?");
		$statment->execute(array($value, $tenant_id));
	} else {
		$statment = $con->prepare("SELECT $select FROM $from WHERE $select = ? ");
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

/**
 * Upserts a setting value into the website_settings table.
 * Uses PostgreSQL ON CONFLICT DO UPDATE statement to prevent race conditions and duplicate logic.
 * 
 * @param PDO $con Database connection
 * @param int $tenant_id Tenant ID
 * @param string $key Setting Key
 * @param string $value Setting Value
 */
function upsertSetting($con, $tenant_id, $key, $value)
{
	$stmt = $con->prepare("
        INSERT INTO website_settings (tenant_id, setting_key, setting_value) 
        VALUES (?, ?, ?) 
        ON CONFLICT (tenant_id, setting_key) 
        DO UPDATE SET setting_value = EXCLUDED.setting_value
    ");
	$stmt->execute([$tenant_id, $key, $value]);
}
