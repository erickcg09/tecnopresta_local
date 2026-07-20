<?php  
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
/*$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
</script>';
}*/

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()){
  echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
} else {

}

require_once __DIR__ . '/usuarioAzure.php';
$usuario_azure = obtenerUsuarioSesion();

if (!$usuario_azure) {
    header("Location: index.html");
    exit();
}

// ==== CONSTRUIR RUTA DE REGRESO =====
$ruta_regreso ='navegar.php?ruta=formulario_menu_principal.php';
if (isset($_GET['subsistema_id'], $_GET['modulo_id'])) {
    $ruta_regreso = 'navegar.php?ruta=formulario_sub_modulos.php'
    . '&subsistema_id=' . intval($_GET['subsistema_id'] ?? 0)
    . '&modulo_id=' . intval($_GET['modulo_id'] ?? 0);
}

// === Bloquear acceso directo ===
if (!defined('ACCESO_SEGURO')) {
  http_response_code(403);
  exit('Acceso directo no permitido');
}

/*
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo']; */
$logcodigo = $usuario_azure['codigoPresu'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>TecnoPresta Ubicar Activo</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- ICONOS -->
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"> -->
  <link href="css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
  
  <!-- Nueva Identidad Gráfica Gobierno de Costa Rica CSS -->
  <link rel="stylesheet" href="assets/css/nueva-identidad.css">
  <link rel="stylesheet" href="css/formulario_menu_principal.css" />

</head>
<body class="layout-page">
  <?php include 'partials/header.php'; ?>
    <main class="flex-grow-1">
      <br>

      <div class="container">
          
          <!-- <h3>Usuario: <?php //echo $lognombre; ?> </h3><br> -->

            <h2 class="mb-5">Asignar ubicación o resguardo de activos</h2>

      <div class="card">
          <div class="card-header">
              <h4>Selección del Origen de los Fondos <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lightbulb-fill" viewBox="0 0 16 16">
      <path d="M2 6a6 6 0 1 1 10.174 4.31c-.203.196-.359.4-.453.619l-.762 1.769A.5.5 0 0 1 10.5 13h-5a.5.5 0 0 1-.46-.302l-.761-1.77a2 2 0 0 0-.453-.618A5.98 5.98 0 0 1 2 6m3 8.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1l-.224.447a1 1 0 0 1-.894.553H6.618a1 1 0 0 1-.894-.553L5.5 15a.5.5 0 0 1-.5-.5"/>
      </svg></h4>
          </div>
          <div class="card-body">
              <p class="card-text">
                  Para continuar, por favor selecciona el origen de los fondos con los que se adquirieron los activos. Esta información nos permitirá filtrar y distribuir los activos de manera más eficiente. Asegúrate de elegir la opción que mejor describa el origen de los fondos utilizados.
              </p>
          </div>
      </div>        
      <form action="form_para_ubicar_n.php" method="POST">
      <input type="hidden" name="subsistema_id" value="<?= intval($_GET['subsistema_id'] ?? 0) ?>">
      <input type="hidden" name="modulo_id" value="<?= intval($_GET['modulo_id'] ?? 0) ?>">
      <input type="hidden" id="codigo" name="codigo" value="<?php echo $logcodigo;?>">
          <div class="mb-3">
              
            <select class="form-select my-3 w-50" id="fondos" name="fondos" aria-label="Example select with button addon" required>
              <option value="0">Seleccione..</option>
              <?php 
                $querz = $link -> query ("SELECT * FROM t_fondos");
                while ($valorez = mysqli_fetch_array($querz)) {
                  echo '<option value="'.$valorez['id_fondos'].'">'.$valorez['fondos'].'</option>';
                }
              ?>
            </select>
          </div>
          <button type="submit" class="btn btn-secondary">Enviar</button>
      </form>

      </div>
      <!-- Botón flotante Volver -->
      <a href="<?= htmlspecialchars($ruta_regreso) ?>" class="btn-disponibilidad" 
          style="bottom: 100px;" data-tooltip="Regresar">
            <i class="bi bi-arrow-left-circle-fill"></i>
      </a>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <?php include 'partials/footer.php'; ?>
</body>
</html>