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
	** Count Items Function
	** This function counts and return the number of elements in a given table
	==============================================================

*/


function countItems($item, $table, $tenant_id = null)
{
	global $con;
	if ($tenant_id) {
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
	** Check Items Function
	** Function to Check Item In Database [Function with Parameters]
	** $select = the item to select [Example : user, item, category]
	** $from = the table to select from [Example : users, items, categories]
	** $value = The value of select [Example: Ossama, Box, Electronics]
	==============================================================

*/

function checkItem($select, $from, $value, $tenant_id = null)
{
	global $con;
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
	$data = htmlspecialchars($data);
	return $data;
}




