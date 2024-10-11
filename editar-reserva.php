<?php
// ver-reserva.php?id=1

// Configuración inicial
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicio de sesión y autenticación
session_start();
require_once 'vendor/autoload.php';

use App\Auth;
use App\Reserva;
use App\Cliente;
use App\Barberia;
use App\Servicio;
use App\Barbero;

// Autenticación del usuario
$auth = new Auth();
if (!$auth->isLogged()) {
	header('Location: login');
	exit();
}

// Carga de clases necesarias
$reserva = new Reserva();
$cliente = new Cliente();
$barberia = new Barberia();
$servicio = new Servicio();
$barbero = new Barbero();

// Datos del usuario
$idSession = $_SESSION['user'];
$nombreYApellido = $auth->obtenerNombreYApellidoPorId($idSession);
$correoElectronico = $auth->obtenerEmailPorId($idSession);

// Obtener ID de la reserva a ver
$idReserva = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idReserva <= 0) {
	echo "ID de reserva no válido.";
	exit();
}

// Obtener datos de la reserva
$datosReserva = $reserva->obtenerReservaPorId($idReserva);
if (!$datosReserva) {
	echo "Reserva no encontrada.";
	exit();
}

// Obtener datos del cliente
$datosCliente = $cliente->obtenerClientePorId($datosReserva['cliente_id']);
if (!$datosCliente) {
	echo "Cliente no encontrado.";
	exit();
}

// Obtener datos de la barbería
$datosBarberia = $barberia->obtenerBarberia();
if (!$datosBarberia) {
	echo "Barbería no encontrada.";
	exit();
}

// Obtener datos del servicio
$datosServicio = $servicio->obtenerServicioPorId($datosReserva['servicio_id']);
if (!$datosServicio) {
	echo "Servicio no encontrado.";
	exit();
}

$todosLosServicios = $servicio->ListarServicios();

$barberoId = $datosReserva['barbero_id'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<base href="" />
	<title>Panel - Editando reserva</title>
	<meta charset="utf-8" />
	<meta name="description" content="Panel de gestión de barbería de NICATECH" />
	<meta name="keywords" content="NICATECH, barbería, gestión, administración" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta property="og:locale" content="es_ES" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="Panel de Gestión de Barbería | NICATECH" />
	<meta property="og:description" content="Panel de gestión de barbería de NICATECH" />
	<meta property="og:url" content="https://nictech.uy/panel" />
	<meta property="og:site_name" content="NICATECH" />
	<link rel="canonical" href="https://nictech.uy/panel" />
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

	<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
		<div class="app-page  flex-column flex-column-fluid " id="kt_app_page">
			<div id="kt_app_header" class="app-header " data-kt-sticky="true"
				data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize"
				data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false">
				<div class="app-container  container-fluid d-flex align-items-stretch justify-content-between"
					id="kt_app_header_container">
					<div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
						<div class="btn btn-icon btn-active-color-primary w-35px h-35px"
							id="kt_app_sidebar_mobile_toggle">
							<i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
								<span class="path1"></span>
								<span class="path2"></span>
							</i>
						</div>
					</div>
					<div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
						<a href="index" class="d-lg-none">
							<img alt="Logo" src="assets/media/logos/default-small.svg" class="h-30px" />
						</a>
					</div>
					<ol class="breadcrumb breadcrumb-line text-muted fs-6 fw-semibold">
						<li class="breadcrumb-item"><a href="#" class="">Inicio</a></li>
						<li class="breadcrumb-item"><a href="#" class="">Gestión</a></li>
						<li class="breadcrumb-item text-muted">Editando reserva #<?php echo $idReserva; ?></li>
					</ol>
					<div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1"
						id="kt_app_header_wrapper">
						<div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true"
							data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}"
							data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end"
							data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true"
							data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
							data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
						</div>
						<div class="app-navbar flex-shrink-0">
							<div class="app-navbar-item ms-1 ms-md-4">
								<a href="#"
									class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px"
									data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent"
									data-kt-menu-placement="bottom-end">
									<i class="ki-duotone ki-night-day theme-light-show fs-1">
										<span class="path1"></span>
										<span class="path2"></span>
										<span class="path3"></span>
										<span class="path4"></span>
										<span class="path5"></span>
										<span class="path6"></span>
										<span class="path7"></span>
										<span class="path8"></span>
										<span class="path9"></span>
										<span class="path10"></span>
									</i>
									<i class="ki-duotone ki-moon theme-dark-show fs-1">
										<span class="path1"></span>
										<span class="path2"></span>
									</i>
								</a>
								<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
									data-kt-menu="true" data-kt-element="theme-mode-menu">
									<div class="menu-item px-3 my-0">
										<a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
											data-kt-value="light">
											<span class="menu-icon" data-kt-element="icon">
												<i class="ki-duotone ki-night-day fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
													<span class="path5"></span>
													<span class="path6"></span>
													<span class="path7"></span>
													<span class="path8"></span>
													<span class="path9"></span>
													<span class="path10"></span>
												</i>
											</span>
											<span class="menu-title">
												Claro
											</span>
										</a>
									</div>
									<div class="menu-item px-3 my-0">
										<a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
											data-kt-value="dark">
											<span class="menu-icon" data-kt-element="icon">
												<i class="ki-duotone ki-moon fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</span>
											<span class="menu-title">
												Oscuro
											</span>
										</a>
									</div>
									<div class="menu-item px-3 my-0">
										<a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
											data-kt-value="system">
											<span class="menu-icon" data-kt-element="icon">
												<i class="ki-duotone ki-screen fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
												</i>
											</span>
											<span class="menu-title">
												Sistema
											</span>
										</a>
									</div>
								</div>
							</div>
							<div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
								<div class="cursor-pointer symbol symbol-35px"
									data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
									data-kt-menu-placement="bottom-end">
									<img src="assets/media/avatars/300-3.jpg" class="rounded-3" alt="user" />
								</div>
								<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
									data-kt-menu="true">
									<div class="menu-item px-3">
										<div class="menu-content d-flex align-items-center px-3">
											<div class="symbol symbol-50px me-5">
												<img alt="Logo" src="assets/media/avatars/300-3.jpg" />
											</div>
											<div class="d-flex flex-column">
												<div class="fw-bold d-flex align-items-center fs-5"><?php echo $nombreYApellido; ?>
													<!-- <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">Membresia</span> -->
												</div>
												<a class="fw-semibold text-muted text-hover-primary fs-7"><?php echo $correoElectronico; ?></a>
											</div>
										</div>
									</div>
									<div class="separator my-2"></div>
									<div class="menu-item px-5 my-1">
										<a href="configuracion" class="menu-link px-5">
											Configuración de la cuenta
										</a>
									</div>
									<div class="menu-item px-5">
										<a href="./functions/logout"
											class="menu-link px-5 text-danger">
											Cerrar sesión
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="app-wrapper flex-column flex-row-fluid " id="kt_app_wrapper">
				<div id="kt_app_sidebar" class="app-sidebar  flex-column " data-kt-drawer="true"
					data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}"
					data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start"
					data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
					<div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
						<a href="index">
							<img alt="Logo" src="assets/media/logos/default-dark.svg"
								class="h-25px app-sidebar-logo-default" />
							<img alt="Logo" src="assets/media/logos/default-small.svg"
								class="h-20px app-sidebar-logo-minimize" />
						</a>
						<div id="kt_app_sidebar_toggle"
							class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate "
							data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
							data-kt-toggle-name="app-sidebar-minimize">
							<i class="ki-duotone ki-black-left-line fs-3 rotate-180">
								<span class="path1"></span>
								<span class="path2"></span>
							</i>
						</div>
					</div>
					<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
						<div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
							<div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true"
								data-kt-scroll-activate="true" data-kt-scroll-height="auto"
								data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
								data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
								data-kt-scroll-save-state="true">
								<div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6"
									id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
									<!-- Menú Principal -->
									<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
										<span class="menu-link">
											<span class="menu-icon">
												<i class="ki-duotone ki-home fs-2"></i>
											</span>
											<span class="menu-title">Menú Principal</span>
											<span class="menu-arrow"></span>
										</span>
										<div class="menu-sub menu-sub-accordion">
											<div class="menu-item">
												<a class="menu-link" href="index">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
													<span class="menu-title">Inicio</span>
												</a>
											</div>
										</div>
									</div>
									<!-- Gestión de Clientes -->
									<div class="menu-item pt-5">
										<div class="menu-content">
											<span class="menu-heading fw-bold text-uppercase fs-7">Gestión de Clientes</span>
										</div>
									</div>
									<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
										<span class="menu-link">
											<span class="menu-icon">
												<i class="ki-duotone ki-people fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
													<span class="path5"></span>
												</i>
											</span>
											<span class="menu-title">Clientes</span>
											<span class="menu-arrow"></span>
										</span>
										<div class="menu-sub menu-sub-accordion">
											<div class="menu-item">
												<a class="menu-link" href="./listado-clientes">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
													<span class="menu-title">Listar los Clientes</span>
												</a>
											</div>
										</div>
									</div>
									<!-- Gestión de Reservas -->
									<div class="menu-item pt-5">
										<div class="menu-content">
											<span class="menu-heading fw-bold text-uppercase fs-7">Gestión de Reservas</span>
										</div>
									</div>
									<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
										<span class="menu-link">
											<span class="menu-icon">
												<i class="ki-duotone ki-calendar fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</span>
											<span class="menu-title">Reservas</span>
											<span class="menu-arrow"></span>
										</span>
										<div class="menu-sub menu-sub-accordion">
											<div class="menu-item">
												<a class="menu-link" href="./listado-reservas">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
													<span class="menu-title">Listar las Reservas</span>
												</a>
											</div>
											<div class="menu-item">
												<a class="menu-link" href="./calendario">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
													<span class="menu-title">Calendario</span>
												</a>
											</div>
										</div>
									</div>
									<!-- Gestión de Servicios -->
									<div class="menu-item pt-5">
										<div class="menu-content">
											<span class="menu-heading fw-bold text-uppercase fs-7">Gestión de Servicios</span>
										</div>
									</div>
									<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
										<span class="menu-link">
											<span class="menu-icon">
												<i class="ki-duotone ki-basket fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
													<span class="path5"></span>
												</i>
											</span>
											<span class="menu-title">Servicios</span>
											<span class="menu-arrow"></span>
										</span>
										<div class="menu-sub menu-sub-accordion">
											<div class="menu-item">
												<a class="menu-link" href="./listado-servicios">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
													<span class="menu-title">Listar los Servicios</span>
												</a>
											</div>
										</div>
									</div>
									<!-- Reportes -->
									<div class="menu-item pt-5">
										<div class="menu-content">
											<span class="menu-heading fw-bold text-uppercase fs-7">Reportes</span>
										</div>
									</div>
									<div class="menu-item">
										<a class="menu-link" href="./reportes">
											<span class="menu-icon">
												<i class="ki-duotone ki-graph-up fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
													<span class="path5"></span>
													<span class="path6"></span>
												</i>
											</span>
											<span class="menu-title">Ver Reportes</span>
										</a>
									</div>
									<!-- Configuración -->
									<div class="menu-item pt-5">
										<div class="menu-content">
											<span class="menu-heading fw-bold text-uppercase fs-7">Configuración</span>
										</div>
									</div>
									<div class="menu-item">
										<a class="menu-link" href="./configuracion">
											<span class="menu-icon">
												<i class="ki-duotone ki-setting-2 fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</span>
											<span class="menu-title">Ajustes</span>
										</a>
									</div>
									<!-- Cuenta -->
									<div class="menu-item pt-5">
										<div class="menu-content">
											<span class="menu-heading fw-bold text-uppercase fs-7">Cuenta</span>
										</div>
									</div>
									<div class="menu-item">
										<a class="menu-link" href="./functions/logout">
											<span class="menu-icon">
												<i class="ki-duotone ki-cross-square fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</span>
											<span class="menu-title">Cerrar sesión</span>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
					<div class="d-flex flex-column flex-column-fluid">
						<div id="kt_app_content" class="app-content flex-column-fluid" bis_skin_checked="1">
							<div id="kt_app_content_container" class="app-container container-xxl " bis_skin_checked="1">
								<div class="card" bis_skin_checked="1">
									<div class="card-body p-lg-20" bis_skin_checked="1">
										<div class="d-flex flex-column flex-xl-row" bis_skin_checked="1">
											<div class="flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0" bis_skin_checked="1">
												<div class="mt-n1" bis_skin_checked="1">
													<div class="d-flex flex-stack pb-10" bis_skin_checked="1">
														<a href="#">
															<img alt="Logo" src="assets/media/svg/brand-logos/code-lab.svg">
														</a>
													</div>
													<div class="m-0" bis_skin_checked="1">
														<div class="fw-bold fs-3 text-gray-800 mb-8" bis_skin_checked="1">Factura #<?php echo $datosReserva['id']; ?></div>
														<div class="row g-5 mb-11" bis_skin_checked="1">
															<div class="col-sm-6" bis_skin_checked="1">
																<div class="fw-semibold fs-7 text-gray-600 mb-1" bis_skin_checked="1">Fecha de la orden:</div>
																<div class="fw-bold fs-6 text-gray-800" bis_skin_checked="1"><?php echo $datosReserva['fecha_emision']; ?></div>
															</div>
															<div class="col-sm-6" bis_skin_checked="1">
																<div class="fw-semibold fs-7 text-gray-600 mb-1" bis_skin_checked="1">Turno reservado:</div>
																<div class="fw-bold fs-6 text-gray-800 d-flex align-items-center flex-wrap" bis_skin_checked="1">
																	<span class="pe-2">
																		<?php echo $datosReserva['fecha_reservada_texto']; ?>
																	</span>
																</div>
															</div>
														</div>
														<div class="row g-5 mb-12" bis_skin_checked="1">
															<div class="col-sm-6" bis_skin_checked="1">
																<div class="fw-semibold fs-7 text-gray-600 mb-1" bis_skin_checked="1">Facturado a:</div>
																<div class="fw-bold fs-6 text-gray-800" bis_skin_checked="1"><?php echo htmlspecialchars($datosCliente['nombre']); ?></div>
																<div class="fw-semibold fs-7 text-gray-600" bis_skin_checked="1">
																	<?php echo htmlspecialchars($datosCliente['email']); ?><br>
																	<?php echo htmlspecialchars($datosCliente['telefono']); ?>
																</div>
															</div>
															<div class="col-sm-6" bis_skin_checked="1">
																<div class="fw-semibold fs-7 text-gray-600 mb-1" bis_skin_checked="1">Facturado por:</div>
																<div class="fw-bold fs-6 text-gray-800" bis_skin_checked="1"><?php echo htmlspecialchars($datosBarberia['nombre']); ?></div>
																<div class="fw-semibold fs-7 text-gray-600" bis_skin_checked="1">
																	<?php echo htmlspecialchars($datosBarberia['direccion']); ?><br>
																	<?php echo htmlspecialchars($datosBarberia['telefono']); ?>
																</div>
															</div>
														</div>
														<div class="flex-grow-1" bis_skin_checked="1">
															<div class="table-responsive border-bottom mb-9" bis_skin_checked="1">
																<table class="table mb-3">
																	<thead>
																		<tr class="border-bottom fs-6 fw-bold text-muted">
																			<th class="min-w-175px pb-2">Servicio</th>
																			<th class="min-w-70px text-end pb-2">Duración aproximada</th>
																			<th class="min-w-70px text-end pb-2">Barbero asignado</th>
																			<th class="min-w-100px text-end pb-2">Monto</th>
																		</tr>
																	</thead>
																	<tbody>
																		<tr class="fw-bold text-gray-700 fs-5 text-end">
																			<td class="d-flex align-items-center pt-6">
																				<i class="fa fa-genderless text-danger fs-2 me-2"></i>
																				<?php echo htmlspecialchars($datosServicio['descripcion']); ?>
																			</td>
																			<td class="pt-6"><?php echo htmlspecialchars($datosServicio['duracion']); ?> minutos</td>
																			<td class="pt-6"><?php echo htmlspecialchars($barbero->obtenerNombreCompleto($barberoId)); ?></td>
																			<td class="pt-6">$<?php echo htmlspecialchars(number_format($datosServicio['precio'], 2)); ?></td>
																		</tr>
																	</tbody>
																</table>
															</div>
															<div class="d-flex justify-content-end" bis_skin_checked="1">
																<div class="mw-300px" bis_skin_checked="1">
																	<div class="d-flex flex-stack mb-3" bis_skin_checked="1">
																		<div class="fw-semibold pe-10 text-gray-600 fs-7" bis_skin_checked="1">Subtotal:</div>
																		<div class="text-end fw-bold fs-6 text-gray-800" bis_skin_checked="1">$<?php echo htmlspecialchars(number_format($datosServicio['precio'], 2)); ?></div>
																	</div>
																	<div class="d-flex flex-stack mb-3" bis_skin_checked="1">
																		<div class="fw-semibold pe-10 text-gray-600 fs-7" bis_skin_checked="1">IVA 0%</div>
																		<div class="text-end fw-bold fs-6 text-gray-800" bis_skin_checked="1">0.00</div>
																	</div>
																	<div class="d-flex flex-stack mb-3" bis_skin_checked="1">
																		<div class="fw-semibold pe-10 text-gray-600 fs-7" bis_skin_checked="1">Subtotal + IVA</div>
																		<div class="text-end fw-bold fs-6 text-gray-800" bis_skin_checked="1">$<?php echo htmlspecialchars(number_format($datosServicio['precio'], 2)); ?></div>
																	</div>
																	<div class="d-flex flex-stack" bis_skin_checked="1">
																		<div class="fw-semibold pe-10 text-gray-600 fs-7" bis_skin_checked="1">Total</div>
																		<div class="text-end fw-bold fs-6 text-gray-800" bis_skin_checked="1">$<?php echo htmlspecialchars(number_format($datosServicio['precio'], 2)); ?></div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="m-0" bis_skin_checked="1">
												<div class="d-print-none border border-dashed border-gray-300 card-rounded h-lg-10 min-w-md-300px p-9 bg-lighten" bis_skin_checked="1">
													<form id="reservaForm">
														<!-- Hidden Fields -->
														<input type="hidden" name="id" value="<?php echo htmlspecialchars($datosReserva['id']); ?>">
														<input type="hidden" name="cliente_id" value="<?php echo htmlspecialchars($datosReserva['cliente_id']); ?>">
														<input type="hidden" name="barbero_id" value="<?php echo htmlspecialchars($datosReserva['barbero_id']); ?>">
														<input type="hidden" name="fecha_reservada" value="<?php echo htmlspecialchars($datosReserva['fecha_reservada']); ?>">
														<input type="hidden" name="hora_inicio" value="<?php echo htmlspecialchars($datosReserva['hora_inicio']); ?>">
														<input type="hidden" name="action" id="action" value="">
														<!-- Display Fields -->
														<div class="mb-5" bis_skin_checked="1">
															<label for="fecha_reservada_texto" class="fw-semibold text-gray-600 fs-7">Fecha reservada:</label>
															<input type="text" id="fecha_reservada_texto" name="fecha_reservada_texto" class="form-control" value="<?php echo htmlspecialchars($datosReserva['fecha_reservada_texto']); ?>" disabled>
														</div>
														<div class="mb-5" bis_skin_checked="1">
															<label for="hora_inicio_texto" class="fw-semibold text-gray-600 fs-7">Hora de inicio:</label>
															<input type="text" id="hora_inicio_texto" name="hora_inicio_texto" class="form-control" value="<?php echo htmlspecialchars($datosReserva['hora_inicio_texto']); ?>" disabled>
														</div>
														<div class="mb-5" bis_skin_checked="1">
															<label for="duracion" class="fw-semibold text-gray-600 fs-7">Duración (minutos):</label>
															<input type="number" id="duracion" name="duracion" class="form-control" value="<?php echo htmlspecialchars($datosReserva['duracion']); ?>" disabled>
														</div>
														<div class="mb-5" bis_skin_checked="1">
															<label for="servicio" class="fw-semibold text-gray-600 fs-7">Servicio:</label>
															<select id="servicio" name="servicio" class="form-control">
																<?php foreach ($todosLosServicios as $servicio): ?>
																	<option value="<?php echo htmlspecialchars($servicio['id']); ?>" <?php echo $servicio['id'] == $datosServicio['id'] ? 'selected' : ''; ?>>
																		<?php echo htmlspecialchars($servicio['descripcion']); ?>
																	</option>
																<?php endforeach; ?>
															</select>
														</div>
														<div class="mb-5" bis_skin_checked="1">
															<label for="estado" class="fw-semibold text-gray-600 fs-7">Estado de la reserva:</label>
															<select id="estado" name="estado" class="form-control">
																<option value="Pendiente" <?php echo $datosReserva['estado'] === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
																<option value="Confirmada" <?php echo $datosReserva['estado'] === 'Confirmada' ? 'selected' : ''; ?>>Confirmada</option>
																<option value="Cancelada" <?php echo $datosReserva['estado'] === 'Cancelada' ? 'selected' : ''; ?>>Cancelada</option>
															</select>
														</div>
														<div class="mb-5" bis_skin_checked="1">
															<label for="metodo_pago" class="fw-semibold text-gray-600 fs-7">Método de pago:</label>
															<select id="metodo_pago" name="metodo_pago" class="form-control" <?php echo $datosReserva['metodo_pago'] !== 'Sin asignar' ? 'disabled' : ''; ?> onchange="document.getElementById('metodo_pago_hidden').value=this.value;">
																<option value="Sin asignar" <?php echo $datosReserva['metodo_pago'] === 'Sin asignar' ? 'selected' : ''; ?>>Sin asignar</option>
																<option value="Tarjeta" <?php echo $datosReserva['metodo_pago'] === 'Tarjeta' ? 'selected' : ''; ?>>Tarjeta</option>
																<option value="Efectivo" <?php echo $datosReserva['metodo_pago'] === 'Efectivo' ? 'selected' : ''; ?>>Efectivo</option>
															</select>
															<input type="hidden" id="metodo_pago_hidden" name="metodo_pago_hidden" value="<?php echo htmlspecialchars($datosReserva['metodo_pago']); ?>">
														</div>

														<div class="text-center mt-10">
															<button type="submit" class="btn btn-sm btn-light-primary" id="guardarCambiosBtn" disabled>Guardar cambios</button>
															<button type="submit" class="btn btn-sm btn-light-success" id="cobrarBtn" <?php echo $datosReserva['pagada'] === 'Sí' ? 'disabled' : ''; ?>>
																<?php echo $datosReserva['pagada'] === 'Sí' ? 'Ya está cobrada' : 'Cobrar'; ?>
															</button>
														</div>
													</form>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="kt_app_footer" class="app-footer ">
							<div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
								<div class="text-gray-900 order-2 order-md-1">
									<span class="text-muted fw-semibold me-1">2024&copy;</span>
									<a href="https://nictech.uy" target="_blank" class="text-gray-800 text-hover-primary">NICATECH</a>
								</div>
								<ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
									<li class="menu-item"><a href="https://nictech.uy/about" target="_blank" class="menu-link px-2">Sobre nosotros</a></li>
									<li class="menu-item"><a href="https://nictech.uy/support" target="_blank" class="menu-link px-2">Soporte</a></li>
									<li class="menu-item"><a href="https://nictech.uy/purchase" target="_blank" class="menu-link px-2">Comprar</a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<i class="ki-duotone ki-arrow-up">
				<span class="path1"></span>
				<span class="path2"></span>
			</i>
		</div>
		<script>
			var hostUrl = "assets/";
		</script>
		<script src="assets/plugins/global/plugins.bundle.js"></script>
		<script src="assets/js/scripts.bundle.js"></script>
		<script src="assets/js/widgets.bundle.js"></script>
		<script src="assets/js/custom/widgets.js"></script>
		<script>
			document.getElementById('reservaForm').addEventListener('submit', function(event) {
				event.preventDefault(); // Evitar el envío normal del formulario

				const form = document.getElementById('reservaForm');
				const formData = new FormData(form);
				const action = document.getElementById('action').value; // Obtener la acción actual

				// Enviar la solicitud AJAX usando Fetch API
				fetch('./api/guardar_cambios_reserva', {
						method: 'POST',
						body: formData
					})
					.then(response => response.json())
					.then(data => {
						toastr.options = {
							"closeButton": false,
							"debug": false,
							"newestOnTop": false,
							"progressBar": false,
							"positionClass": "toastr-top-right",
							"preventDuplicates": false,
							"onclick": null,
							"showDuration": "300",
							"hideDuration": "1000",
							"timeOut": "5000",
							"extendedTimeOut": "1000",
							"showEasing": "swing",
							"hideEasing": "linear",
							"showMethod": "fadeIn",
							"hideMethod": "fadeOut"
						};

						if (data.success) {
							toastr.success(data.message, "Éxito");
							setTimeout(() => location.reload(), 2000); // Recargar la página después de un retraso
						} else {
							toastr.error(data.message, "Error");
						}
					})
					.catch(error => {
						toastr.error("Hubo un error al intentar procesar la solicitud.", "Error");
						console.error('Error:', error);
					});
			});

			document.getElementById('guardarCambiosBtn').addEventListener('click', () => {
				document.getElementById('action').value = 'guardar';
			});

			document.getElementById('cobrarBtn').addEventListener('click', () => {
				document.getElementById('action').value = 'cobrar';
			});

			function checkFormChanges() {
				const form = document.getElementById('reservaForm');
				const initialFormData = new FormData(form);
				const fieldsToCheck = ['servicio', 'estado', 'metodo_pago'];

				form.querySelectorAll('input, select, textarea').forEach(field => {
					field.addEventListener('input', () => {
						const currentFormData = new FormData(form);
						const formChanged = fieldsToCheck.some(key => initialFormData.get(key) !== currentFormData.get(key));
						document.getElementById('guardarCambiosBtn').disabled = !formChanged;
					});
				});
			}

			checkFormChanges();
		</script>
</body>

</html>