<?php
ob_start();
session_start();

$pageTitle = 'Editar Reserva';

include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
include '../Includes/tenant_context.php';

$tenant_id = getCurrentTenantId($con);

if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {

    $appointment_id = isset($_GET['appointment_id']) && is_numeric($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_appointment'])) {
        $appointment_id = $_POST['appointment_id'];
        $employee_id = $_POST['employee_id'];
        $start_time = $_POST['start_time'];
        
        // Calculate End Time based on duration
        // First get original duration
        $stmtDur = $con->prepare("SELECT start_time, end_time_expected FROM appointments WHERE appointment_id = ? AND tenant_id = ?");
        $stmtDur->execute([$appointment_id, $tenant_id]);
        $appt = $stmtDur->fetch();
        
        if ($appt) {
            $orig_start = new DateTime($appt['start_time']);
            $orig_end = new DateTime($appt['end_time_expected']);
            $interval = $orig_start->diff($orig_end);
            
            $new_start = new DateTime($start_time);
            $new_end = clone $new_start;
            $new_end->add($interval);
            
            $end_time_expected = $new_end->format('Y-m-d H:i:s');
            $start_time_str = $new_start->format('Y-m-d H:i:s');

            $stmt = $con->prepare("UPDATE appointments SET employee_id = ?, start_time = ?, end_time_expected = ? WHERE appointment_id = ? AND tenant_id = ?");
            $stmt->execute([$employee_id, $start_time_str, $end_time_expected, $appointment_id, $tenant_id]);
            
            echo "<div class='alert alert-success'>Reserva actualizada exitosamente.</div>";
            echo "<script>setTimeout(\"location.href = 'index.php';\",1500);</script>";
        } else {
             echo "<div class='alert alert-danger'>Reserva no encontrada.</div>";
        }
    }

    $stmt = $con->prepare("SELECT * FROM appointments WHERE appointment_id = ? AND tenant_id = ?");
    $stmt->execute([$appointment_id, $tenant_id]);
    $appointment = $stmt->fetch();

    if ($appointment) {
        // Fetch Employees
        $stmtEmp = $con->prepare("SELECT * FROM employees WHERE tenant_id = ?");
        $stmtEmp->execute([$tenant_id]);
        $employees = $stmtEmp->fetchAll();
?>

    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Editar Reserva #<?php echo $appointment_id; ?></h6>
            </div>
            <div class="card-body">
                <form method="POST" action="edit_appointment.php?appointment_id=<?php echo $appointment_id; ?>">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                    
                    <div class="form-group">
                        <label>Fecha y Hora de Inicio</label>
                        <input type="datetime-local" class="form-control" name="start_time" 
                            value="<?php echo date('Y-m-d\TH:i', strtotime($appointment['start_time'])); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Empleado Asignado</label>
                        <select class="form-control" name="employee_id" required>
                            <?php foreach($employees as $emp): ?>
                                <option value="<?php echo $emp['employee_id']; ?>" 
                                    <?php if($emp['employee_id'] == $appointment['employee_id']) echo 'selected'; ?>>
                                    <?php echo $emp['first_name'] . ' ' . $emp['last_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Seleccione otro empleado si el original no está disponible.</small>
                    </div>

                    <button type="submit" name="update_appointment" class="btn btn-primary">Guardar Cambios</button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>

<?php
    } else {
        echo "<div class='container-fluid'><div class='alert alert-danger'>Reserva no encontrada.</div></div>";
    }

    include 'Includes/templates/footer.php';
} else {
    header('Location: login.php');
    exit();
}
?>
