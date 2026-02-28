<?php
session_start();
include 'connect.php';

// Only allow demo user to reset demo data
if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && $_SESSION['username_barbershop_Xw211qAAsq4'] == 'demo') {
    // We execute the seeder script
    // Note: Since this is web-based, we'll try to include it or run it via shell if permitted.
    // For simplicity in this SaaS, we can just include the logic or trigger the file.

    ob_start();
    include 'demo_seeder.php';
    $output = ob_get_clean();

    $_SESSION['demo_reset_msg'] = "Demo reiniciada con éxito.";
    header('Location: index.php');
    exit();
} else {
    header('Location: login.php');
    exit();
}
