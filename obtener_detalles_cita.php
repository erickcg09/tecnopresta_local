<?php
session_start();

// Verificar sesión y permisos
if (!isset($_SESSION['cedula'])) {
    echo '<div class="alert alert-danger">Sesión no iniciada. Por favor, inicie sesión.</div>';
    exit();
}

require_once("conexion.php");
$link = $mysqli;
if (!mysqli_set_charset($link, "utf8mb4")) {
    printf("Error cargando el conjunto de caracteres utf8mb4: %s\n", mysqli_error($link));
    exit();
}
// Obtener ID de la cita desde GET
$id_cita = intval($_GET['id'] ?? 0);
$logusuario = $_SESSION['cedula'];

if ($id_cita <= 0) {
    echo '<div class="alert alert-danger">ID de cita inválido.</div>';
    exit();
}

// Consulta para obtener detalles de la cita
$sql = "SELECT 
            id,
            cedula_creador,
            nombre_creador,
            asunto,
            fecha_hora_inicio,
            duracion_minutos,
            descripcion,
            enlace_teams,
            estado,
            motivo_no_realizacion,
            destinatarios,
            recordatorio,
            evento_id,
            email_creador_microsoft,
            nombre_creador_microsoft,
            archivo_ics_generado,
            correos_enviados,
            token_acceso,
            fecha_creacion,
            fecha_actualizacion
        FROM t_control_citas_teams 
        WHERE id = ? AND cedula_creador = ?";
        
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo '<div class="alert alert-danger">Error preparando la consulta: ' . htmlspecialchars($mysqli->error) . '</div>';
    exit();
}

$stmt->bind_param("is", $id_cita, $logusuario);
$stmt->execute();
$result = $stmt->get_result();

if ($cita = $result->fetch_assoc()) {
    // Calcular fecha y hora de fin
    $fecha_inicio = new DateTime($cita['fecha_hora_inicio']);
    $fecha_fin = clone $fecha_inicio;
    $fecha_fin->add(new DateInterval('PT' . $cita['duracion_minutos'] . 'M'));
    
    // Formatear fechas
    $fecha_creacion = new DateTime($cita['fecha_creacion']);
    $fecha_actualizacion = new DateTime($cita['fecha_actualizacion']);
    
    // Determinar clase del badge según estado
    $badge_class = '';
    switch ($cita['estado']) {
        case 'programada':
            $badge_class = 'bg-primary';
            break;
        case 'realizada':
            $badge_class = 'bg-success';
            break;
        case 'cancelada':
            $badge_class = 'bg-danger';
            break;
    }
    
    // Parsear destinatarios
    $destinatarios = json_decode($cita['destinatarios'], true);
    $num_destinatarios = is_array($destinatarios) ? count($destinatarios) : 0;
    
    // Preparar texto de recordatorio
    $recordatorio_texto = ($cita['recordatorio'] == 'S') ? 'Sí (correo enviado)' : 'No';
    
    // Mostrar detalles
    ?>
    <style>
        .detail-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        .detail-value {
            color: #212529;
        }
        .badge-estado {
            font-size: 0.9em;
            padding: 5px 10px;
        }
        .destinatarios-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
        }
        .destinatario-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .destinatario-item:last-child {
            border-bottom: none;
        }
        .section-title {
            color: #2c3e50;
            border-left: 4px solid #3498db;
            padding-left: 10px;
            margin: 20px 0 15px 0;
            font-weight: 600;
        }
        .icon-label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .copy-btn {
            cursor: pointer;
            padding: 2px 8px;
            font-size: 0.8em;
            border-radius: 3px;
        }
        .url-container {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px;
            word-break: break-all;
            font-size: 0.9em;
        }
    </style>
    
    <div class="container-fluid">
        <!-- Encabezado con ID y estado -->
        <div class="row mb-4">
            <div class="col-6">
                <h5 class="detail-label">Cita #<?php echo $cita['id']; ?></h5>
                <p class="text-muted mb-0">Creada por: <?php echo htmlspecialchars($cita['nombre_creador']); ?></p>
                <small class="text-muted">Cédula: <?php echo htmlspecialchars($cita['cedula_creador']); ?></small>
            </div>
            <div class="col-6 text-end">
                <span class="badge <?php echo $badge_class; ?> badge-estado">
                    <?php echo strtoupper($cita['estado']); ?>
                </span>
                <?php if ($cita['estado'] == 'cancelada' && !empty($cita['motivo_no_realizacion'])): ?>
                    <div class="mt-2">
                        <small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Cancelada</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sección: Información de la cita -->
        <h6 class="section-title">
            <i class="bi bi-calendar-event"></i> Información de la cita
        </h6>
        
        <div class="row">
            <div class="col-md-6">
                <div class="detail-item">
                    <div class="detail-label">Asunto</div>
                    <div class="detail-value"><?php echo htmlspecialchars($cita['asunto']); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Fecha y hora</div>
                    <div class="detail-value">
                        <span class="icon-label">
                            <i class="bi bi-calendar"></i> <?php echo $fecha_inicio->format('d/m/Y'); ?>
                        </span>
                        <br>
                        <span class="icon-label">
                            <i class="bi bi-clock"></i> <?php echo $fecha_inicio->format('H:i'); ?> - <?php echo $fecha_fin->format('H:i'); ?>
                        </span>
                        <small class="text-muted"> (<?php echo $cita['duracion_minutos']; ?> minutos)</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="detail-item">
                    <div class="detail-label">Estado actual</div>
                    <div class="detail-value">
                        <span class="badge <?php echo $badge_class; ?>">
                            <?php echo ucfirst($cita['estado']); ?>
                        </span>
                        <?php if ($cita['estado'] == 'cancelada'): ?>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" 
                                    onclick="mostrarMotivo()">
                                <i class="bi bi-chat-left-text"></i> Ver motivo
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Recordatorio</div>
                    <div class="detail-value">
                        <span class="icon-label">
                            <?php if ($cita['recordatorio'] == 'S'): ?>
                                <i class="bi bi-bell-fill text-success"></i> Activado
                            <?php else: ?>
                                <i class="bi bi-bell-slash text-secondary"></i> No activado
                            <?php endif; ?>
                            <?php echo $recordatorio_texto; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Descripción -->
        <?php if (!empty($cita['descripcion'])): ?>
        <div class="detail-item">
            <div class="detail-label">Descripción</div>
            <div class="detail-value border rounded p-3 bg-light">
                <?php echo nl2br(htmlspecialchars($cita['descripcion'])); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Motivo de cancelación (oculto inicialmente) -->
        <?php if ($cita['estado'] == 'cancelada' && !empty($cita['motivo_no_realizacion'])): ?>
        <div class="detail-item" id="motivoCancelacion" style="display: none;">
            <div class="detail-label text-danger">Motivo de cancelación</div>
            <div class="detail-value border rounded p-3 bg-light">
                <i class="bi bi-exclamation-circle text-danger"></i>
                <?php echo nl2br(htmlspecialchars($cita['motivo_no_realizacion'])); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Sección: Enlace de Teams -->
        <h6 class="section-title mt-4">
            <i class="bi bi-microsoft-teams"></i> Enlace de la reunión
        </h6>
        
        <div class="detail-item">
            <div class="detail-label">URL de Teams</div>
            <div class="detail-value">
                <?php if (!empty($cita['enlace_teams'])): ?>
                    <div class="url-container">
                        <span id="enlaceTeams"><?php echo htmlspecialchars($cita['enlace_teams']); ?></span>
                        <button type="button" class="btn btn-sm btn-outline-primary copy-btn ms-2" 
                                onclick="copiarEnlace('<?php echo htmlspecialchars(addslashes($cita['enlace_teams'])); ?>')">
                            <i class="bi bi-copy"></i> Copiar
                        </button>
                    </div>
                    <div class="mt-2">
                        <a href="<?php echo htmlspecialchars($cita['enlace_teams']); ?>" 
                           target="_blank" class="btn btn-primary btn-sm">
                            <i class="bi bi-box-arrow-up-right"></i> Abrir en Teams
                        </a>
                    </div>
                <?php else: ?>
                    <span class="text-muted">No hay enlace disponible</span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sección: Destinatarios -->
        <h6 class="section-title mt-4">
            <i class="bi bi-people"></i> Destinatarios
        </h6>
        
        <div class="detail-item">
            <div class="detail-label">Personas invitadas</div>
            <div class="detail-value">
                <?php if ($num_destinatarios > 0): ?>
                    <div class="destinatarios-list">
                        <?php foreach ($destinatarios as $index => $dest): ?>
                            <div class="destinatario-item">
                                <div>
                                    <strong><?php echo htmlspecialchars($dest['nombre'] ?? 'Sin nombre'); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($dest['correo'] ?? 'Sin correo'); ?></small>
                                </div>
                                <div>
                                    <a href="mailto:<?php echo htmlspecialchars($dest['correo'] ?? ''); ?>" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-info">Total: <?php echo $num_destinatarios; ?> persona(s)</span>
                    </div>
                <?php else: ?>
                    <span class="text-muted">No hay destinatarios registrados</span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sección: Información técnica -->
        <h6 class="section-title mt-4">
            <i class="bi bi-gear"></i> Información técnica
        </h6>
        
        <div class="row">
            <div class="col-md-6">
                <div class="detail-item">
                    <div class="detail-label">Archivo ICS generado</div>
                    <div class="detail-value">
                        <span class="icon-label">
                            <?php if ($cita['archivo_ics_generado'] == 'S'): ?>
                                <i class="bi bi-file-check text-success"></i> Sí
                            <?php else: ?>
                                <i class="bi bi-file-x text-secondary"></i> No
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Correos enviados</div>
                    <div class="detail-value">
                        <span class="icon-label">
                            <?php if ($cita['correos_enviados'] == 'S'): ?>
                                <i class="bi bi-envelope-check text-success"></i> Sí
                            <?php else: ?>
                                <i class="bi bi-envelope-x text-secondary"></i> No
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="detail-item">
                    <div class="detail-label">Event ID (Microsoft)</div>
                    <div class="detail-value">
                        <?php if (!empty($cita['evento_id'])): ?>
                            <code><?php echo htmlspecialchars(substr($cita['evento_id'], 0, 30) . (strlen($cita['evento_id']) > 30 ? '...' : '')); ?></code>
                        <?php else: ?>
                            <span class="text-muted">No disponible</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Creador (Microsoft)</div>
                    <div class="detail-value">
                        <?php if (!empty($cita['nombre_creador_microsoft'])): ?>
                            <?php echo htmlspecialchars($cita['nombre_creador_microsoft']); ?>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars($cita['email_creador_microsoft'] ?? ''); ?></small>
                        <?php else: ?>
                            <span class="text-muted">No registrado</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sección: Fechas del sistema -->
        <h6 class="section-title mt-4">
            <i class="bi bi-clock-history"></i> Fechas del sistema
        </h6>
        
        <div class="row">
            <div class="col-md-6">
                <div class="detail-item">
                    <div class="detail-label">Fecha de creación</div>
                    <div class="detail-value">
                        <span class="icon-label">
                            <i class="bi bi-calendar-plus"></i> <?php echo $fecha_creacion->format('d/m/Y H:i:s'); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="detail-item">
                    <div class="detail-label">Última actualización</div>
                    <div class="detail-value">
                        <span class="icon-label">
                            <i class="bi bi-arrow-clockwise"></i> <?php echo $fecha_actualizacion->format('d/m/Y H:i:s'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function mostrarMotivo() {
            var motivoDiv = document.getElementById('motivoCancelacion');
            if (motivoDiv) {
                if (motivoDiv.style.display === 'none') {
                    motivoDiv.style.display = 'block';
                } else {
                    motivoDiv.style.display = 'none';
                }
            }
        }
        
        function copiarEnlace(enlace) {
            // Crear un elemento temporal para copiar
            var tempInput = document.createElement('input');
            tempInput.value = enlace;
            document.body.appendChild(tempInput);
            
            // Seleccionar y copiar
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // Para dispositivos móviles
            
            try {
                var successful = document.execCommand('copy');
                if (successful) {
                    // Mostrar notificación visual
                    var btn = event.target.closest('.copy-btn');
                    var originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check"></i> Copiado';
                    btn.classList.remove('btn-outline-primary');
                    btn.classList.add('btn-outline-success');
                    
                    // Restaurar después de 2 segundos
                    setTimeout(function() {
                        btn.innerHTML = originalHTML;
                        btn.classList.remove('btn-outline-success');
                        btn.classList.add('btn-outline-primary');
                    }, 2000);
                }
            } catch (err) {
                console.error('Error al copiar: ', err);
            }
            
            // Limpiar
            document.body.removeChild(tempInput);
        }
        
        // Para navegadores modernos (Clipboard API)
        function copiarEnlaceModerno(enlace) {
            navigator.clipboard.writeText(enlace).then(function() {
                var btn = event.target.closest('.copy-btn');
                var originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check"></i> Copiado';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-outline-success');
                
                setTimeout(function() {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('btn-outline-success');
                    btn.classList.add('btn-outline-primary');
                }, 2000);
            }).catch(function(err) {
                console.error('Error al copiar: ', err);
                // Fallback a método antiguo
                copiarEnlace(enlace);
            });
        }
        
        // Usar Clipboard API si está disponible
        if (navigator.clipboard) {
            // Sobreescribir función
            copiarEnlace = copiarEnlaceModerno;
        }
    </script>
    <?php
} else {
    echo '<div class="alert alert-warning">No se encontró la cita o no tiene permisos para verla.</div>';
}

$stmt->close();
?>