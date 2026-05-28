<?php
session_start();
require_once("conexion.php");

$link = $mysqli;

// Verificar permisos
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos para realizar esta acción");
    window.history.back();
    </script>';
    exit();
}

// Configurar conexión y charset
if (mysqli_connect_errno()) {
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link));
}

// Definir nomenclatura de lugares
$nomenclatura_lugares = [
    1 => 'BODEGA',
    2 => 'LABORATORIO', 
    3 => 'SALA DE ROBÓTICA',
    4 => 'AULAS',
    5 => 'BIBLIOTECA',
    6 => 'OFICINAS ADMINISTRATIVAS'
];

// Inicializar variables para mensajes
$mensaje = "";
$tipoMensaje = ""; // success, danger, warning
$resultados = [];
$totalRegistros = 0; // Nueva variable para el contador

// Procesar búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $codigo = mysqli_real_escape_string($link, $_POST['codigo']);
    $id_fondos = mysqli_real_escape_string($link, $_POST['id_fondos']);
    
    $query = "SELECT * FROM t_placa WHERE codigo = '$codigo' AND id_fondos = '$id_fondos' ORDER BY placa";
    $result = mysqli_query($link, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $resultados = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $totalRegistros = count($resultados); // Contar los registros encontrados
    } else {
        $mensaje = "No se encontraron registros con los criterios de búsqueda proporcionados.";
        $tipoMensaje = "warning";
        $totalRegistros = 0;
    }
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $updates = $_POST['updates'];
    $actualizacionesExitosas = 0;
    $errores = [];
    
    foreach ($updates as $id_placa => $datos) {
        // Solo procesar si el checkbox fue marcado
        if (!isset($datos['editar'])) {
            continue;
        }
        
        $nuevo_id_lugar = mysqli_real_escape_string($link, trim($datos['nuevo_id_lugar']));
        
        // Validar que el campo no esté vacío
        if (empty($nuevo_id_lugar)) {
            $errores[] = "El campo ID Lugar no puede estar vacío para el registro ID: $id_placa";
            continue;
        }
        
        // Validar que sea numérico
        if (!is_numeric($nuevo_id_lugar)) {
            $errores[] = "El ID Lugar debe ser un valor numérico para el registro ID: $id_placa";
            continue;
        }
        
        // Validar que esté en el rango permitido (1-6)
        if ($nuevo_id_lugar < 1 || $nuevo_id_lugar > 6) {
            $errores[] = "El ID Lugar debe estar entre 1 y 6 para el registro ID: $id_placa";
            continue;
        }
        
        // Actualizar registro - solo el campo id_lugar
        $update_query = "UPDATE t_placa SET id_lugar = '$nuevo_id_lugar' WHERE id_placa = '$id_placa'";
        if (mysqli_query($link, $update_query)) {
            $actualizacionesExitosas++;
        } else {
            $errores[] = "Error al actualizar registro ID: $id_placa - " . mysqli_error($link);
        }
    }
    
    // Preparar mensaje de resultado
    if ($actualizacionesExitosas > 0) {
        $mensaje = "Se actualizaron exitosamente $actualizacionesExitosas registros.";
        $tipoMensaje = "success";
    }
    
    if (!empty($errores)) {
        $mensaje .= " Errores: " . implode(", ", $errores);
        $tipoMensaje = "danger";
    }
    
    // Volver a buscar para mostrar los datos actualizados
    if (!empty($_POST['codigo']) && !empty($_POST['id_fondos'])) {
        $codigo = mysqli_real_escape_string($link, $_POST['codigo']);
        $id_fondos = mysqli_real_escape_string($link, $_POST['id_fondos']);
        
        $query = "SELECT * FROM t_placa WHERE codigo = '$codigo' AND id_fondos = '$id_fondos' ORDER BY placa";
        $result = mysqli_query($link, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $resultados = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $totalRegistros = count($resultados); // Actualizar contador después de la actualización
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edición de ID Lugar</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }
        .header-fixed {
            position: sticky;
            top: 0;
            background: white;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        .contador-registros {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: bold;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .contador-container {
            margin-bottom: 15px;
        }
        .nomenclatura-table {
            font-size: 0.9rem;
        }
        .nomenclatura-table th {
            background-color: #e9ecef;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0c63e4;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-md bg-dark navbar-dark">
        <img src="img/logodelgobierno.png" width="35" height="30" alt="" loading="lazy">
        <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="herramientas.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply-all" viewBox="0 0 16 16">
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
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Edición de ID Lugar</h3>
                    </div>
                    <div class="card-body">
                        <!-- Mostrar mensajes -->
                        <?php if (!empty($mensaje)): ?>
                        <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                            <?php echo $mensaje; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Formulario de búsqueda -->
                        <form method="POST" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="codigo" class="form-label required-field">Código</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" required 
                                           value="<?php echo isset($_POST['codigo']) ? htmlspecialchars($_POST['codigo']) : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="id_fondos" class="form-label required-field">ID Fondos</label>
                                    <input type="number" class="form-control" id="id_fondos" name="id_fondos" required 
                                           value="<?php echo isset($_POST['id_fondos']) ? htmlspecialchars($_POST['id_fondos']) : ''; ?>">
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="buscar" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Acordeón con la nomenclatura de lugares -->
                        <div class="accordion mb-4" id="accordionNomenclatura">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingNomenclatura">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapseNomenclatura" aria-expanded="false" 
                                            aria-controls="collapseNomenclatura">
                                        <i class="fas fa-map-marker-alt me-2"></i> Nomenclatura de Lugares (ID Lugar)
                                    </button>
                                </h2>
                                <div id="collapseNomenclatura" class="accordion-collapse collapse" 
                                     aria-labelledby="headingNomenclatura" data-bs-parent="#accordionNomenclatura">
                                    <div class="accordion-body">
                                        <p class="text-muted mb-3">Utilice los siguientes códigos para asignar la ubicación del activo:</p>
                                        <div class="table-responsive">
                                            <table class="table table-bordered nomenclatura-table">
                                                <thead>
                                                    <tr>
                                                        <th width="20%" class="text-center">ID Lugar</th>
                                                        <th width="80%">Descripción</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($nomenclatura_lugares as $id => $descripcion): ?>
                                                    <tr>
                                                        <td class="text-center fw-bold"><?php echo $id; ?></td>
                                                        <td><?php echo $descripcion; ?></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($resultados)): ?>
                        <!-- Contador de registros -->
                        <div class="contador-container">
                            <div class="contador-registros">
                                <i class="fas fa-database me-2"></i>
                                Registros encontrados: 
                                <span id="contador"><?php echo $totalRegistros; ?></span>
                                <?php if ($totalRegistros == 1): ?>
                                    registro
                                <?php else: ?>
                                    registros
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Formulario de edición -->
                        <form method="POST" id="form-edicion">
                            <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($_POST['codigo']); ?>">
                            <input type="hidden" name="id_fondos" value="<?php echo htmlspecialchars($_POST['id_fondos']); ?>">
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="header-fixed">
                                        <tr>
                                            <th width="5%">Editar</th>
                                            <th width="15%">Placa</th>
                                            <th width="15%">Serial</th>
                                            <th width="15%">ID Lugar Actual</th>
                                            <th width="15%">Nuevo ID Lugar</th>
                                            <th width="15%">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultados as $registro): 
                                            $id_lugar_actual = $registro['id_lugar'] ?? '';
                                            $descripcion_lugar = isset($nomenclatura_lugares[$id_lugar_actual]) ? $nomenclatura_lugares[$id_lugar_actual] : 'No asignado';
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="updates[<?php echo $registro['id_placa']; ?>][editar]" 
                                                       class="form-check-input check-editar" value="1">
                                            </td>
                                            <td><?php echo htmlspecialchars($registro['placa']); ?></td>
                                            <td><?php echo htmlspecialchars($registro['serial']); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($id_lugar_actual); ?>
                                                <?php if (!empty($id_lugar_actual)): ?>
                                                    <br><small class="text-muted">(<?php echo $descripcion_lugar; ?>)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <input type="number" name="updates[<?php echo $registro['id_placa']; ?>][nuevo_id_lugar]" 
                                                       class="form-control campo-edicion" placeholder="Nuevo ID Lugar" 
                                                       value="<?php echo htmlspecialchars($id_lugar_actual); ?>" 
                                                       min="1" max="6" disabled>
                                                <small class="text-muted">(1-6)</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $registro['activo'] ? 'success' : 'danger'; ?>">
                                                    <?php echo $registro['activo'] ? 'Activo' : 'Inactivo'; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" name="actualizar" class="btn btn-success" id="btn-actualizar" disabled>
                                    <i class="fas fa-save me-1"></i> Actualizar Seleccionados
                                </button>
                                <span class="ms-2 text-muted" id="contador-seleccionados">
                                    (0 seleccionados)
                                </span>
                            </div>
                        </form>
                        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])): ?>
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i> No se encontraron registros con los criterios de búsqueda proporcionados.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Habilitar/deshabilitar campos de edición según checkbox
            $('.check-editar').change(function() {
                const fila = $(this).closest('tr');
                const habilitar = this.checked;
                
                fila.find('.campo-edicion').prop('disabled', !habilitar);
                
                // Verificar si hay algún checkbox seleccionado
                const haySeleccionados = $('.check-editar:checked').length > 0;
                $('#btn-actualizar').prop('disabled', !haySeleccionados);
                
                // Actualizar contador de seleccionados
                const cantidadSeleccionados = $('.check-editar:checked').length;
                $('#contador-seleccionados').text('(' + cantidadSeleccionados + ' seleccionados)');
            });
            
            // Validación antes de enviar
            $('#form-edicion').submit(function() {
                let errores = [];
                
                $('.check-editar:checked').each(function() {
                    const fila = $(this).closest('tr');
                    const nuevoIdLugar = fila.find('input[name*="nuevo_id_lugar"]').val().trim();
                    
                    if (!nuevoIdLugar) {
                        errores.push('El campo Nuevo ID Lugar no puede estar vacío');
                    }
                    
                    if (!$.isNumeric(nuevoIdLugar)) {
                        errores.push('El campo Nuevo ID Lugar debe ser un número');
                    }
                    
                    const idLugarNum = parseInt(nuevoIdLugar);
                    if (idLugarNum < 1 || idLugarNum > 6) {
                        errores.push('El ID Lugar debe estar entre 1 y 6');
                    }
                });
                
                if (errores.length > 0) {
                    alert('Errores encontrados:\n' + errores.join('\n'));
                    return false;
                }
                
                return confirm('¿Está seguro de que desea actualizar los registros seleccionados?');
            });
        });
    </script>
</body>
</html>