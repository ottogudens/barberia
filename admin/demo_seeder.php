<?php
include 'connect.php';

echo "Starting Demo Seeder...\n";

try {
    // 1. Create Demo Tenant
    $tenant_name = "Barbería Premium Demo";
    $tenant_slug = "demo-barber";
    $owner_email = "demo@skale.cl";

    $stmt = $con->prepare("INSERT INTO tenants (name, slug, owner_email, city, address, status) 
                           VALUES (?, ?, ?, 'Santiago', 'Av. Providencia 1234', 'active') 
                           ON CONFLICT (slug) DO UPDATE SET name = EXCLUDED.name RETURNING tenant_id");
    $stmt->execute([$tenant_name, $tenant_slug, $owner_email]);
    $tenant_id = $stmt->fetchColumn();

    if (!$tenant_id) {
        $stmt = $con->prepare("SELECT tenant_id FROM tenants WHERE slug = ?");
        $stmt->execute([$tenant_slug]);
        $tenant_id = $stmt->fetchColumn();
    }

    echo "Tenant ID: $tenant_id\n";

    // CLEANUP EXISTING DEMO DATA (Reset)
    echo "Cleaning up existing demo data...\n";
    $con->prepare("DELETE FROM appointments WHERE tenant_id = ?")->execute([$tenant_id]);
    $con->prepare("DELETE FROM employees WHERE tenant_id = ?")->execute([$tenant_id]);
    $con->prepare("DELETE FROM clients WHERE tenant_id = ?")->execute([$tenant_id]);
    $con->prepare("DELETE FROM services WHERE tenant_id = ?")->execute([$tenant_id]);
    $con->prepare("DELETE FROM service_categories WHERE tenant_id = ?")->execute([$tenant_id]);
    $con->prepare("DELETE FROM expenses WHERE tenant_id = ?")->execute([$tenant_id]);

    // 2. Create Demo Admin
    $admin_user = "demo";
    $admin_pass = password_hash("demo123", PASSWORD_DEFAULT);
    $stmt = $con->prepare("INSERT INTO barber_admin (username, password, email, full_name, tenant_id) 
                           VALUES (?, ?, ?, 'Administrador Demo', ?) 
                           ON CONFLICT DO NOTHING");
    $stmt->execute([$admin_user, $admin_pass, $owner_email, $tenant_id]);

    // 3. Create Categories
    $categories = ['Corte de Cabello', 'Barba & Afeitado', 'Tratamientos Capilares', 'Combos Especiales'];
    $cat_ids = [];
    foreach ($categories as $cat) {
        $stmt = $con->prepare("INSERT INTO service_categories (category_name, tenant_id) VALUES (?, ?) RETURNING category_id");
        $stmt->execute([$cat, $tenant_id]);
        $cat_ids[$cat] = $stmt->fetchColumn();
    }

    // 4. Create Services
    $services = [
        ['Corte Caballero Tradicional', 'Corte clásico con tijera y máquina', 15000, 45, 'Corte de Cabello'],
        ['Degradado / Fade', 'Corte moderno con degradado impecable', 18000, 60, 'Corte de Cabello'],
        ['Perfilado de Barba', 'Diseño y recorte de barba con toalla caliente', 12000, 30, 'Barba & Afeitado'],
        ['Afeitado Clásico a Navaja', 'Afeitado completo con productos premium', 15000, 45, 'Barba & Afeitado'],
        ['Limpieza Facial Profunda', 'Eliminación de impurezas y exfoliación', 20000, 45, 'Tratamientos Capilares'],
        ['Combo Premium', 'Corte + Barba + Lavado + Masaje', 30000, 90, 'Combos Especiales']
    ];
    $svc_ids = [];
    foreach ($services as $s) {
        $stmt = $con->prepare("INSERT INTO services (service_name, service_description, service_price, service_duration, category_id, tenant_id) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$s[0], $s[1], $s[2], $s[3], $cat_ids[$s[4]], $tenant_id]);
        $svc_ids[] = $con->lastInsertId();
    }

    // 5. Create Employees
    $employees = [
        ['Carlos', 'Rodríguez', '+56911112222', 'carlos@barber.cl'],
        ['Sebastián', 'Muñoz', '+56933334444', 'sebastian@barber.cl'],
        ['Mateo', 'López', '+56955556666', 'mateo@barber.cl']
    ];
    $emp_ids = [];
    foreach ($employees as $e) {
        $stmt = $con->prepare("INSERT INTO employees (first_name, last_name, phone_number, email, tenant_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$e[0], $e[1], $e[2], $e[3], $tenant_id]);
        $emp_ids[] = $con->lastInsertId();
    }

    // 6. Create Clients
    $clients = [
        ['Juan', 'Pérez', '988887777', 'juan@gmail.com'],
        ['Diego', 'Soto', '977776666', 'diego@gmail.com'],
        ['Andrés', 'Silva', '966665555', 'andres@gmail.com'],
        ['Roberto', 'Díaz', '955554444', 'roberto@gmail.com']
    ];
    $cli_ids = [];
    foreach ($clients as $c) {
        $stmt = $con->prepare("INSERT INTO clients (first_name, last_name, phone_number, client_email, tenant_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$c[0], $c[1], $c[2], $c[3], $tenant_id]);
        $cli_ids[] = $con->lastInsertId();
    }

    // 7. Create Historical Appointments (Last 30 days)
    echo "Creating historical appointments...\n";
    for ($i = 0; $i < 40; $i++) {
        $days_ago = rand(0, 30);
        $hour = rand(9, 19);
        $date = date('Y-m-d H:i:s', strtotime("-$days_ago days $hour:00:00"));
        $end_date = date('Y-m-d H:i:s', strtotime($date . ' + 1 hour'));

        $c_id = $cli_ids[array_rand($cli_ids)];
        $e_id = $emp_ids[array_rand($emp_ids)];
        $is_paid = ($days_ago > 0) ? 1 : rand(0, 1);
        $total = rand(15000, 45000);

        $stmt = $con->prepare("INSERT INTO appointments (client_id, employee_id, start_time, end_time_expected, tenant_id, is_paid, total_amount, paid_at, canceled) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0) RETURNING appointment_id");
        $stmt->execute([$c_id, $e_id, $date, $end_date, $tenant_id, $is_paid, $total, ($is_paid ? $date : null)]);
        $appt_id = $stmt->fetchColumn();

        // Book services
        $stmt = $con->prepare("INSERT INTO services_booked (appointment_id, service_id) VALUES (?, ?)");
        $stmt->execute([$appt_id, $svc_ids[array_rand($svc_ids)]]);
    }

    // 8. Create Expenses
    $expenses = [
        [500000, 'Arriendo', 'Mensualidad local Providencia'],
        [150000, 'Suministros', 'Insumos barbería (Ceras, Shampoos)'],
        [80000, 'Marketing', 'Publicidad Instagram'],
        [45000, 'Servicios', 'Luz y Agua']
    ];
    foreach ($expenses as $ex) {
        $stmt = $con->prepare("INSERT INTO expenses (amount, category, description, expense_date, tenant_id) VALUES (?, ?, ?, CURRENT_DATE - INTERVAL '10 days', ?)");
        $stmt->execute([$ex[0], $ex[1], $ex[2], $tenant_id]);
    }

    echo "Demo Seeder completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
