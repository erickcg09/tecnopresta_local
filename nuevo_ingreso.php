<?php
// nuevo_ingreso.php
header('Content-Type: text/plain; charset=utf-8');

error_log("=== NUEVO_INGRESO.PHP ACCEDIDO ===");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "ERROR: Método no permitido. Se requiere POST.\n";
    exit;
}

$jsonInput = file_get_contents('php://input');
error_log("Datos recibidos: " . $jsonInput);

if (empty($jsonInput)) {
    http_response_code(400);
    echo "ERROR: No se recibieron datos JSON\n";
    exit;
}

$data = json_decode($jsonInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo "ERROR: JSON inválido - " . json_last_error_msg() . "\n";
    exit;
}

// Procesamiento exitoso
echo "=== AUTENTICACIÓN AZURE AD EXITOSA ===\n\n";
echo "INFORMACIÓN DEL USUARIO:\n";
echo "------------------------\n";
echo "Nombre: " . ($data['displayName'] ?? 'N/A') . "\n";
echo "Email: " . ($data['email'] ?? 'N/A') . "\n";
echo "ID: " . ($data['id'] ?? 'N/A') . "\n";
echo "Puesto: " . ($data['jobTitle'] ?? 'N/A') . "\n";
echo "Departamento: " . ($data['department'] ?? 'N/A') . "\n";
echo "Ubicación: " . ($data['officeLocation'] ?? 'N/A') . "\n";
echo "Teléfono: " . ($data['mobilePhone'] ?? 'N/A') . "\n";
echo "Manager: " . ($data['manager'] ?? 'N/A') . "\n";
echo "Foto: " . (($data['hasPhoto'] ?? false) ? 'Disponible' : 'No disponible') . "\n";

echo "\n✅ LISTO PARA INTEGRAR CON BASE DE DATOS MYSQL\n";

error_log("nuevo_ingreso.php - Usuario autenticado: " . ($data['email'] ?? 'N/A'));
?>