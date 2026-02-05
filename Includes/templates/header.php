<!DOCTYPE html>
<html lang="en">

<!-- HEAD -->

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0" />
	<meta name="description" content="Gold Luk Barbershop">
	<title>Gold Luk Barbershop</title>

	<!-- EXTERNAL CSS LINKS -->
	<link rel="shortcut icon" href="./faviconconfiguroweb.png" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="Design/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="Design/fonts/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="Design/css/main.css">
	<link rel="stylesheet" type="text/css" href="Design/css/responsive.css">
	<link rel="stylesheet" type="text/css" href="Design/css/barber-icons.css">

	<!-- GOOGLE FONTS -->

	<link
		href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
		rel="stylesheet">
	<link
		href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;1,100;1,200;1,300;1,400;1,500&display=swap"
		rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Prata&display=swap" rel="stylesheet">

	<!-- DYNAMIC STYLES FROM SETTINGS -->
	<style>
		:root {
			--gold:
				<?php echo isset($settings['primary_color']) ? $settings['primary_color'] : '#D4AF37'; ?>
			;
			--dark:
				<?php echo isset($settings['secondary_color']) ? $settings['secondary_color'] : '#111111'; ?>
			;
			--nav-bg:
				<?php echo isset($settings['navbar_bg_color']) ? $settings['navbar_bg_color'] : '#111111'; ?>
			;
			--footer-bg:
				<?php echo isset($settings['footer_bg_color']) ? $settings['footer_bg_color'] : '#111111'; ?>
			;
			--body-bg:
				<?php echo isset($settings['background_color']) ? $settings['background_color'] : '#ffffff'; ?>
			;
			--text-color:
				<?php echo isset($settings['text_color']) ? $settings['text_color'] : '#333333'; ?>
			;
		}

		body {
			background-color: var(--body-bg) !important;
			color: var(--text-color) !important;
		}

		.header-section {
			background-color: var(--nav-bg) !important;
		}

		.footer-section {
			background-color: var(--footer-bg) !important;
		}

		/* Overrides for Gold Theme */
		.text-gold,
		.section-title h2,
		.hero-title span {
			color: var(--gold) !important;
		}

		.bg-gold,
		.btn-gold,
		.divider {
			background-color: var(--gold) !important;
		}

		.btn-gold {
			border-color: var(--gold) !important;
		}
	</style>

</head>

<!-- BODY -->

<body>