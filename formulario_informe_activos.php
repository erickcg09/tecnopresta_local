<?php
session_start();

// Verificación de permisos más eficiente
$permisosValidos = [1, 2, 3, 4, 5];
$tienellave = isset($_SESSION['tipo']) && in_array($_SESSION['tipo'], $permisosValidos);

if (!$tienellave) {
    echo '<script>
        alert("No tienes permisos");
        window.location.href = "formulario_menu_inventario.html";
    </script>';
    exit;
}

// Cargar datos de sesión
$logusuario = $_SESSION['cedula'] ?? '';
$lognombre = $_SESSION['nombre'] ?? '';
$logtipo = $_SESSION['tipo'] ?? '';
$logcodigo = $_SESSION['codigo'] ?? '';
$logdependencia = $_SESSION['dependencia'] ?? '';
$activado = 1;

require_once("conexion.php");
$link = $mysqli;
// Manejo mejorado de conexión
if (mysqli_connect_errno()) {
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres utf8");
}

// Inicializar variables
$b_estado = 0;
$estado = '';
$consulta = null;

// Procesar filtro de estado
if (isset($_POST['btnEstado']) && !empty(trim($_POST['s_estado'] ?? ''))) {
    $estado = trim($_POST['s_estado']);
    
    // Consulta preparada para mayor seguridad
    $query = "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, 
                     Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
              FROM t_activo Ta
              INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
              INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
              INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
              INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
              WHERE Tp.codigo = ? AND Tp.activo = ? AND Tp.id_estado = ?
              ORDER BY Tg.clase ASC";
    
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "sii", $logcodigo, $activado, $estado);
    mysqli_stmt_execute($stmt);
    $consulta = mysqli_stmt_get_result($stmt);
    
    $b_estado = 1;
}

// Consulta por defecto (sin filtro de estado)
if ($b_estado == 0) {
    $query = "SELECT Ta.id_activo, Tg.clase, Tm.marca, Ta.modelo, Tc.color, 
                     Tp.id_placa, Tp.placa, Tp.serial, Tp.id_estado, Tp.codigo, Tp.activo
              FROM t_activo Ta
              INNER JOIN t_marca Tm ON Ta.id_marca = Tm.id_marca 
              INNER JOIN t_color Tc ON Ta.id_color = Tc.id_color
              INNER JOIN t_placa Tp ON Ta.id_activo = Tp.id_activo
              INNER JOIN t_activo_general Tg ON Ta.id_ag = Tg.id_ag 
              WHERE Tp.codigo = ? AND Tp.activo = ?
              ORDER BY Tg.clase ASC";
    
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "si", $logcodigo, $activado);
    mysqli_stmt_execute($stmt);
    $consulta = mysqli_stmt_get_result($stmt);
}

// Obtener estados para el dropdown
$q_estado = mysqli_query($link, "SELECT * FROM t_estado");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>PNTM - Listado de Activos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="stylesheet" type="text/css" href="/css_reportes/ico_reportes.css">
    <style>
        .table-container {
            max-height: 70vh;
            overflow-y: auto;
        }
        .table thead th {
            position: sticky;
            top: 0;
            background-color: #343a40;
            color: white;
            z-index: 10;
        }
        .search-box {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .export-buttons {
            margin-top: 20px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-excelente { background-color: #d4edda; color: #155724; }
        .status-bueno { background-color: #d1ecf1; color: #0c5460; }
        .status-regular { background-color: #fff3cd; color: #856404; }
        .status-malo { background-color: #f8d7da; color: #721c24; }        
        .status-hurtado { background-color: #e6e6fa; color: #4b0082; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="formulario_menu_principal.html">
                <i class="fas fa-laptop-code me-2"></i>Tecnopresta
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="inventario_reporte.php">
                            <i class="fas fa-chart-bar me-1"></i>Reportes
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="mb-1">Listado de Activos</h2>
                <p class="text-muted">Usuario: <?php echo htmlspecialchars($lognombre); ?></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="ayuda.html#rla" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-life-ring me-1"></i>Ayuda
                </a>
                <a href="contactenos.php?rep=Error en Listado de Activos" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-envelope me-1"></i>Reportar Incidencia
                </a>
            </div>
        </div>

        <div class="search-box">
            <form method="POST">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="s_estado" class="form-label">Filtrar por estado</label>
                        <select class="form-select" id="s_estado" name="s_estado" required>
                            <option value="">Seleccione un estado...</option>
                            <?php while ($v_estado = mysqli_fetch_array($q_estado)): ?>
                                <option value="<?php echo $v_estado['id_estado']; ?>" 
                                    <?php echo ($estado == $v_estado['id_estado']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($v_estado['estado']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="btnEstado" class="btn btn-dark w-100">
                            <i class="fas fa-search me-1"></i>Buscar
                        </button>
                    </div>
                    <div class="col-md-6">
                        <label for="FiltrarContenido" class="form-label">Búsqueda rápida</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input id="FiltrarContenido" type="text" class="form-control" 
                                   placeholder="Ingrese el tipo del activo">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Resultados</h5>
                <div>
                    <a class="btn btn-danger me-2" 
                       href="exportar_a_c.php?codigop=<?php echo $logcodigo; ?>&b_estadop=<?php echo $b_estado; ?>&estadop=<?php echo $estado; ?>&dependenciap=<?php echo $logdependencia; ?>">
                        <i class="fas fa-file-pdf me-1"></i> Exportar PDF
                    </a>
                    <a class="btn btn-success" 
                       href="exportar_excel.php?codigop=<?php echo $logcodigo; ?>&b_estadop=<?php echo $b_estado; ?>&estadop=<?php echo $estado; ?>&dependenciap=<?php echo $logdependencia; ?>">
                        <i class="fas fa-file-excel me-1"></i> Exportar Excel
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Activo</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Color</th>
                                <th>Placa</th>
                                <th>Serie</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody class="BusquedaRapida">
                            <?php if ($consulta && mysqli_num_rows($consulta) > 0): ?>
                                <?php while ($activos = mysqli_fetch_array($consulta)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($activos['clase']); ?></td>
                                        <td><?php echo htmlspecialchars($activos['marca']); ?></td>
                                        <td><?php echo htmlspecialchars($activos['modelo']); ?></td>
                                        <td><?php echo htmlspecialchars($activos['color']); ?></td>
                                        <td><?php echo htmlspecialchars($activos['placa']); ?></td>
                                        <td><?php echo htmlspecialchars($activos['serial']); ?></td>
                                        <td>
                                            <?php
                                            $idestado = $activos['id_estado'];
                                            $estadoClase = '';
                                            $estadoTexto = '';
                                            
                                            switch($idestado) {
                                                case 1:
                                                    $estadoClase = 'status-excelente';
                                                    $estadoTexto = 'Excelente';
                                                    break;
                                                case 2:
                                                    $estadoClase = 'status-bueno';
                                                    $estadoTexto = 'Bueno';
                                                    break;
                                                case 3:
                                                    $estadoClase = 'status-regular';
                                                    $estadoTexto = 'Regular';
                                                    break;
                                                case 4:
                                                    $estadoClase = 'status-malo';
                                                    $estadoTexto = 'Malo';
                                                    break;
                                                case 5:
                                                    $estadoClase = 'status-hurtado';
                                                    $estadoTexto = 'Hurtado';
                                                    break;
                                                default:
                                                    $estadoClase = '';
                                                    $estadoTexto = 'Desconocido';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $estadoClase; ?>">
                                                <?php echo $estadoTexto; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">No se encontraron activos con los criterios seleccionados.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Función de búsqueda rápida
        $(document).ready(function () {
            $('#FiltrarContenido').on('keyup', function () {
                var valor = $(this).val().toLowerCase();
                $('.BusquedaRapida tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
                });
            });

            // Verificar si hay parámetros de error en la URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('export_error')) {
                alert('Error al exportar: ' + urlParams.get('export_error'));
            }
        });
    </script>
</body>
</html>
<?php
// Cerrar conexión
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>


