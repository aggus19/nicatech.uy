<?php
// enviar_mail.php . Este codigo basicamente va a recibir el email y lo va a guardar en la tabla futuros_clientes con la clase Cliente.

session_start();
require_once '../vendor/autoload.php';

use App\Cliente;

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit;
}

// Verificar el origen de la solicitud
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$loginPageFileName = 'index';

if (basename(parse_url($referer, PHP_URL_PATH)) !== $loginPageFileName) {
    http_response_code(403);
    exit;
}

// Obtener el contenido JSON de la solicitud
$data = json_decode(file_get_contents('php://input'), true);
$correo = trim($data['correo']);

// Crear instancia de Cliente
$cliente = new Cliente();

try {
    // Guardar el email del nuevo cliente
    $clienteId = $cliente->guardarEmail($correo);
    if ($clienteId > 0) {
        echo json_encode(['success' => true, 'message' => 'El cliente ha sido agregado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Hubo un problema al intentar agregar al cliente.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
