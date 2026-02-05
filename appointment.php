<!-- PHP INCLUDES -->

<?php

include "connect.php";
include "Includes/functions/functions.php";
include "Includes/csrf.php";
include "Includes/tenant_context.php";

$tenant_id = getCurrentTenantId($con);

if (!$tenant_id) {
	header('Location: index.php');
	exit();
}

// Authentication Check
if (!isset($_SESSION['client_logged_in'])) {
	header('Location: login.php');
	exit();
}

// FETCH SETTINGS (Same as index.php to get colors)
$settings = [];
$stmt = $con->prepare("SELECT setting_key, setting_value FROM website_settings WHERE tenant_id = ?");
$stmt->execute([$tenant_id]);
while ($row = $stmt->fetch()) {
	$settings[$row['setting_key']] = $row['setting_value'];
}
// Defaults
$defaults = [
	'primary_color' => '#D4AF37',
	'secondary_color' => '#111111',
	'navbar_bg_color' => '#000000',
	'footer_bg_color' => '#111111',
	'background_color' => '#ffffff',
	'text_color' => '#333333',
	'website_logo' => 'Design/images/barbershop_logo.png'
];
foreach ($defaults as $key => $val) {
	if (!isset($settings[$key]) || empty($settings[$key]))
		$settings[$key] = $val;
}

include "Includes/templates/header.php";

// CUSTOM NAVBAR (To match index.php without duplicate code if possible, but for now inline to ensure consistency)
?>
<style>
	:root {
		--primary-color:
			<?php echo $settings['primary_color']; ?>
		;
		--secondary-color:
			<?php echo $settings['secondary_color']; ?>
		;
		--navbar-bg:
			<?php echo $settings['navbar_bg_color']; ?>
		;
		--footer-bg:
			<?php echo $settings['footer_bg_color']; ?>
		;
		--body-bg:
			<?php echo $settings['background_color']; ?>
		;
		--text-color:
			<?php echo $settings['text_color']; ?>
		;
	}

	body {
		background-color: var(--body-bg) !important;
		color: var(--text-color) !important;
	}

	.navbar {
		background-color: var(--navbar-bg) !important;
	}

	.footer_widget,
	.widget_section {
		background-color: var(--footer-bg) !important;
	}

	/* Appointment Page Specifics */
	.next_prev_buttons {
		background-color: var(--primary-color) !important;
	}

	.step.active {
		opacity: 1;
		background-color: var(--primary-color) !important;
	}

	.form-control:focus {
		border-color: var(--primary-color) !important;
		box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
		/* Approximate gold */
	}

	label.btn.btn-secondary.active {
		background-color: var(--primary-color) !important;
		border-color: var(--primary-color) !important;
	}
</style>

<!-- START NAVBAR SECTION -->
<?php include "Includes/templates/navbar.php"; ?>
<!-- END NAVBAR SECTION -->

<!-- Appointment Page Stylesheet -->
<link rel="stylesheet" href="Design/css/appointment-page-style.css">

<!-- BOOKING APPOINTMENT SECTION -->

<section class="booking_section">
	<div class="container">

		<?php

		// Fetch Current Client Info
		$client_id = $_SESSION['client_id'];
		$stmtClientInfo = $con->prepare("SELECT * FROM clients WHERE client_id = ?");
		$stmtClientInfo->execute([$client_id]);
		$current_client = $stmtClientInfo->fetch();

		if (isset($_POST['submit_book_appointment_form']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
			// ... (Keep existing logic)
			if (!verifyCsrfToken($_POST['csrf_token'])) {
				die("CSRF Token Verification Failed");
			}
			// Selected SERVICES
		
			$selected_services = $_POST['selected_services'];

			// Selected EMPLOYEE
		
			$selected_employee = $_POST['selected_employee'];

			// Selected DATE+TIME
		
			$selected_date_time = explode(' ', $_POST['desired_date_time']);

			$date_selected = $selected_date_time[0];
			$start_time = $date_selected . " " . $selected_date_time[1];
			$end_time = $date_selected . " " . $selected_date_time[2];


			//Client Details
		
			$client_first_name = test_input($_POST['client_first_name']);
			$client_last_name = test_input($_POST['client_last_name']);
			$client_phone_number = test_input($_POST['client_phone_number']);
			$client_email = test_input($_POST['client_email']);

			$con->beginTransaction();

			try {
				$tenant_id = getCurrentTenantId($con);

				// Check If the client's email already exist in our database
				$stmtCheckClient = $con->prepare("SELECT * FROM clients WHERE client_email = ? AND tenant_id = ?");
				$stmtCheckClient->execute(array($client_email, $tenant_id));
				$client_result = $stmtCheckClient->fetch();
				$client_count = $stmtCheckClient->rowCount();

				if ($client_count > 0) {
					$client_id = $client_result["client_id"];
				} else {
					$stmtClient = $con->prepare("insert into clients(first_name,last_name,phone_number,client_email,tenant_id) 
									values(?,?,?,?,?)");
					$stmtClient->execute(array($client_first_name, $client_last_name, $client_phone_number, $client_email, $tenant_id));
					$client_id = $con->lastInsertId();
				}

				$stmt_appointment = $con->prepare("insert into appointments(date_created, client_id, employee_id, start_time, end_time_expected, tenant_id ) values(?, ?, ?, ?, ?, ?)");
				$stmt_appointment->execute(array(Date("Y-m-d H:i"), $client_id, $selected_employee, $start_time, $end_time, $tenant_id));
				$appointment_id = $con->lastInsertId();

				foreach ($selected_services as $service) {
					$stmt = $con->prepare("insert into services_booked(appointment_id, service_id) values(?, ?)");
					$stmt->execute(array($appointment_id, $service));
				}

				echo "<div class = 'alert alert-success'>";
				echo "¡Excelente! Tu cita ha sido creada con éxito.";
				echo "</div>";

				$con->commit();
			} catch (Exception $e) {
				$con->rollBack();
				echo "<div class = 'alert alert-danger'>";
				echo $e->getMessage();
				echo "</div>";
			}
		}

		?>

		<!-- RESERVATION FORM -->

		<form method="post" id="appointment_form" action="appointment.php">
			<?php csrfInput(); ?>

			<!-- SELECT SERVICE -->

			<div class="select_services_div tab_reservation" id="services_tab">

				<!-- ALERT MESSAGE -->

				<div class="alert alert-danger" role="alert" style="display: none">
					¡Por favor, seleccione al menos un servicio!
				</div>

				<div class="text_header">
					<span>
						1. Escoge el servicio que requieres:
					</span>
				</div>

				<!-- SERVICES TAB -->

				<div class="items_tab">
					<?php
					$stmt = $con->prepare("Select * from services WHERE tenant_id = ?");
					$stmt->execute(array($tenant_id));
					$rows = $stmt->fetchAll();

					foreach ($rows as $row) {
						echo "<div class='itemListElement'>";
						echo "<div class = 'item_details'>";
						echo "<div>";
						echo $row['service_name'];
						echo "</div>";
						echo "<div class = 'item_select_part'>";
						echo "<span class = 'service_duration_field'>";
						echo $row['service_duration'] . " min";
						echo "</span>";
						echo "<div class = 'service_price_field'>";
						echo "<span style = 'font-weight: bold;'>";
						echo $row['service_price'] . "$";
						echo "</span>";
						echo "</div>";
						?>
						<div class="select_item_bttn">
							<div class="btn-group-toggle" data-toggle="buttons">
								<label class="service_label item_label btn btn-secondary">
									<input type="checkbox" name="selected_services[]"
										value="<?php echo $row['service_id'] ?>" autocomplete="off">Fijar
								</label>
							</div>
						</div>
						<?php
						echo "</div>";
						echo "</div>";
						echo "</div>";
					}
					?>
				</div>
			</div>

			<!-- SELECT EMPLOYEE -->

			<div class="select_employee_div tab_reservation" id="employees_tab">

				<!-- ALERT MESSAGE -->

				<div class="alert alert-danger" role="alert" style="display: none">
					Por favor, seleccione un@ Peluquer@!
				</div>

				<div class="text_header">
					<span>
						2. Elección del peluquer@
					</span>
				</div>

				<!-- EMPLOYEES TAB -->

				<div class="btn-group-toggle" data-toggle="buttons">
					<div class="items_tab">
						<?php
						$stmt = $con->prepare("Select * from employees WHERE tenant_id = ?");
						$stmt->execute(array($tenant_id));
						$rows = $stmt->fetchAll();

						foreach ($rows as $row) {
							echo "<div class='itemListElement'>";
							echo "<div class = 'item_details'>";

							// Image Display
							$img = !empty($row['image']) ? $row['image'] : 'Design/images/default_employee.png';
							echo "<div class='text-center mb-2'>";
							echo "<img src='" . $img . "' style='width: 80px; height: 80px; border-radius: 50%; object-fit: cover;'>";
							echo "</div>";

							echo "<div>";
							echo $row['first_name'] . " " . $row['last_name'];
							echo "</div>";
							echo "<div class = 'item_select_part'>";
							?>
							<div class="select_item_bttn">
								<label class="item_label btn btn-secondary">
									<input type="radio" class="radio_employee_select" name="selected_employee"
										value="<?php echo $row['employee_id'] ?>">Seleccionar
								</label>
							</div>
							<?php
							echo "</div>";
							echo "</div>";
							echo "</div>";
						}
						?>
					</div>
				</div>
			</div>


			<!-- SELECT DATE TIME -->

			<div class="select_date_time_div tab_reservation" id="calendar_tab">

				<!-- ALERT MESSAGE -->

				<div class="alert alert-danger" role="alert" style="display: none">
					Por favor, selecciona hora de tu reserva!
				</div>

				<div class="text_header">
					<span>
						3. Elección de fecha y hora:
					</span>
				</div>

				<div class="calendar_tab" style="overflow-x: auto;overflow-y: visible;" id="calendar_tab_in">
					<div id="calendar_loading">
						<img src="Design/images/ajax_loader_gif.gif"
							style="display: block;margin-left: auto;margin-right: auto;">
					</div>
				</div>

			</div>


			<!-- CLIENT DETAILS -->

			<div class="client_details_div tab_reservation" id="client_tab">

				<div class="text_header">
					<span>
						4. Confirmación de Cita:
					</span>
				</div>

				<div>
					<div class="alert alert-info">
						<strong>¡Hola, <?php echo $current_client['first_name']; ?>!</strong><br>
						Ya tenemos tus datos registrados. Por favor, confirma tu cita a continuación.
					</div>
					<div class="form-group colum-row row" style="display: none;">
						<div class="col-sm-6">
							<input type="text" name="client_first_name" id="client_first_name" class="form-control"
								placeholder="Nombre" value="<?php echo $current_client['first_name']; ?>" readonly>
							<span class="invalid-feedback">Este campo es requerido</span>
						</div>
						<div class="col-sm-6">
							<input type="text" name="client_last_name" id="client_last_name" class="form-control"
								placeholder="Apellido" value="<?php echo $current_client['last_name']; ?>" readonly>
							<span class="invalid-feedback">Este campo es requerido</span>
						</div>
						<div class="col-sm-6">
							<input type="email" name="client_email" id="client_email" class="form-control"
								placeholder="Correo" value="<?php echo $current_client['client_email']; ?>" readonly>
							<span class="invalid-feedback">Dirección de Correo Inválido</span>
						</div>
						<div class="col-sm-6">
							<input type="text" name="client_phone_number" id="client_phone_number" class="form-control"
								placeholder="Teléfono Móvil" value="<?php echo $current_client['phone_number']; ?>"
								readonly>
							<span class="invalid-feedback">Número de Teléfono Inválido</span>
						</div>
					</div>

					<div class="card bg-light mb-3">
						<div class="card-body">
							<h5 class="card-title">Resumen de Datos</h5>
							<p class="card-text">
								<strong>Nombre:</strong>
								<?php echo $current_client['first_name'] . ' ' . $current_client['last_name']; ?><br>
								<strong>Email:</strong> <?php echo $current_client['client_email']; ?><br>
								<strong>Teléfono:</strong> <?php echo $current_client['phone_number']; ?>
							</p>
							<p class="text-muted small">Si desea cambiar estos datos, por favor contacte a la
								administración o edite su perfil.</p>
						</div>
					</div>

				</div>
			</div>




			<!-- NEXT AND PREVIOUS BUTTONS -->

			<div style="overflow:auto;padding: 30px 0px;">
				<div style="float:right;">
					<input type="hidden" name="submit_book_appointment_form">
					<button type="button" id="prevBtn" class="next_prev_buttons" style="background-color: #bbbbbb;"
						onclick="nextPrev(-1)">Previo</button>
					<button type="button" id="nextBtn" class="next_prev_buttons" onclick="nextPrev(1)">Próximo</button>
				</div>
			</div>

			<!-- Circles which indicates the steps of the form: -->

			<div style="text-align:center;margin-top:40px;">
				<span class="step"></span>
				<span class="step"></span>
				<span class="step"></span>
				<span class="step"></span>
			</div>

		</form>
	</div>
</section>



<!-- FOOTER BOTTOM -->

<?php include "Includes/templates/footer.php"; ?>