<?php
require_once("conexion.php");
header('Content-Type: application/json');

$link = $mysqli;

if (mysqli_connect_errno()) {
    echo json_encode(['error' => 'Error de conexión a MySQL: ' . mysqli_connect_error()]);
    exit();
}

// Obtenemos el código enviado por POST
$codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';

if (empty($codigo)) {
    echo json_encode(['error' => 'Código vacío']);
    exit();
}

// Consulta para buscar instituciones por código
$query = "SELECT institucion FROM t_instituciones WHERE codigo = ? AND activo = 1";
$stmt = $link->prepare($query);

if ($stmt) {
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $instituciones = [];
    while ($row = $result->fetch_assoc()) {
        $instituciones[] = $row['institucion'];
    }
    
    $stmt->close();
    
    if (count($instituciones) > 0) {
        // Si hay múltiples instituciones con el mismo código
        if (count($instituciones) > 1) {
            echo json_encode([
                'institucion' => $instituciones[0] . ' (contiene satélites)',
                'multiple' => true
            ]);
        } else {
            // Solo una institución encontrada
            echo json_encode([
                'institucion' => $instituciones[0],
                'multiple' => false
            ]);
        }
    } else {
        // No se encontraron instituciones
        echo json_encode([
            'institucion' => 'No Registrado',
            'multiple' => false
        ]);
    }
} else {
    echo json_encode(['error' => 'Error en la consulta: ' . $link->error]);
}

$link->close();
?>