<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
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

$resultados = [];
$mensajes = [];
$encontrado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = trim($_POST['placa'] ?? '');
    $serial = trim($_POST['serial'] ?? '');
    
    if (empty($placa) && empty($serial)) {
        $mensajes[] = ['tipo' => 'warning', 'texto' => 'Debe ingresar al menos un número de placa o serial para buscar.'];
    } else {
        // Buscar en activos actuales - MEJORADO: incluir código de institución
        $query = "SELECT p.*, i.codigo as codigo_institucion, i.institucion 
                  FROM t_placa p 
                  LEFT JOIN t_instituciones i ON p.codigo = i.codigo 
                  WHERE ";
        
        $conditions = [];
        $params = [];
        $types = '';
        
        if (!empty($placa)) {
            $conditions[] = "p.placa = ?";
            $params[] = $placa;
            $types .= 's';
        }
        
        if (!empty($serial)) {
            $conditions[] = "p.serial = ?";
            $params[] = $serial;
            $types .= 's';
        }
        
        $query .= implode(" OR ", $conditions);
        
        $stmt = $link->prepare($query);
        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $resultados['activos'][] = $row;
                $encontrado = true;
            }
            $stmt->close();
        }
        
        // Buscar en activos eliminados - MEJORADO: incluir código de institución
        $query_eliminados = "SELECT b.*, i.codigo as codigo_institucion, i.institucion 
                            FROM bitacora_eliminados b 
                            LEFT JOIN t_instituciones i ON b.codigo = i.codigo 
                            WHERE ";
        
        $conditions_eliminados = [];
        if (!empty($placa)) {
            $conditions_eliminados[] = "b.placa = ?";
        }
        if (!empty($serial)) {
            $conditions_eliminados[] = "b.serial = ?";
        }
        
        $stmt_eliminados = $link->prepare($query_eliminados . implode(" OR ", $conditions_eliminados));
        if ($stmt_eliminados) {
            if (!empty($params)) {
                $stmt_eliminados->bind_param($types, ...$params);
            }
            $stmt_eliminados->execute();
            $result_eliminados = $stmt_eliminados->get_result();
            
            while ($row = $result_eliminados->fetch_assoc()) {
                $resultados['eliminados'][] = $row;
                $encontrado = true;
            }
            $stmt_eliminados->close();
        }
        
        if (!$encontrado) {
            $mensajes[] = ['tipo' => 'info', 'texto' => 'No se encontraron resultados para la búsqueda.'];
        } else {
            // Analizar resultados para mensajes específicos
            if (!empty($resultados['activos'])) {
                foreach ($resultados['activos'] as $activo) {
                    if (!empty($placa) && !empty($serial)) {
                        if ($activo['placa'] == $placa && $activo['serial'] == $serial) {
                            $mensajes[] = ['tipo' => 'success', 'texto' => 'El activo fue encontrado con placa y serial coincidentes.'];
                            break;
                        } elseif ($activo['placa'] == $placa && $activo['serial'] != $serial) {
                            $mensajes[] = ['tipo' => 'warning', 'texto' => 'La placa fue encontrada pero con un serial diferente.'];
                        } elseif ($activo['placa'] != $placa && $activo['serial'] == $serial) {
                            $mensajes[] = ['tipo' => 'warning', 'texto' => 'El serial fue encontrado pero asociado a otra placa.'];
                        }
                    }
                }
            }
            
            if (!empty($resultados['eliminados'])) {
                foreach ($resultados['eliminados'] as $eliminado) {
                    $mensajes[] = [
                        'tipo' => 'danger', 
                        'texto' => 'El activo fue encontrado en registros eliminados. Institución que lo eliminó: ' . 
                                  ($eliminado['institucion'] ?? 'Desconocida') . 
                                  ' (Código: ' . ($eliminado['codigo_institucion'] ?? 'N/A') . ')'
                    ];
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Activos</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .result-card {
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
        }
        .eliminado-card {
            border-left: 4px solid #dc3545;
        }
        .mensaje-alerta {
            margin-bottom: 15px;
        }
        .institucion-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .codigo-institucion {
            font-family: 'Courier New', monospace;
            background-color: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="formulario_menu_principal.html">
                <i class="bi bi-laptop"></i> Tecnopresta
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="herramientas.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>   
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2 class="mb-4">Consulta de Activos</h2>
        
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Buscador de Activos</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="placa" class="form-label">Número de Placa</label>
                            <input type="text" class="form-control" id="placa" name="placa" 
                                   value="<?= htmlspecialchars($_POST['placa'] ?? '') ?>" 
                                   placeholder="Ingrese el número de placa">
                        </div>
                        <div class="col-md-6">
                            <label for="serial" class="form-label">Número de Serial</label>
                            <input type="text" class="form-control" id="serial" name="serial" 
                                   value="<?= htmlspecialchars($_POST['serial'] ?? '') ?>" 
                                   placeholder="Ingrese el número de serial">
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar Activo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($mensajes)): ?>
            <div class="mt-4">
                <?php foreach ($mensajes as $mensaje): ?>
                    <div class="alert alert-<?= $mensaje['tipo'] ?> mensaje-alerta">
                        <?= $mensaje['texto'] ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($resultados)): ?>
            <div class="mt-4">
                <h4>Resultados de la Búsqueda</h4>
                
                <?php if (!empty($resultados['activos'])): ?>
                    <h5 class="mt-3 text-success">
                        <i class="bi bi-check-circle"></i> Activos Encontrados (<?= count($resultados['activos']) ?>)
                    </h5>
                    <?php foreach ($resultados['activos'] as $activo): ?>
                        <div class="card result-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="bi bi-pc-display"></i> Activo #<?= $activo['id_placa'] ?>
                                    <span class="badge bg-success float-end">ACTIVO</span>
                                </h5>
                                
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <p><strong>Placa:</strong> <span class="text-primary"><?= htmlspecialchars($activo['placa']) ?></span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Serial:</strong> <span class="text-primary"><?= htmlspecialchars($activo['serial']) ?></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>ID Interno:</strong> <code><?= htmlspecialchars($activo['id_placa']) ?></code></p>
                                    </div>
                                </div>
                                
                                <div class="institucion-info">
                                    <h6><i class="bi bi-building"></i> Información de la Institución</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Nombre:</strong> <?= htmlspecialchars($activo['institucion'] ?? 'No asignada') ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Código:</strong> <span class="codigo-institucion"><?= htmlspecialchars($activo['codigo_institucion'] ?? 'N/A') ?></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (!empty($resultados['eliminados'])): ?>
                    <h5 class="mt-3 text-danger">
                        <i class="bi bi-exclamation-triangle"></i> Activos Eliminados (<?= count($resultados['eliminados']) ?>)
                    </h5>
                    <?php foreach ($resultados['eliminados'] as $eliminado): ?>
                        <div class="card result-card eliminado-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="bi bi-archive"></i> Activo Eliminado
                                    <span class="badge bg-danger float-end">ELIMINADO</span>
                                </h5>
                                
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <p><strong>Placa:</strong> <span class="text-danger"><?= htmlspecialchars($eliminado['placa']) ?></span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Serial:</strong> <span class="text-danger"><?= htmlspecialchars($eliminado['serial']) ?></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>ID Original:</strong> <code><?= htmlspecialchars($eliminado['id_placa'] ?? 'N/A') ?></code></p>
                                    </div>
                                </div>
                                
                                <div class="institucion-info">
                                    <h6><i class="bi bi-building-exclamation"></i> Institución que Eliminó</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Nombre:</strong> <?= htmlspecialchars($eliminado['institucion'] ?? 'No registrada') ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Código:</strong> <span class="codigo-institucion"><?= htmlspecialchars($eliminado['codigo_institucion'] ?? 'N/A') ?></span></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (isset($eliminado['fecha_eliminacion'])): ?>
                                    <div class="mt-2">
                                        <p class="text-muted">
                                            <small><strong>Fecha de eliminación:</strong> <?= htmlspecialchars($eliminado['fecha_eliminacion']) ?></small>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del formulario antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const placa = document.getElementById('placa').value.trim();
            const serial = document.getElementById('serial').value.trim();
            
            if (placa === '' && serial === '') {
                e.preventDefault();
                alert('Debe ingresar al menos un número de placa o serial para buscar.');
            }
        });

        // Auto-focus en el primer campo
        document.addEventListener('DOMContentLoaded', function() {
            const placaField = document.getElementById('placa');
            if (placaField) {
                placaField.focus();
            }
        });
    </script>
</body>
</html>