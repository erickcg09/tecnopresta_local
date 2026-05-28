<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false) {
    echo '<script language = javascript>
    alert("No tienes permisos")
    self.location = "index.html"
    </script>';
}

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
    exit();
}

// Recibir datos del formulario
$centro = isset($_POST['centro']) ? mysqli_real_escape_string($link, $_POST['centro']) : '';
$fondos = isset($_POST['fondos']) ? intval($_POST['fondos']) : 0;

// Validar que los datos no estén vacíos
if (empty($centro) || $fondos == 0) {
    echo '<div class="alert alert-danger">Debe completar todos los campos del formulario</div>';
    exit();
}

// Consulta SQL mejorada con manejo de NULL
$query = "SELECT p.id_placa, p.placa, p.serial, p.id_lugar,
          IFNULL(l.lugar, 'SIN UBICACIÓN (Revisar)') as lugar,
          CASE WHEN p.enuso = 1 THEN 'Si' ELSE 'No' END as estado_uso,
          CASE WHEN p.id_lugar IS NULL THEN '⚠' ELSE '' END as alerta
          FROM t_placa p
          LEFT JOIN t_lugar l ON p.id_lugar = l.id_lugar
          WHERE p.codigo = '$centro' AND p.id_fondos = $fondos
          ORDER BY p.placa";

$result = mysqli_query($link, $query);

if (!$result) {
    echo '<div class="alert alert-danger">Error en la consulta: ' . mysqli_error($link) . '</div>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activos por Centro Educativo</title>
        <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-container {
            margin-top: 30px;
        }
        .table th {
            background-color: #343a40;
            color: white;
        }
        .warning-icon {
            color: #dc3545;
            font-weight: bold;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-md bg-dark navbar-dark">
  <img src="img/logodelgobierno.png" width="45" height="30" alt="" loading="lazy">
  <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="formulario_herramientas_con_tablas.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply-all" viewBox="0 0 16 16">
                <path d="M8.098 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.7 8.7 0 0 0-1.921-.306 7 7 0 0 0-.798.008h-.013l-.005.001h-.001L8.8 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L4.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028zM9.3 10.386q.102 0 .223.006c.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96z"/>
                <path d="M5.232 4.293a.5.5 0 0 0-.7-.106L.54 7.127a1.147 1.147 0 0 0 0 1.946l3.994 2.94a.5.5 0 1 0 .593-.805L1.114 8.254l-.042-.028a.147.147 0 0 1 0-.252l.042-.028 4.012-2.954a.5.5 0 0 0 .106-.699"/>
                </svg> Regresar</a>
      </li> 
      <li class="nav-item">
        <a class="nav-link" href="gameover.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-open" viewBox="0 0 16 16">
                <path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1"/>
                <path d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117M11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5M4 1.934V15h6V1.077z"/>
                </svg> Cerrar Sesión</a>
      </li>  
    </ul>
  </div>  
</nav>
    <div class="container">
        <h2 class="text-center my-4">Activos del Centro Educativo</h2>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="alert alert-info">
                <strong>⚠ Atención:</strong> Los registros marcados con símbolo de advertencia no tienen ubicación asignada.
            </div>
            
            <div class="table-responsive table-container">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID Placa</th>
                            <th>Placa</th>
                            <th>Serial</th>
                            <th>Lugar</th>
                            <th>En Uso</th>
                            <th>Alerta</th>
                            <th>Corregir</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="<?php echo ($row['id_lugar'] === null) ? 'table-warning' : ''; ?>">
                                <td><?php echo htmlspecialchars($row['id_placa']); ?></td>
                                <td><?php echo htmlspecialchars($row['placa']); ?></td>
                                <td><?php echo htmlspecialchars($row['serial']); ?></td>
                                <td><?php echo htmlspecialchars($row['lugar']); ?></td>
                                <td><?php echo htmlspecialchars($row['estado_uso']); ?></td>
                                <td class="warning-icon"><?php echo htmlspecialchars($row['alerta']); ?></td>
                                <td>
                                    <a href="actualizar_lugar.php?id_placa=<?php echo $row['id_placa']; ?>" 
                                       class="btn btn-warning btn-sm btn-action">
                                        <i class="bi bi-arrow-counterclockwise"></i> A 0
                                    </a>
                                </td>
                                <td>
                                    <a href="eliminar_registro.php?id_placa=<?php echo $row['id_placa']; ?>" 
                                       class="btn btn-danger btn-sm btn-action"
                                       onclick="return confirm('¿Está seguro que desea eliminar este registro?');">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                No se encontraron activos para los criterios de búsqueda seleccionados.
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="javascript:history.back()" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>