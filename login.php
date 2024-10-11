<?php
// index.php
session_start();

require_once 'vendor/autoload.php';

use App\Auth;

$auth = new Auth();

// Si ya está logueado, redirigir a la página de inicio
if ($auth->isLogged()) {
	header('Location: index');
	exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<base href="" />
	<title>Iniciar sesión</title>
	<meta charset="utf-8" />
	<meta name="description" content="Página de inicio de sesión del Panel de Gestión de Barbería de NICATECH" />
	<meta name="keywords" content="Nicatech, Barbería, Gestión, Panel de Control, Login" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta property="og:locale" content="es_ES" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="Panel de Gestión de Barbería - NICATECH" />
	<meta property="og:description" content="Página de inicio de sesión del Panel de Gestión de Barbería de NICATECH" />
	<meta property="og:url" content="https://nicatech.com/panel" />
	<meta property="og:site_name" content="Nicatech" />
	<link rel="canonical" href="https://nicatech.com/panel/login" />
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

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
	data-kt-app-header-fixed-mobile="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true"
	data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
	data-kt-app-sidebar-push-footer="true" class="app-default">
	<script>
		var defaultThemeMode = "light";
		var themeMode;
		if (document.documentElement) {
			if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
				themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
			} else {
				if (localStorage.getItem("data-bs-theme") !== null) {
					themeMode = localStorage.getItem("data-bs-theme");
				} else {
					themeMode = defaultThemeMode;
				}
			}
			if (themeMode === "system") {
				themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
			}
			document.documentElement.setAttribute("data-bs-theme", themeMode);
		}
	</script>

	<div class="d-flex flex-column flex-root" id="kt_app_root">
		<style>
			body {
				background-image: url('assets/media/auth/bg4.jpg');
			}

			[data-bs-theme="dark"] body {
				background-image: url('assets/media/auth/bg4-dark.jpg');
			}
		</style>
		<div class="d-flex flex-column flex-lg-row flex-column-fluid">
			<div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
				<div class="d-flex flex-center flex-column flex-lg-row-fluid">
					<div class="w-lg-500px p-10">
						<form class="form w-100" novalidate="novalidate" id="kt_sign_in_form">
							<div class="text-center mb-11">
								<h1 class="text-gray-900 fw-bolder mb-3">Iniciar sesión</h1>
								<div class="text-gray-500 fw-semibold fs-6">Ingrese sus credenciales para acceder al panel de control</div>
							</div>
							<div class="fv-row mb-8">
								<input type="text" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" value="agus@nicatech.uy" />
							</div>
							<div class="fv-row mb-3">
								<input type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent" value="agusnicatechadmin" />
							</div>
							<div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
								<div></div>
								<a href="mailto:admin@nicatech.uy" class="link-primary">¿Olvidaste tu contraseña?</a>
							</div>
							<div class="d-grid mb-10">
								<button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
									<span class="indicator-label">Iniciar sesión</span>
									<span class="indicator-progress">Iniciando sesión... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
									</span>
								</button>
							</div>
							<div class="text-gray-500 text-center fw-semibold fs-6">
								¿No estás registrado?
								<a href="https://api.whatsapp.com/send?phone=59894306272&text=Hola,%20quiero%20solicitar%20una%20prueba%20gratuita" target="_blank" class="link-primary">
									Solicitar prueba gratuita
								</a>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2" style="background-image: url(assets/media/misc/auth-bg.png)">
				<div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
					<a href="index.html" class="mb-0 mb-lg-12">
						<img alt="Logo" src="assets/media/logos/default-dark.svg" class="h-60px h-lg-75px" />
					</a>
					<img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20" src="assets/media/misc/auth-screens.png" alt="" />
					<h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">NICATECH</h1>
					<div class="d-none d-lg-block text-white fs-base text-center">
						En NICATECH, nos dedicamos a brindar soluciones tecnológicas innovadoras <br />
						que transforman la manera en que trabajas y vives. <br />
						Descubre cómo nuestras soluciones pueden ayudarte a alcanzar tus objetivos.
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
	<script src="assets/js/widgets.bundle.js"></script>
	<script src="assets/js/custom/widgets.js"></script>
	<script>
		// Element to indicate
		var button = document.querySelector("#kt_sign_in_submit");

		// Handle form submit event
		document.getElementById('kt_sign_in_form').addEventListener('submit', function(event) {
			event.preventDefault(); // Evitar el envío normal del formulario

			// Activate indicator
			button.setAttribute("data-kt-indicator", "on");
			button.disabled = true; // Deshabilitar el botón

			// Recolectar los datos del formulario
			var formData = new FormData(this);

			// Enviar la solicitud AJAX
			fetch('api/login', {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					toastr.options = {
						"closeButton": true,
						"debug": false,
						"newestOnTop": false,
						"progressBar": true,
						"positionClass": "toastr-top-right",
						"preventDuplicates": false,
						"onclick": null,
						"showDuration": "300",
						"hideDuration": "1000",
						"timeOut": "2000",
						"extendedTimeOut": "1000",
						"showEasing": "swing",
						"hideEasing": "linear",
						"showMethod": "fadeIn",
						"hideMethod": "fadeOut"
					};

					if (data.success) {
						// Mostrar mensaje de éxito
						toastr.success("Inicio de sesión exitoso", "Éxito");

						// Redirigir después de un pequeño retraso
						setTimeout(() => {
							window.location.href = 'index'; // Redirigir a la página principal
						}, 2000); // 2 segundos de retraso
					} else {
						// Mostrar mensaje de error
						toastr.error('Error: ' + data.message, "Error");
					}
				})
				.catch(error => {
					toastr.error(`Hubo un error al intentar iniciar sesión. ${error}`, "Error");
				})
				.finally(() => {
					// Disable indicator after 3 seconds
					setTimeout(function() {
						button.removeAttribute("data-kt-indicator");
						button.disabled = false; // Habilitar el botón
					}, 3000);
				});
		});
	</script>
</body>

</html>