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

if (!mysqli_set_charset($link, "utf8mb4")) {
    printf("Error cargando el conjunto de caracteres utf8mb4: %s\n", mysqli_error($link));
    exit();
}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcorreo = $_SESSION['correomep'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
date_default_timezone_set('America/Costa_Rica');

// Procesar actualización de estado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['actualizar_estado'])) {
        $id_cita = intval($_POST['id_cita']);
        $nuevo_estado = $_POST['nuevo_estado'];
        $motivo_cancelacion = isset($_POST['motivo_cancelacion']) ? $_POST['motivo_cancelacion'] : null;
        
        // Verificar que la cita pertenece al usuario logueado
        $verificar_sql = "SELECT id FROM t_control_citas_teams WHERE id = ? AND cedula_creador = ?";
        $verificar_stmt = $link->prepare($verificar_sql);
        $verificar_stmt->bind_param("is", $id_cita, $logusuario);
        $verificar_stmt->execute();
        $verificar_stmt->store_result();
        
        if ($verificar_stmt->num_rows > 0) {
            // Actualizar estado
            if ($nuevo_estado == 'cancelada') {
                $sql = "UPDATE t_control_citas_teams SET estado = ?, motivo_no_realizacion = ? WHERE id = ?";
                $stmt = $link->prepare($sql);
                $stmt->bind_param("ssi", $nuevo_estado, $motivo_cancelacion, $id_cita);
            } else {
                $sql = "UPDATE t_control_citas_teams SET estado = ?, motivo_no_realizacion = NULL WHERE id = ?";
                $stmt = $link->prepare($sql);
                $stmt->bind_param("si", $nuevo_estado, $id_cita);
            }
            
            if ($stmt->execute()) {
                $mensaje = "success|Estado de la cita actualizado correctamente.";
            } else {
                $mensaje = "error|Error al actualizar el estado: " . $link->error;
            }
            $stmt->close();
        } else {
            $mensaje = "error|No tiene permisos para modificar esta cita o la cita no existe.";
        }
        $verificar_stmt->close();
    }
}

// Obtener citas del usuario
$sql = "SELECT 
            id,
            asunto,
            fecha_hora_inicio,
            duracion_minutos,
            descripcion,
            enlace_teams,
            estado,
            motivo_no_realizacion,
            destinatarios,
            fecha_creacion,
            archivo_ics_generado,
            correos_enviados
        FROM t_control_citas_teams 
        WHERE cedula_creador = ? 
        ORDER BY fecha_hora_inicio DESC";
        
$stmt = $link->prepare($sql);
$stmt->bind_param("s", $logusuario);
$stmt->execute();
$result = $stmt->get_result();
$citas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Citas - TecnoPresta</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #2c3e50;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        .badge-programada {
            background-color: #3498db;
        }
        .badge-realizada {
            background-color: #2ecc71;
        }
        .badge-cancelada {
            background-color: #e74c3c;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .modal-backdrop {
            z-index: 1040;
        }
        .modal {
            z-index: 1050;
        }
        .table th {
            background-color: #2c3e50;
            color: white;
            border-bottom: none;
        }
        .table td {
            vertical-align: middle;
        }
        .estado-select {
            min-width: 140px;
        }
        .motivo-textarea {
            resize: vertical;
            min-height: 100px;
        }
        .fecha-col {
            min-width: 150px;
        }
        .acciones-col {
            min-width: 200px;
        }
        .dataTables_wrapper {
            margin-top: 20px;
        }
        .pagination .page-item.active .page-link {
            background-color: #2c3e50;
            border-color: #2c3e50;
        }
        .pagination .page-link {
            color: #2c3e50;
        }
        .destinatarios-list {
            max-height: 100px;
            overflow-y: auto;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
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
                        <a class="nav-link" href="ver_citas_de_soportista.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>
                        <li class="nav-item">
                        <a class="nav-link" href="formulario_reporte_citas.php">
                            <i class="bi bi-file-pdf"></i> Generar reporte
                        </a>
                    </li>
                </ul>
                <span class="navbar-text text-white">
                    <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($lognombre); ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <?php if (isset($mensaje)): 
            $parts = explode("|", $mensaje);
            $type = $parts[0];
            $text = $parts[1];
        ?>
            <div class="alert alert-<?php echo $type == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($text); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-check"></i> Mis Citas Programadas</span>
                <span class="badge bg-primary"><?php echo count($citas); ?> citas</span>
            </div>
            <div class="card-body">
                <?php if (count($citas) > 0): ?>
                    <div class="table-responsive">
                        <table id="tablaCitas" class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Asunto</th>
                                    <th class="fecha-col">Fecha y Hora</th>
                                    <th>Duración</th>
                                    <th>Estado</th>
                                    <th>Destinatarios</th>
                                    <th>Enlace Teams</th>
                                    <th class="acciones-col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($citas as $cita): 
                                    $fecha = new DateTime($cita['fecha_hora_inicio']);
                                    $hora_fin = clone $fecha;
                                    $hora_fin->add(new DateInterval('PT' . $cita['duracion_minutos'] . 'M'));
                                    
                                    // Parsear destinatarios JSON
                                    $destinatarios = json_decode($cita['destinatarios'], true);
                                    $num_destinatarios = is_array($destinatarios) ? count($destinatarios) : 0;
                                    
                                    // Determinar clase del badge según estado
                                    $badge_class = '';
                                    switch ($cita['estado']) {
                                        case 'programada':
                                            $badge_class = 'badge-programada';
                                            break;
                                        case 'realizada':
                                            $badge_class = 'badge-realizada';
                                            break;
                                        case 'cancelada':
                                            $badge_class = 'badge-cancelada';
                                            break;
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo $cita['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($cita['asunto']); ?></strong>
                                            <?php if (!empty($cita['descripcion'])): ?>
                                                <br><small class="text-muted"><?php echo nl2br(htmlspecialchars(substr($cita['descripcion'], 0, 100) . (strlen($cita['descripcion']) > 100 ? '...' : ''))); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fecha-col">
                                            <?php echo $fecha->format('d/m/Y'); ?><br>
                                            <small class="text-muted">
                                                <?php echo $fecha->format('H:i'); ?> - <?php echo $hora_fin->format('H:i'); ?>
                                            </small>
                                        </td>
                                        <td><?php echo $cita['duracion_minutos']; ?> min</td>
                                        <td>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <?php echo ucfirst($cita['estado']); ?>
                                            </span>
                                            <?php if ($cita['estado'] == 'cancelada' && !empty($cita['motivo_no_realizacion'])): ?>
                                                <br><small class="text-danger"><?php echo htmlspecialchars(substr($cita['motivo_no_realizacion'], 0, 50) . (strlen($cita['motivo_no_realizacion']) > 50 ? '...' : '')); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="destinatarios-list">
                                                <?php if ($num_destinatarios > 0): ?>
                                                    <span class="badge bg-secondary"><?php echo $num_destinatarios; ?></span>
                                                    <button type="button" class="btn btn-link btn-sm p-0" onclick="mostrarDestinatarios(<?php echo $cita['id']; ?>)">
                                                        Ver lista
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">Sin destinatarios</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($cita['enlace_teams']): ?>
                                                <a href="<?php echo htmlspecialchars($cita['enlace_teams']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-microsoft-teams"></i> Unirse
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Sin enlace</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="acciones-col">
                                            <div class="d-flex flex-column gap-2">
                                                <!-- Formulario para cambiar estado -->
                                                <form method="POST" class="d-inline" onsubmit="return validarCancelacion(<?php echo $cita['id']; ?>, this)">
                                                    <input type="hidden" name="id_cita" value="<?php echo $cita['id']; ?>">
                                                    <div class="input-group input-group-sm">
                                                        <select name="nuevo_estado" class="form-select estado-select" onchange="toggleMotivo(this, <?php echo $cita['id']; ?>)">
                                                            <option value="programada" <?php echo $cita['estado'] == 'programada' ? 'selected' : ''; ?>>Programada</option>
                                                            <option value="realizada" <?php echo $cita['estado'] == 'realizada' ? 'selected' : ''; ?>>Realizada</option>
                                                            <option value="cancelada" <?php echo $cita['estado'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                                        </select>
                                                        <button type="submit" name="actualizar_estado" class="btn btn-primary btn-sm">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Campo para motivo de cancelación (oculto por defecto) -->
                                                    <div id="motivo-container-<?php echo $cita['id']; ?>" class="mt-2" style="display: none;">
                                                        <textarea name="motivo_cancelacion" class="form-control form-control-sm motivo-textarea" 
                                                                  placeholder="Motivo de cancelación..." maxlength="500"><?php echo htmlspecialchars($cita['motivo_no_realizacion'] ?? ''); ?></textarea>
                                                        <small class="text-muted">Máximo 500 caracteres</small>
                                                    </div>
                                                </form>
                                                
                                                <!-- Botones adicionales -->
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-info" onclick="verDetalles(<?php echo $cita['id']; ?>)">
                                                        <i class="bi bi-eye"></i> Detalles
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                        <h4 class="text-muted">No hay citas programadas</h4>
                        <p class="text-muted">No has creado ninguna cita todavía.</p>
                        <a href="ver_citas_de_soportista.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Crear nueva cita
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-muted">
                <div class="row">
                    <div class="col-md-6">
                        <small>
                            <i class="bi bi-info-circle"></i> 
                            Última actualización: <?php echo date('d/m/Y H:i:s'); ?>
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small>
                            <span class="badge bg-primary">Programada</span>
                            <span class="badge bg-success">Realizada</span>
                            <span class="badge bg-danger">Cancelada</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles -->
    <div class="modal fade" id="detallesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalles de la Cita</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detallesContenido">
                    <!-- Contenido cargado por AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver destinatarios -->
    <div class="modal fade" id="destinatariosModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Destinatarios de la Cita</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="destinatariosContenido">
                    <!-- Contenido cargado por AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#tablaCitas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                pageLength: 10,
                responsive: true
            });
            
            // Mostrar/ocultar motivo de cancelación según selección
            $('select[name="nuevo_estado"]').each(function() {
                var id = $(this).closest('form').find('input[name="id_cita"]').val();
                if ($(this).val() === 'cancelada') {
                    $('#motivo-container-' + id).show();
                }
            });
        });
        
        function toggleMotivo(select, idCita) {
            var motivoContainer = $('#motivo-container-' + idCita);
            if (select.value === 'cancelada') {
                motivoContainer.slideDown();
            } else {
                motivoContainer.slideUp();
            }
        }
        
        function validarCancelacion(idCita, form) {
            var estado = $(form).find('select[name="nuevo_estado"]').val();
            var motivo = $(form).find('textarea[name="motivo_cancelacion"]').val();
            
            if (estado === 'cancelada' && (!motivo || motivo.trim() === '')) {
                alert('Por favor, ingrese el motivo de cancelación.');
                return false;
            }
            
            return confirm('¿Está seguro de cambiar el estado de esta cita?');
        }
        
        function verDetalles(idCita) {
            $.ajax({
                url: 'obtener_detalles_cita.php',
                type: 'GET',
                data: { id: idCita },
                success: function(response) {
                    $('#detallesContenido').html(response);
                    $('#detallesModal').modal('show');
                },
                error: function() {
                    alert('Error al cargar los detalles de la cita.');
                }
            });
        }
        
        function mostrarDestinatarios(idCita) {
            $.ajax({
                url: 'obtener_destinatarios_cita.php',
                type: 'GET',
                data: { id: idCita },
                success: function(response) {
                    $('#destinatariosContenido').html(response);
                    $('#destinatariosModal').modal('show');
                },
                error: function() {
                    alert('Error al cargar la lista de destinatarios.');
                }
            });
        }
        
        function reenviarCorreo(idCita) {
            if (confirm('¿Desea reenviar el correo de invitación para esta cita?')) {
                $.ajax({
                    url: 'reenviar_correo_cita.php',
                    type: 'POST',
                    data: { id: idCita },
                    success: function(response) {
                        alert(response);
                    },
                    error: function() {
                        alert('Error al reenviar el correo.');
                    }
                });
            }
        }
    </script>
</body>
</html>