<?php
session_start();

//Check If user is already logged in
if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['admin_id_barbershop_Xw211qAAsq4'])) {
    //Page Title
    $pageTitle = 'Dashboard';

    //Includes
    include 'connect.php';
    include 'Includes/functions/functions.php';
    include 'Includes/templates/header.php';
    include '../Includes/tenant_context.php';

    $tenant_id = getCurrentTenantId($con);

    ?>
    <!-- Begin Page Content -->
    <div class="container-fluid py-4">

        <?php if ($_SESSION['username_barbershop_Xw211qAAsq4'] == 'demo'): ?>
            <div class="alert alert-info border-0 shadow-sm glass-card mb-4 d-flex align-items-center animate-fade-in"
                style="background: rgba(212, 175, 55, 0.15); border: 1px solid var(--primary-gold) !important;">
                <i class="fas fa-crown mr-3 fa-lg text-gold"></i>
                <div>
                    <span class="font-weight-bold">Modo Demostración Activo:</span>
                    Estás visualizando datos de prueba reales. Puedes resetear el entorno en cualquier momento.
                </div>
            </div>
            <?php if (isset($_SESSION['demo_reset_msg'])): ?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Listo',
                        text: '<?php echo $_SESSION['demo_reset_msg'];
                        unset($_SESSION['demo_reset_msg']); ?>',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#1a202c',
                        color: '#fff'
                    });
                </script>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 animate-fade-in">
            <h1 class="h3 mb-0 text-white font-weight-bold">Dashboard Administrativo</h1>
            <div class="d-flex">
                <?php if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && $_SESSION['username_barbershop_Xw211qAAsq4'] == 'demo'): ?>
                    <button class="btn btn-outline-warning btn-sm shadow-sm mr-2 glass-card" onclick="resetDemo()">
                        <i class="fas fa-sync-alt"></i> Resetear Demo
                    </button>
                <?php endif; ?>
                <a href="reservations_stats.php"
                    class="d-none d-sm-inline-block btn btn-sm btn-outline-light shadow-sm mr-2 glass-card">
                    <i class="fas fa-download fa-sm text-white-50"></i> Generar Reporte
                </a>
                <button class="btn btn-primary btn-sm shadow-sm"
                    onclick="Swal.fire('Tip', 'Puedes ver el historial detallado en la sección de reportes', 'info')">
                    <i class="fas fa-lightbulb"></i> Ver Tips
                </button>
            </div>
        </div>

        <!-- KPI Cards Row -->
        <div class="row">

            <div class="col-xl-3 col-md-6 mb-4 animate-fade-in">
                <div class="card glass-card shadow h-100 py-2 border-left-primary">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-gold text-uppercase mb-1"
                                    style="letter-spacing: 0.5px;">Total Clientes</div>
                                <div class="h3 mb-0 font-weight-bold text-white">
                                    <?php echo countItems("client_id", "clients", $tenant_id) ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-800 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4 animate-fade-in" style="animation-delay: 0.1s;">
                <div class="card glass-card shadow h-100 py-2 border-left-success">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ventas Hoy</div>
                                <div class="h3 mb-0 font-weight-bold text-white">
                                    <?php
                                    $stmt = $con->prepare("SELECT SUM(total_amount) FROM appointments WHERE tenant_id = ? AND is_paid = 1 AND DATE(paid_at) = CURRENT_DATE");
                                    $stmt->execute([$tenant_id]);
                                    echo "$" . number_format($stmt->fetchColumn() ?: 0, 0);
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-800 opacity-50"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4 animate-fade-in" style="animation-delay: 0.2s;">
                <div class="card glass-card shadow h-100 py-2 border-left-warning">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pendiente de Cobro
                                </div>
                                <div class="h3 mb-0 font-weight-bold text-white">
                                    <?php
                                    $stmt = $con->prepare("SELECT COUNT(*) FROM appointments WHERE tenant_id = ? AND is_paid = 0 AND canceled = 0");
                                    $stmt->execute([$tenant_id]);
                                    echo $stmt->fetchColumn() ?: 0;
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-800 opacity-50"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4 animate-fade-in" style="animation-delay: 0.3s;">
                <div class="card glass-card shadow h-100 py-2 border-left-info">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Citas Finalizadas Hoy
                                </div>
                                <div class="h3 mb-0 font-weight-bold text-white">
                                    <?php
                                    $stmt = $con->prepare("SELECT COUNT(*) FROM appointments WHERE tenant_id = ? AND DATE(start_time) = CURRENT_DATE AND canceled = 0");
                                    $stmt->execute([$tenant_id]);
                                    echo $stmt->fetchColumn() ?: 0;
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-800 opacity-50"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Lists Row -->
        <div class="row">
            <!-- Sales Chart -->
            <div class="col-xl-8 col-lg-7 mb-4 animate-fade-in" style="animation-delay: 0.4s;">
                <div class="card glass-card shadow h-100 border-0">
                    <div class="card-header d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-gold text-uppercase">Rendimiento de Ventas (Últimos 7 días)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 300px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Services List -->
            <div class="col-xl-4 col-lg-5 mb-4 animate-fade-in" style="animation-delay: 0.5s;">
                <div class="card glass-card shadow h-100 border-0">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-gold text-uppercase">Servicios más Solicitados</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmtTop = $con->prepare("SELECT s.service_name, COUNT(sb.service_id) as usage_count 
                                                    FROM services s 
                                                    JOIN services_booked sb ON s.service_id = sb.service_id 
                                                    JOIN appointments a ON sb.appointment_id = a.appointment_id 
                                                    WHERE a.tenant_id = ? 
                                                    GROUP BY s.service_id, s.service_name
                                                    ORDER BY usage_count DESC 
                                                    LIMIT 5");
                        $stmtTop->execute([$tenant_id]);
                        $topServices = $stmtTop->fetchAll();

                        if (count($topServices) > 0) {
                            foreach ($topServices as $svc) {
                                $pct = min(100, $svc['usage_count'] * 10); // Dummy percentage logic for visual
                                echo '<div class="mb-3">';
                                echo '  <div class="small text-white-50 d-flex justify-content-between"><span>' . htmlspecialchars($svc['service_name']) . '</span><span>' . $svc['usage_count'] . '</span></div>';
                                echo '  <div class="progress progress-sm"><div class="progress-bar bg-gold" style="width: ' . $pct . '%"></div></div>';
                                echo '</div>';
                            }
                        } else {
                            echo "<div class='text-center py-5'><p class='text-muted'>No hay datos suficientes.</p></div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Appointments Table -->
        <div class="row">
            <div class="col-12 animate-fade-in" style="animation-delay: 0.6s;">
                <div class="card glass-card shadow mb-4 border-0">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-gold text-uppercase">Próximas Citas Hoy</h6>
                        <a href="calendar.php" class="btn btn-sm btn-link text-gold">Ver Calendario Full</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="color: #e2e8f0;">
                                <thead>
                                    <tr class="text-white-50 small text-uppercase">
                                        <th>Hora</th>
                                        <th>Cliente</th>
                                        <th>Barbero</th>
                                        <th>Servicios</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $con->prepare("SELECT a.*, c.first_name as c_fname, c.last_name as c_lname, e.first_name as e_fname, e.last_name as e_lname 
                                                            FROM appointments a 
                                                            JOIN clients c ON a.client_id = c.client_id
                                                            JOIN employees e ON a.employee_id = e.employee_id
                                                            WHERE a.tenant_id = ? AND a.canceled = 0 
                                                            AND DATE(a.start_time) = CURRENT_DATE
                                                            ORDER BY a.start_time ASC LIMIT 5");
                                    $stmt->execute([$tenant_id]);
                                    $rows = $stmt->fetchAll();

                                    if (count($rows) == 0) {
                                        echo "<tr><td colspan='5' class='text-center text-muted'>No hay más citas programadas para hoy.</td></tr>";
                                    } else {
                                        foreach ($rows as $row) {
                                            $startTime = date('H:i', strtotime($row['start_time']));
                                            echo "<tr>";
                                            echo "<td class='font-weight-bold'>" . $startTime . "</td>";
                                            echo "<td>" . $row['c_fname'] . " " . $row['c_lname'] . "</td>";
                                            echo "<td>" . $row['e_fname'] . "</td>";
                                            echo "<td class='small opacity-75'>";
                                            $stmtS = $con->prepare("SELECT service_name FROM services s JOIN services_booked sb ON s.service_id = sb.service_id WHERE sb.appointment_id = ?");
                                            $stmtS->execute([$row['appointment_id']]);
                                            echo implode(", ", $stmtS->fetchAll(PDO::FETCH_COLUMN));
                                            echo "</td>";
                                            echo "<td>";
                                            echo '<a href="edit_appointment.php?appointment_id=' . $row['appointment_id'] . '" class="btn btn-sm btn-circle btn-outline-light"><i class="fas fa-edit"></i></a>';
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart Data Logic -->
    <?php
    $labels = [];
    $data = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $labels[] = date('D', strtotime($date));

        $stmtSales = $con->prepare("SELECT SUM(total_amount) FROM appointments WHERE tenant_id = ? AND is_paid = 1 AND DATE(paid_at) = ?");
        $stmtSales->execute([$tenant_id, $date]);
        $data[] = $stmtSales->fetchColumn() ?: 0;
    }
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('salesChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Ventas ($)',
                        data: <?php echo json_encode($data); ?>,
                        backgroundColor: 'rgba(212, 175, 55, 0.1)',
                        borderColor: '#D4AF37',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#D4AF37',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#94a3b8' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8' }
                        }
                    }
                }
            });
        });

        function resetDemo() {
            Swal.fire({
                title: '¿Reiniciar Demo?',
                text: "Se generarán nuevos datos reales para la demostración.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#D4AF37',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, reiniciar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'demo_reset_handler.php';
                }
            })
        }
    </script>

    <?php
    //Include Footer
    include 'Includes/templates/footer.php';
} else {
    header('Location: login.php');
    exit();
}

?>