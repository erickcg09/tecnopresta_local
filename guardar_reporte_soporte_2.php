<?php
session_start();
if (!isset($_SESSION['cedula']) || !$_SESSION['cedula']) {
    echo '<script language="javascript">
    alert("Usuario no autenticado");
    self.location = "index.html";
    </script>';
    exit;
}

require_once("conexion.php");
$link = $mysqli;
date_default_timezone_set('America/Costa_Rica');

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

// Verificar si se envi¨® el formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<script language="javascript">
    alert("Acceso no v¨¢lido");
    self.location = "plataforma_clientes.php";
    </script>';
    exit;
}

// Validar campos requeridos
$required_fields = ['funcionario', 'correo', 'institucion', 'codigo', 'fecha', 
                   'estatus', 'incidencia', 'dre', 'circuito', 'fondos', 'asunto'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo '<script language="javascript">
        alert("Faltan datos obligatorios");
        self.location = "plataforma_clientes.php";
        </script>';
        exit;
    }
}

// Asignar variables con saneamiento b¨¢sico
$funcionario = trim($_POST['funcionario']) . " " . $_SESSION['cedula'] . " " . $_SESSION['codigo'];
$correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
$institucion = htmlspecialchars($_POST['institucion']);
$codigo = htmlspecialchars($_POST['codigo']);
$fecha = $_POST['fecha']; // Asumimos que viene en formato correcto
$estatus = htmlspecialchars($_POST['estatus']);
$tema = htmlspecialchars($_POST['tema'] ?? ''); // Usamos el operador null coalescing
$problema = htmlspecialchars($_POST['incidencia']);
$dre = htmlspecialchars($_POST['dre']);
$circuito = htmlspecialchars($_POST['circuito']);
$fondos = (int)$_POST['fondos']; // Convertir a entero
$asunto = (int)$_POST['asunto']; // Convertir a entero
$tomado = "No";
$ahora = date("Y-m-d H:i:s"); // Mejor usar formato completo

// Preparar la consulta SQL con sentencia preparada
$stmt = $link->prepare("INSERT INTO soporte 
    (`funcionario`, `placa`, `problema`, `fecha`, `estatus`, `codigo`, `correo`, 
     `institucion`, `tomado`, `dre`, `circuito`, `id_fondos`, `id_asunto`)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    die("Error en la preparaci¨®n de la consulta: " . $link->error);
}

// Vincular par¨¢metros
$stmt->bind_param("sssssssssssii", 
    $funcionario, 
    $tema, 
    $problema, 
    $ahora, 
    $estatus, 
    $codigo, 
    $correo, 
    $institucion, 
    $tomado, 
    $dre, 
    $circuito, 
    $fondos, 
    $asunto
);

// Ejecutar consulta
if ($stmt->execute()) {
    $id_insertado = $link->insert_id;
    
    // Registrar en el chat
    $saludo = "Hola soy " . $_POST['funcionario'] . " un gusto";
    $sql_chat = "INSERT INTO `t_chat_soporte` (`id_caso`, `tipo`, `mensaje`, `fecha`) 
                 VALUES (?, ?, ?, ?)";
    $stmt_chat = $link->prepare($sql_chat);
    $tipo = $_SESSION['cedula'];
    $stmt_chat->bind_param("isss", $id_insertado, $tipo, $saludo, $ahora);
    $stmt_chat->execute();
    $stmt_chat->close();
    
    echo '<script language="javascript">
        alert("Reporte creado exitosamente");
        self.location = "plataforma_clientes.php";
        </script>';
} else {
    error_log("Error al insertar reporte: " . $stmt->error);
    echo '<script language="javascript">
        alert("Ocurri¨® un error al guardar el reporte");
        self.location = "plataforma_clientes.php";
        </script>';
}

// Cerrar conexiones
$stmt->close();
$link->close();
?>