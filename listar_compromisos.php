<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,2]);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
</script>';
}
require_once 'configPDO.php';

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$loginstitucion = $_SESSION['dependencia'];
$logcorreo = $_SESSION['correomep'];
// Configuración de la institución
$institucion = $loginstitucion;
$representante = $lognombre;
$cedula_juridica = $logusuario;
$codigo = $logcodigo;

// Procesar cambio de estado
if (isset($_POST['cambiar_estado'])) {
    $id = $_POST['id'];
    $estado = $_POST['estado'];
    $observaciones = $_POST['observaciones'];
    
    try {
        $sql = "UPDATE compromisos SET estado = ?, observaciones = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$estado, $observaciones, $id]);
        $success = "Estado actualizado correctamente!";
    } catch(PDOException $e) {
        $error = "Error al actualizar el estado: " . $e->getMessage();
    }
}

// Obtener compromisos
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';

// Construir la consulta base con el filtro de código
$sql = "SELECT * FROM compromisos WHERE codigo = :codigo";

// Añadir condiciones adicionales según el filtro
if ($filtro == 'vencidos') {
    $sql .= " AND fecha_vencimiento < CURDATE() AND estado NOT IN ('cumplido', 'incumplido')";
} elseif ($filtro == 'activos') {
    $sql .= " AND fecha_vencimiento >= CURDATE() AND estado = 'activo'";
} elseif ($filtro == 'cumplidos') {
    $sql .= " AND estado = 'cumplido'";
} elseif ($filtro == 'incumplidos') {
    $sql .= " AND estado = 'incumplido'";
}

// Preparar y ejecutar la consulta con PDO (usando parámetros para evitar SQL injection)
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR); // 
$stmt->execute();
$compromisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Compromisos</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="formulario_menu_principal.html">
                <i class="bi bi-laptop"></i> Tecnopresta
            </a>
            <!-- Botón para móviles (actualizado a BS5) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Menú colapsable -->
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>   
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4">Listado de Compromisos</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="mb-4">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <select class="form-select" name="filtro">
                        <option value="todos" <?= $filtro == 'todos' ? 'selected' : '' ?>>Todos los compromisos</option>
                        <option value="activos" <?= $filtro == 'activos' ? 'selected' : '' ?>>Activos</option>
                        <option value="vencidos" <?= $filtro == 'vencidos' ? 'selected' : '' ?>>Vencidos</option>
                        <option value="cumplidos" <?= $filtro == 'cumplidos' ? 'selected' : '' ?>>Cumplidos</option>
                        <option value="incumplidos" <?= $filtro == 'incumplidos' ? 'selected' : '' ?>>Incumplidos</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="crear_compromiso.php" class="btn btn-success">Nuevo Compromiso</a>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Menor</th>
                        <th>Responsable</th>
                        <th>Inicio</th>
                        <th>Vence</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compromisos as $compromiso): ?>
                        <tr>
                            <td><?= $compromiso['id'] ?></td>
                            <td><?= $compromiso['nombre_menor'] ?></td>
                            <td><?= $compromiso['nombre_responsable'] ?></td>
                            <td><?= date('d/m/Y', strtotime($compromiso['fecha_inicio'])) ?></td>
                            <td class="<?= (strtotime($compromiso['fecha_vencimiento']) < time() && $compromiso['estado'] == 'activo') ? 'text-danger fw-bold' : '' ?>">
                                <?= date('d/m/Y', strtotime($compromiso['fecha_vencimiento'])) ?>
                            </td>
                            <td>
                                <?php 
                                    $badge_class = '';
                                    if ($compromiso['estado'] == 'activo') {
                                        $badge_class = 'bg-primary';
                                    } elseif ($compromiso['estado'] == 'cumplido') {
                                        $badge_class = 'bg-success';
                                    } elseif ($compromiso['estado'] == 'incumplido') {
                                        $badge_class = 'bg-danger';
                                    } elseif ($compromiso['estado'] == 'vencido') {
                                        $badge_class = 'bg-warning text-dark';
                                    }
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= ucfirst($compromiso['estado']) ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalDetalle<?= $compromiso['id'] ?>">Ver</button>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEstado<?= $compromiso['id'] ?>">Cambiar Estado</button>
                                <?php if ($compromiso['archivo_contrato']): ?>
                                    <a href="contratos/<?= $compromiso['archivo_contrato'] ?>" class="btn btn-sm btn-secondary" target="_blank">PDF</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <!-- Modal Detalle -->
                        <div class="modal fade" id="modalDetalle<?= $compromiso['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detalle del Compromiso #<?= $compromiso['id'] ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p><strong>Menor:</strong> <?= $compromiso['nombre_menor'] ?></p>
                                                <p><strong>Documento:</strong> <?= $compromiso['documento_menor'] ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Responsable:</strong> <?= $compromiso['nombre_responsable'] ?></p>
                                                <p><strong>Documento:</strong> <?= $compromiso['documento_responsable'] ?></p>
                                            </div>
                                        </div>
                                        <p><strong>Dato Adicional:</strong> <?= $compromiso['dato_adicional'] ?></p>
                                        <p><strong>Vigencia:</strong> <?= date('d/m/Y', strtotime($compromiso['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($compromiso['fecha_vencimiento'])) ?></p>
                                        <p><strong>Estado:</strong> <span class="badge <?= $badge_class ?>"><?= ucfirst($compromiso['estado']) ?></span></p>
                                        <?php if ($compromiso['observaciones']): ?>
                                            <p><strong>Observaciones:</strong> <?= $compromiso['observaciones'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal Cambiar Estado -->
                        <div class="modal fade" id="modalEstado<?= $compromiso['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Cambiar Estado - Compromiso #<?= $compromiso['id'] ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $compromiso['id'] ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Nuevo Estado</label>
                                                <select class="form-select" name="estado" required>
                                                    <option value="cumplido" <?= $compromiso['estado'] == 'cumplido' ? 'selected' : '' ?>>Cumplido</option>
                                                    <option value="incumplido" <?= $compromiso['estado'] == 'incumplido' ? 'selected' : '' ?>>Incumplido</option>
                                                    <option value="activo" <?= $compromiso['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                                                    <option value="vencido" <?= $compromiso['estado'] == 'vencido' ? 'selected' : '' ?>>Vencido</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Observaciones</label>
                                                <textarea class="form-control" name="observaciones" rows="3"><?= $compromiso['observaciones'] ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary" name="cambiar_estado">Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>