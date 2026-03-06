<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username_barbershop_Xw211qAAsq4']) || !isset($_SESSION['admin_id_barbershop_Xw211qAAsq4'])) {
    header('Location: login.php');
    exit();
}
?>