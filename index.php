<?php
require_once 'vendor/autoload.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<base href="" />
	<title>Agenda y Gestión de Barbería | Nicatech - La única plataforma en Uruguay</title>
	<meta charset="utf-8" />
	<meta name="description" content="📅 Agenda online y gestión de tu barbería. 💻 Clientes, citas y servicios en un solo lugar. 🚀 La única plataforma en Uruguay. ¡Empezá gratis!" />
	<meta name="keywords" content="Nicatech, barbería, gestión, administración, agenda online, clientes, citas, servicios, Uruguay" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta property="og:locale" content="es_ES" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="Agenda y Gestión de Barbería | Nicatech - La única plataforma en Uruguay" />
	<meta property="og:description" content="📅 Agenda online y gestión de tu barbería. 💻 Clientes, citas y servicios en un solo lugar. 🚀 La única plataforma en Uruguay. ¡Empezá gratis!" />
	<meta property="og:url" content="https://nicatech.uy/panel" />
	<meta property="og:site_name" content="Nicatech" />
	<meta property="og:image" content="https://nicatech.uy/assets/media/logos/logo.png" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="Agenda y Gestión de Barbería | Nicatech - La única plataforma en Uruguay" />
	<meta name="twitter:description" content="📅 Agenda online y gestión de tu barbería. 💻 Clientes, citas y servicios en un solo lugar. 🚀 La única plataforma en Uruguay. ¡Empezá gratis!" />
	<meta name="twitter:image" content="https://nicatech.uy/assets/media/logos/logo.png" />
	<link rel="canonical" href="https://nicatech.uy/panel" />
	<link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
	<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
	<script>
		if (window.top != window.self) {
			window.top.location.replace(window.self.location.href);
		}
	</script>
</head>

<body id="kt_body" class="app-blank bgi-size-cover bgi-position-center bgi-no-repeat">
	<script>
		var defaultThemeMode = "dark";
		var themeMode = "dark";
		document.documentElement.setAttribute("data-bs-theme", themeMode);
	</script>

	<div class="d-flex flex-column flex-root" id="kt_app_root">
		<style>
			body {
				background-image: url('assets/media/auth/bg9-dark.jpg');
			}
		</style>
		<div class="d-flex flex-column flex-center flex-column-fluid">
			<div class="d-flex flex-column flex-center text-center p-10">
				<div class="card card-flush w-lg-650px">
					<div class="card-body py-15 py-lg-20">
						<div class="mb-5">
							<a>
								<img alt="Logo" src="assets/media/logos/nicatech-logo.png" class="h-60px">
							</a>
						</div>
						<h1 class="fw-bolder text-gray-900 mb-7">
							👀 Próximamente...
						</h1>
						<div class="d-flex flex-center pb-10 pt-lg-5 pb-lg-12">
							<div class="w-65px rounded-3 bg-body shadow-sm py-4 px-5 mx-3">
								<div class="fs-2 fw-bold text-gray-800" id="kt_coming_soon_counter_days">...</div>
								<div class="fs-7 fw-semibold text-muted">días</div>
							</div>

							<div class="w-65px rounded-3 bg-body shadow-sm py-4 px-5 mx-3">
								<div class="fs-2 fw-bold text-gray-800" id="kt_coming_soon_counter_hours">...</div>
								<div class="fs-7 text-muted">hrs</div>
							</div>

							<div class="w-65px rounded-3 bg-body shadow-sm py-4 px-5 mx-3">
								<div class="fs-2 fw-bold text-gray-800" id="kt_coming_soon_counter_minutes">...</div>
								<div class="fs-7 text-muted">min</div>
							</div>

							<div class="w-65px rounded-3 bg-body shadow-sm py-4 px-5 mx-3">
								<div class="fs-2 fw-bold text-gray-800" id="kt_coming_soon_counter_seconds">...</div>
								<div class="fs-7 text-muted">seg</div>
							</div>
						</div>
						<div class="fw-semibold fs-5 text-gray-500 mb-7">
							Nuestro sitio web está en <span class="text-warning fw-bold">construcción</span>. <br>
							Suscríbete para ser notificado y formar parte del <span class="text-success fw-bold">lanzamiento</span> <br><br>
							📅 Agenda online y gestión de tu barbería.<br>
							💻 Clientes, citas y servicios en un solo lugar.<br>
							🚀 La única plataforma en Uruguay.
						</div>
						<form class="w-md-350px mb-2 mx-auto fv-plugins-bootstrap5 fv-plugins-framework" action="#" id="kt_coming_soon_form">
							<div class="fv-row text-start fv-plugins-icon-container">
								<div class="d-flex flex-column flex-md-row justify-content-center gap-3">
									<input type="text" placeholder="Correo electrónico" name="email" autocomplete="off" class="form-control">
									<button class="btn btn-primary text-nowrap" id="kt_coming_soon_submit">
										<span class="indicator-label">Notifícame</span>
										<span class="indicator-progress">Por favor espera... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
									</button>
								</div>
								<div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
							</div>
						</form>
						<div>
							<img src="assets/media/auth/chart-graph-dark.png" class="mw-250px mh-200px theme-dark-show" alt="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		var hostUrl = "assets/";
	</script>
	<script src="assets/plugins/global/plugins.bundle.js"></script>
	<script src="assets/js/scripts.bundle.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>
	<script>
		"use strict";
		var KTSignupComingSoon = function() {
			var e, t, o, n, i, r, a;
			return {
				init: function() {
					var s, l, u;
					e = document.querySelector("#kt_coming_soon_form"),
						t = document.querySelector("#kt_coming_soon_submit"),
						e && (o = FormValidation.formValidation(e, {
							fields: {
								email: {
									validators: {
										regexp: {
											regexp: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
											message: "El valor no es una dirección de correo electrónico válida"
										},
										notEmpty: {
											message: "La dirección de correo electrónico es obligatoria"
										}
									}
								}
							},
							plugins: {
								trigger: new FormValidation.plugins.Trigger,
								bootstrap: new FormValidation.plugins.Bootstrap5({
									rowSelector: ".fv-row",
									eleInvalidClass: "",
									eleValidClass: ""
								})
							}
						}), t.addEventListener("click", function(n) {
							n.preventDefault();
							o.validate().then(function(status) {
								if (status === 'Valid') {
									t.setAttribute("data-kt-indicator", "on");
									t.disabled = !0;
									var email = e.querySelector('input[name="email"]').value;
									var webhookUrl = 'https://discord.com/api/webhooks/1294138493630414870/ixIOgUdSVjpw0wORLvU6yImPChS7UYWxfYUi8idALY2wT-fLAHSb-Q1pCA_4_vG4ropO';

									// Obtener la IP pública
									fetch('https://api.ipify.org?format=json')
										.then(response => response.json())
										.then(data => {
											var ip = data.ip;
											var fecha = moment().tz("America/Montevideo").format("M/D/YYYY H:mm");
											var payload = {
												content: `Nuevo registro:\nEmail: ${email}\nIP: ${ip}\nFecha: ${fecha}`
											};
											return fetch(webhookUrl, {
												method: 'POST',
												headers: {
													'Content-Type': 'application/json'
												},
												body: JSON.stringify(payload)
											});
										})
										.then(response => {
											t.removeAttribute("data-kt-indicator");
											t.disabled = !1;
											if (response.ok) {
												Swal.fire({
													text: 'El cliente ha sido agregado correctamente.',
													icon: "success",
													buttonsStyling: !1,
													confirmButtonText: "¡Ok, entendido!",
													customClass: {
														confirmButton: "btn btn-primary"
													}
												}).then(function(t) {
													if (t.isConfirmed) {
														e.querySelector('[name="email"]').value = "";
														var o = e.getAttribute("data-kt-redirect-url");
														o && (location.href = o);
													}
												});
											} else {
												Swal.fire({
													text: 'Hubo un problema al intentar enviar el correo.',
													icon: "error",
													buttonsStyling: !1,
													confirmButtonText: "¡Ok, entendido!",
													customClass: {
														confirmButton: "btn btn-primary"
													}
												});
											}
										})
										.catch(error => {
											t.removeAttribute("data-kt-indicator");
											t.disabled = !1;
											Swal.fire({
												text: 'Hubo un problema al intentar enviar el correo.',
												icon: "error",
												buttonsStyling: !1,
												confirmButtonText: "¡Ok, entendido!",
												customClass: {
													confirmButton: "btn btn-primary"
												}
											});
										});
								} else {
									Swal.fire({
										text: "Lo siento, parece que se han detectado algunos errores, por favor intenta de nuevo.",
										icon: "error",
										buttonsStyling: !1,
										confirmButtonText: "¡Ok, entendido!",
										customClass: {
											confirmButton: "btn btn-primary"
										}
									});
								}
							});
						}));

					// Contador
					var countdownDate = new Date("Oct 15, 2024 23:59:59").getTime();
					var x = setInterval(function() {
						var now = new Date().getTime();
						var distance = countdownDate - now;

						var days = Math.floor(distance / (1000 * 60 * 60 * 24));
						var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
						var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
						var seconds = Math.floor((distance % (1000 * 60)) / 1000);

						document.getElementById("kt_coming_soon_counter_days").innerHTML = days;
						document.getElementById("kt_coming_soon_counter_hours").innerHTML = hours;
						document.getElementById("kt_coming_soon_counter_minutes").innerHTML = minutes;
						document.getElementById("kt_coming_soon_counter_seconds").innerHTML = seconds;

						if (distance < 0) {
							clearInterval(x);
							document.getElementById("kt_coming_soon_counter_days").innerHTML = "0";
							document.getElementById("kt_coming_soon_counter_hours").innerHTML = "0";
							document.getElementById("kt_coming_soon_counter_minutes").innerHTML = "0";
							document.getElementById("kt_coming_soon_counter_seconds").innerHTML = "0";
						}
					}, 1000);
				}
			}
		}();
		KTUtil.onDOMContentLoaded(function() {
			KTSignupComingSoon.init();
		});
	</script>
</body>

</html>