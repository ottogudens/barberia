<?php
session_start();
if (!isset($_SESSION['super_admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../connect.php';
include '../Includes/functions/functions.php';

if (!isset($_GET['tenant_id'])) {
    header('Location: dashboard.php');
    exit();
}

$tenant_id = $_GET['tenant_id'];
$stmt = $con->prepare("SELECT * FROM tenants WHERE tenant_id = ?");
$stmt->execute([$tenant_id]);
$tenant = $stmt->fetch();

if (!$tenant) {
    echo "Tenant not found!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = test_input($_POST['name']);
    $email = test_input($_POST['email']);
    $slug = test_input($_POST['slug']);

    // Update tenant
    $updateStmt = $con->prepare("UPDATE tenants SET name = ?, owner_email = ?, slug = ? WHERE tenant_id = ?");
    try {
        $updateStmt->execute([$name, $email, $slug, $tenant_id]);
        $success = "Tenant updated successfully!";
        // Refresh data
        $stmt->execute([$tenant_id]);
        $tenant = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Error updating tenant: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Tenant - Super Admin</title>
    <link href="../Design/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../Design/fonts/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary" style="background-color: #f8f9fc;">

    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Edit Tenant</h1>
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
                                    <label>Barbershop Name</label>
                                    <input type="text" class="form-control form-control-user" name="name"
                                        value="<?php echo htmlspecialchars($tenant['name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Owner Email</label>
                                    <input type="email" class="form-control form-control-user" name="email"
                                        value="<?php echo htmlspecialchars($tenant['owner_email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Slug (Subdomain)</label>
                                    <input type="text" class="form-control form-control-user" name="slug"
                                        value="<?php echo htmlspecialchars($tenant['slug']); ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block"
                                    style="background-color: #D4AF37; border-color: #D4AF37; color: black;">
                                    Save Changes
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