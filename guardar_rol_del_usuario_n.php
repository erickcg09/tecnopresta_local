<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
/*if (!$_SESSION){
echo '<script language = javascript>
alert("usuario no autenticado")
self.location = "index.html"
</script>';
}
*/
require_once("conexion.php");
require_once __DIR__ . '/usuarioAzure.php';
require_once("variablesemail.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$link = $mysqli;
$usuario_azure = obtenerUsuarioSesion();
if (!$usuario_azure) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Usuario no autenticado'];
    header("Location: index.html");
    exit;
}
$logcodigo = $usuario_azure['codigoPresu'] ?? '';
$estafecha = date('d-m-Y h:i:s');

if (mysqli_connect_errno()) {
    error_log("Error de conexion a mysql: " . mysqli_connect_error());
}

if (!mysqli_set_charset($link, "utf8")) {
    error_log("Error cargando el conjunto de caracteres utf8");
}


function verificacion ($correousuario,$nombreusuario,$rolusuario,$correosistema,$passmail,$fechora){ 
            $fh=$fechora;
            $roles=$rolusuario;
  			$para = $correousuario;
			$completo = $nombreusuario;
			$email_user = $correosistema;
			$email_password = $passmail;
			$the_subject = "Asignación de Permisos Específicos";
			$address_to = $para;
			$from_name = "Tecnopresta";
			$phpmailer = new PHPMailer();
			$texto = "Test email";
			// ---------- datos de la cuenta de Tecnopresta -------------------------------
			$phpmailer->Username = $email_user;
			$phpmailer->Password = $email_password; 
			//----------------------------------------------------------------------- 

		    $phpmailer->SMTPDebug = 0;  // Opciones 0, 1, 2
			$phpmailer->SMTPSecure = 'tls';
			$phpmailer->Host = "smtp.office365.com"; // Office365
			$phpmailer->Port = 587;
			$phpmailer->IsSMTP(); // use SMTP
			$phpmailer->SMTPAuth = true;
			$phpmailer->CharSet = 'UTF-8';
			$phpmailer->setFrom($phpmailer->Username,$from_name);
			$phpmailer->AddAddress($address_to); // recipients email
			$phpmailer->Subject = $the_subject;	
			$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/mailpermiso.png\" alt=\"\" width=\"600px\" height=\"500px\" />";
			$phpmailer->Body .="<h2 style='color:#3498db;'>Tecnopresta Alerta</h2>";
			$phpmailer->Body .="<table border=\"1\" WIDTH=\"600\" HEIGHT=\"500\">
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/usuario.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">$completo</td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/mensaje.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">Estimado se&ntilde;or (a) se comunica que se le han otorgado el siguiente rol de permisos en el sistema</td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/candado.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\"><b>$roles</b></td>
                                  </tr>
                                  <tr>
                                    <td align=\"center\" width=\"20%\"><img src=\"http://tecnopresta.mep.go.cr/revi/reloj.png\" alt=\"\" width=\"50px\" height=\"50px\" /></td>
                                    <td align=\"center\">$fh</td>
                                  </tr>
                                </table>
			";
			$phpmailer->Body .= "<img src=\"http://tecnopresta.mep.go.cr/ico/eserialb.png\" alt=\"\" width=\"600px\" height=\"150px\" />";
			$phpmailer->IsHTML(true);  // Activar si se envia etiquetas html
			$phpmailer->Send();
}

$correo = "tecnopresta@mep.go.cr";

$post = (isset($_POST['rol']) && !empty($_POST['rol'])) &&
        (isset($_POST['cedula']) && !empty($_POST['cedula'])) &&
        (isset($_POST['nombre']) && !empty($_POST['nombre'])) &&
        (isset($_POST['codigop']) && !empty($_POST['codigop']));

if (!$post) {
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Debe completar todos los campos'];
    header("Location: navegar.php?ruta=formulario_crear_roles_n.php");
    exit;
}

$rol = $_POST['rol'];
$cedula = trim($_POST['cedula']);
$nombre = $_POST['nombre'];
$codigop = trim($_POST['codigop']);
$edit_id = isset($_POST['edit_id']) && !empty($_POST['edit_id']) ? intval($_POST['edit_id']) : null;

if (!in_array($rol, [3, 4]) || $cedula === '') {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Rol no válido'];
    header("Location: navegar.php?ruta=formulario_crear_roles_n.php");
    exit;
}

if ($codigop != $logcodigo) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'El código presupuestario no coincide con el del usuario actual'];
    header("Location: navegar.php?ruta=formulario_crear_roles_n.php");
    exit;
}

$preguntar = mysqli_query($link, "select rol from t_roles where id_rol='$rol'");
$respuesta = mysqli_fetch_array($preguntar);
$nrol = $respuesta['rol'];

// === MODO EDICIÓN ===
if ($edit_id) {
    $checkRecord = mysqli_query($link, "SELECT ur.id, ur.usuario_id FROM usuarios_roles ur WHERE ur.id = $edit_id AND ur.codigo_presu = '$codigop' AND ur.eliminado = 0 LIMIT 1");
    if (mysqli_num_rows($checkRecord) != 1) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Registro no encontrado o no pertenece a su centro'];
        header("Location: navegar.php?ruta=formulario_crear_roles_n.php");
        exit;
    }
    $rowRecord = mysqli_fetch_array($checkRecord);
    $usuario_id = $rowRecord['usuario_id'];

    $link->query("UPDATE usuarios_roles SET rol_id = $rol, updated_at = NOW() WHERE id = $edit_id");
    $link->query("UPDATE usuarios SET correo = '$nombre', updated_at = NOW() WHERE id = $usuario_id");

    verificacion($nombre, $cedula, $nrol, $correo, $passemail, $estafecha);

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Usuario actualizado correctamente'];
    header("Location: navegar.php?ruta=formulario_crear_roles_n.php");
    exit;
}

// === MODO CREACIÓN ===
$miconsulta = "SELECT ur.id FROM usuarios u INNER JOIN usuarios_roles ur ON u.id = ur.usuario_id WHERE u.cedula='$cedula' AND ur.codigo_presu='$codigop' AND ur.eliminado=0 LIMIT 1";
$mirespuesta = $link->query($miconsulta);

if ($mirespuesta->num_rows >= 1) {
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'El usuario al que intenta asignar permisos ya existe en ese centro'];
    header("Location: navegar.php?ruta=formulario_crear_roles_n.php");
    exit;
}

$checkDeleted = "SELECT ur.id FROM usuarios u INNER JOIN usuarios_roles ur ON u.id = ur.usuario_id WHERE u.cedula='$cedula' AND ur.codigo_presu='$codigop' AND ur.eliminado=1 LIMIT 1";
$resultDeleted = $link->query($checkDeleted);
if ($resultDeleted->num_rows >= 1) {
    $rowDeleted = $resultDeleted->fetch_assoc();
    $link->query("UPDATE usuarios_roles SET eliminado = 0, rol_id = $rol WHERE id = {$rowDeleted['id']}");

    $preguntar2 = mysqli_query($link, "SELECT rol FROM t_roles WHERE id_rol='$rol'");
    $nrol = mysqli_fetch_array($preguntar2)['rol'];

    verificacion($nombre, $cedula, $nrol, $correo, $passemail, $estafecha);

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Usuario reactivado correctamente'];
    header("Location: navegar.php?ruta=formulario_crear_roles_n.php");
    exit;
}

$resUser = mysqli_query($link, "SELECT id FROM usuarios WHERE cedula='$cedula' LIMIT 1");
if (mysqli_num_rows($resUser) > 0) {
    $rowUser = mysqli_fetch_array($resUser);
    $usuario_id = $rowUser['id'];
} else {
    mysqli_query($link, "INSERT INTO usuarios (cedula, nombre, correo, azure_id, sexo, created_at, updated_at) VALUES ('$cedula', '$nombre', '$nombre', '', NULL, NOW(), NOW())");
    $usuario_id = mysqli_insert_id($link);
}

$consulta = "INSERT INTO usuarios_roles (usuario_id, rol_id, subsistema_id, codigo_presu, created_at) VALUES ($usuario_id, $rol, 1, '$codigop', NOW())";
$link->query($consulta);

verificacion($nombre, $cedula, $nrol, $correo, $passemail, $estafecha);

$_SESSION['flash'] = ['type' => 'success', 'message' => 'Usuario guardado correctamente'];
header("Location: navegar.php?ruta=formulario_crear_roles_n.php");
exit;