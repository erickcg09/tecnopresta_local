<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/usuarioAzure.php';
$usuario_azure = obtenerUsuarioSesion();

if (!$usuario_azure) {
    header("Location: index.html");
    exit();
}

// ==== CONSTRUIR RUTA DE REGRESO ===== Para el botón regresar
$ruta_regreso = 'navegar.php?ruta=plataforma_soporte_n.php'; // Ruta por defecto si no vienen parámetros
//Validar que vengan los parámetros necesarios para construir la ruta de regreso
if (isset($_GET['ruta_regreso'])) {
    $ruta_regreso = $_GET['ruta_regreso'];
} elseif (isset($_GET['subsistema_id'], $_GET['modulo_id'])) {
    $ruta_regreso = 'navegar.php?ruta=plataforma_soporte_n.php'
    . '&subsistema_id=' . intval($_GET['subsistema_id'] ?? 0)
    . '&modulo_id=' . intval($_GET['modulo_id'] ?? 0);
}

$logusuario = $usuario_azure['cedula'];
$lognombre = $usuario_azure['nombre'];
$logcodigo = $usuario_azure['codigoPresu'];
$logcorreo = $usuario_azure['correo'];
$logdireccionreg = $usuario_azure['regional'];
$logcircuito = $usuario_azure['circuito'];
$soportista = "tecnopresta@mep.go.cr";
$generica = $_GET["rep"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>Contactar a Soporte TecnoPresta</title>
  <link rel="shortcut icon" href="ico/favicon.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="bootstrap5/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">

  <!-- Nueva Identidad Gráfica Gobierno de Costa Rica CSS -->
  <link rel="stylesheet" href="assets/css/nueva-identidad.css">
  <link rel="stylesheet" href="css/formulario_menu_principal.css" />
  <style>
    body {
      background-color: #f8f9fa;
    }
  </style>
</head>
<body class="layout-page">
    <?php include 'partials/header.php'; ?>

    <div class="container py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 class="fw-bold mb-1"><i class="bi bi-envelope me-2"></i>Contactar a Soporte TecnoPresta</h4>
          <p class="text-muted mb-0 small">Reporte una incidencia o realice una consulta al equipo de soporte</p>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-7">
          <div class="card border-0 shadow-sm" style="border-radius:16px;border-top:4px solid #CFAC65;">
            <div class="card-body p-4">
              <form name="solicitar" id="solicitar" action="enviar_notificacion_soporte_n.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="asunto" class="form-label fw-semibold">Asunto</label>
                  <input type="text" class="form-control" id="asunto" name="asunto" value="<?php echo $generica?>">
                </div>
                <div class="mb-3">
                  <label for="emisor" class="form-label fw-semibold">Funcionario</label>
                  <input type="text" class="form-control" id="emisor" name="emisor" value="<?php echo $lognombre?>" readonly>
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="regional" class="form-label fw-semibold">Regional</label>
                    <input type="text" class="form-control" id="regional" name="regional" value="<?php echo $logdireccionreg?>" readonly>
                  </div>
                  <div class="col-md-6">
                    <label for="circuito" class="form-label fw-semibold">Circuito</label>
                    <input type="text" class="form-control" id="circuito" name="circuito" value="<?php echo $logcircuito?>" readonly>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="correo" class="form-label fw-semibold">Correo donde contactarle</label>
                  <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $logcorreo?>">
                </div>
                <div class="mb-3">
                  <label for="mensaje" class="form-label fw-semibold">Mensaje</label>
                  <textarea class="form-control" id="mensaje" name="mensaje" rows="4" placeholder="Escriba su problema o inquietud"></textarea>
                </div>
                <div class="mb-4">
                  <label for="resume" class="form-label fw-semibold">Captura o PDF</label>
                  <input type="file" class="form-control" id="resume" name="resume" accept="image/*, .pdf" required>
                </div>
                <input type="hidden" id="receptor" name="receptor" value="<?php echo $soportista?>">
                <input type="hidden" id="cedula" name="cedula" value="<?php echo $logusuario?>">
                <input type="hidden" name="ruta_regreso" value="<?= htmlspecialchars($ruta_regreso) ?>">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                  <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-send me-2"></i>Enviar
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-md-5">
          <div class="card border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
            <div class="card-body text-center p-4">
              <img src="img/contactenos2.png" class="img-fluid" alt="Contacto" style="max-height:450px;">
            </div>
          </div>
        </div>
      </div>   
    </div>
    <!-- Botón flotante Volver al Dashboard -->
    <a href="<?= htmlspecialchars($ruta_regreso) ?>" class="btn-disponibilidad" data-tooltip="Regresar">
        <i class="bi bi-arrow-left-circle-fill"></i>
    </a>
    
    <?php include 'partials/footer.php'; ?>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="bootstrap5/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery para mejor manejo de eventos -->
    <script src="js/jquery-3.7.1.min.js"></script>
</body>
</html>