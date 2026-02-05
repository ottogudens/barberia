<?php
session_start();
$pageTitle = 'Respaldo de Base de Datos';
include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

// HANDLE BACKUP GENERATION
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_backup'])) {

    // DB Credentials
    $dbHost = getenv('DB_HOST') ?: '127.0.0.1';
    $dbUser = getenv('DB_USER') ?: 'barberia';
    $dbPass = getenv('DB_PASS') ?: 'Ing3N3tZ##';
    $dbName = getenv('DB_NAME') ?: 'barberia_prod';

    $backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

    // Command to dump database (excluding sensitive tables if needed, here full dump)
    // NOTE: This requires mysqldump to be in PATH and permissions to execute.
    $command = "mysqldump --user={$dbUser} --password='{$dbPass}' --host={$dbHost} {$dbName} > /tmp/{$backupFile}";

    system($command, $output);

    // Download Logic
    $file = '/tmp/' . $backupFile;
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        unlink($file); // Delete after download
        exit;
    } else {
        echo "<script>swal('Error', 'No se pudo generar el respaldo. Verifique configuración de mysqldump.', 'error');</script>";
    }
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Respaldo de Base de Datos</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Generar Copia de Seguridad</h6>
        </div>
        <div class="card-body">
            <p>
                Esta herramienta generará un archivo SQL completo de la base de datos del sistema.
                Guarde este archivo en un lugar seguro.
            </p>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Nota:</strong> Este proceso puede tardar unos segundos dependiendo del tamaño de la base de
                datos.
            </div>

            <form method="POST" action="backup.php">
                <button type="submit" name="generate_backup" class="btn btn-lg btn-success">
                    <i class="fas fa-database mb-1"></i> Generar y Descargar Backup
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'Includes/templates/footer.php'; ?>