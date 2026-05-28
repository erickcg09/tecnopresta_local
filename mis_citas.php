<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 2, 3, 4, 5, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte con el administrador.");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
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

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];

// Obtener citas del usuario
$query = "SELECT c.*, s.nombre as soportista_nombre 
          FROM citas c
          JOIN soportistas s ON c.soportista_id = s.id
          WHERE c.usuario_id = '".$_SESSION['cedula']."'
          ORDER BY c.fecha DESC, c.hora_inicio DESC";
$result = mysqli_query($link, $query);

// Mostrar mensajes si existen
$mensaje = $_SESSION['mensaje'] ?? null;
unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas Agendadas</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .cita-pendiente { border-left: 4px solid #0d6efd; }
        .cita-completada { border-left: 4px solid #198754; }
        .cita-cancelada { border-left: 4px solid #dc3545; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="formulario_menu_principal.html">
                <i class="bi bi-laptop"></i> Tecnopresta
            </a>
            <!-- Bot«Ñn para m«Ñviles (actualizado a BS5) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Men«â colapsable -->
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="citas_agendar.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>   
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-calendar-check"></i> Mis Citas</h2>
            <a href="citas_agendar.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Agendar Nueva
            </a>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $mensaje['tipo'] ?> alert-dismissible fade show">
                <?= $mensaje['texto'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-list"></i> Historial</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-primary">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Soportista</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($cita = mysqli_fetch_assoc($result)): ?>
                                    <tr class="cita-<?= $cita['estado'] ?>">
                                        <td><?= date('d/m/Y', strtotime($cita['fecha'])) ?></td>
                                        <td><?= date('H:i', strtotime($cita['hora_inicio'])) ?> - <?= date('H:i', strtotime($cita['hora_fin'])) ?></td>
                                        <td><?= htmlspecialchars($cita['soportista_nombre']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $cita['estado'] == 'pendiente' ? 'primary' : 
                                                ($cita['estado'] == 'completada' ? 'success' : 'danger') 
                                            ?>">
                                                <?= ucfirst($cita['estado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($cita['estado'] == 'pendiente'): ?>
                                                <form action="cancelar.php" method="POST" class="d-inline">
                                                    <input type="hidden" name="id" value="<?= $cita['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Â¿Cancelar esta cita?')">
                                                        <i class="bi bi-x-circle"></i> Cancelar
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No tienes citas agendadas.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>