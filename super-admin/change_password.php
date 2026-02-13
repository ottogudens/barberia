<?php
session_start();
if (!isset($_SESSION['super_admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../connect.php';
include '../Includes/functions/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("UPDATE super_admins SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $_SESSION['super_admin_id']]);
        $success = "Password updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Password - Super Admin</title>
    <link href="../Design/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../Design/fonts/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary" style="background-color: #f8f9fc;">

    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Change Password</h1>
                            </div>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success">
                                    <?php echo $success; ?>
                                </div>
                            <?php endif; ?>
                            <form class="user" method="POST">
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" name="new_password"
                                        placeholder="New Password" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user"
                                        name="confirm_password" placeholder="Confirm Password" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block"
                                    style="background-color: #D4AF37; border-color: #D4AF37; color: black;">
                                    Update Password
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="dashboard.php">Back to Dashboard</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>