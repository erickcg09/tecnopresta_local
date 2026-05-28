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

// Consulta para obtener destinatarios de la cita
$sql = "SELECT 
            asunto,
            fecha_hora_inicio,
            destinatarios,
            estado
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
    // Parsear destinatarios del JSON
    $destinatarios = json_decode($cita['destinatarios'], true);
    
    if (is_array($destinatarios) && count($destinatarios) > 0) {
        // Formatear fecha
        $fecha_inicio = new DateTime($cita['fecha_hora_inicio']);
        
        // Determinar color según estado
        $estado_color = '';
        switch ($cita['estado']) {
            case 'programada': $estado_color = 'primary'; break;
            case 'realizada': $estado_color = 'success'; break;
            case 'cancelada': $estado_color = 'danger'; break;
        }
        
        ?>
        <style>
            .destinatario-card {
                border: 1px solid #dee2e6;
                border-radius: 8px;
                margin-bottom: 10px;
                transition: all 0.3s ease;
                background-color: white;
            }
            .destinatario-card:hover {
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                transform: translateY(-2px);
            }
            .destinatario-header {
                background-color: #f8f9fa;
                padding: 12px 15px;
                border-bottom: 1px solid #dee2e6;
                border-radius: 8px 8px 0 0;
            }
            .destinatario-body {
                padding: 15px;
            }
            .destinatario-nombre {
                font-weight: 600;
                color: #2c3e50;
                font-size: 1.1em;
            }
            .destinatario-email {
                color: #3498db;
                word-break: break-all;
                font-size: 0.95em;
            }
            .action-buttons {
                display: flex;
                gap: 8px;
                margin-top: 10px;
            }
            .copy-btn {
                cursor: pointer;
                font-size: 0.85em;
            }
            .header-info {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }
            .cita-info {
                background-color: #e8f4fc;
                border-radius: 6px;
                padding: 10px;
                margin-bottom: 15px;
            }
            .empty-state {
                text-align: center;
                padding: 40px 20px;
                color: #6c757d;
            }
            .empty-state i {
                font-size: 3em;
                margin-bottom: 15px;
                display: block;
            }
            .badge-estado {
                font-size: 0.8em;
                padding: 4px 8px;
            }
            .export-buttons {
                display: flex;
                gap: 10px;
                justify-content: center;
                margin-top: 20px;
            }
            @media (max-width: 768px) {
                .header-info {
                    flex-direction: column;
                    align-items: flex-start;
                }
                .action-buttons {
                    flex-wrap: wrap;
                }
            }
        </style>
        
        <!-- Información de la cita -->
        <div class="cita-info">
            <div class="header-info">
                <div>
                    <h6 class="mb-1"><?php echo htmlspecialchars($cita['asunto']); ?></h6>
                    <p class="mb-0 text-muted">
                        <i class="bi bi-calendar"></i> 
                        <?php echo $fecha_inicio->format('d/m/Y H:i'); ?>
                    </p>
                </div>
                <div>
                    <span class="badge bg-<?php echo $estado_color; ?> badge-estado">
                        <?php echo strtoupper($cita['estado']); ?>
                    </span>
                    <span class="badge bg-info badge-estado">
                        <?php echo count($destinatarios); ?> destinatario(s)
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Lista de destinatarios -->
        <div class="destinatarios-list">
            <?php 
            $contador = 0;
            foreach ($destinatarios as $index => $dest): 
                $contador++;
                $nombre = htmlspecialchars($dest['nombre'] ?? 'Sin nombre');
                $email = htmlspecialchars($dest['correo'] ?? '');
                $email_sin_html = $dest['correo'] ?? '';
            ?>
                <div class="destinatario-card" id="destinatario-<?php echo $contador; ?>">
                    <div class="destinatario-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="destinatario-nombre">
                                <i class="bi bi-person-circle"></i> 
                                <?php echo $nombre; ?>
                            </span>
                            <span class="badge bg-secondary">#<?php echo $contador; ?></span>
                        </div>
                    </div>
                    <div class="destinatario-body">
                        <div class="mb-2">
                            <span class="destinatario-email">
                                <i class="bi bi-envelope"></i> 
                                <?php echo $email ? $email : '<span class="text-muted">Sin correo</span>'; ?>
                            </span>
                        </div>
                        
                        <div class="action-buttons">
                            <?php if (!empty($email_sin_html)): ?>
                                <a href="mailto:<?php echo $email_sin_html; ?>?subject=<?php echo urlencode('Recordatorio: ' . $cita['asunto']); ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-envelope-paper"></i> Enviar correo
                                </a>
                                
                                <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" 
                                        onclick="copiarTexto('<?php echo addslashes($email_sin_html); ?>', this)">
                                    <i class="bi bi-copy"></i> Copiar email
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-outline-info copy-btn" 
                                        onclick="copiarTexto('<?php echo addslashes($nombre); ?>', this, 'nombre')">
                                    <i class="bi bi-person-badge"></i> Copiar nombre
                                </button>
                            <?php else: ?>
                                <span class="text-muted">No hay correo disponible</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Resumen y acciones -->
        <div class="mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-info-circle"></i> Resumen</h6>
                            <ul class="list-unstyled mb-0">
                                <li><strong>Total destinatarios:</strong> <?php echo count($destinatarios); ?></li>
                                <li><strong>Con email:</strong> 
                                    <?php 
                                    $con_email = 0;
                                    foreach ($destinatarios as $dest) {
                                        if (!empty($dest['correo'])) $con_email++;
                                    }
                                    echo $con_email;
                                    ?>
                                </li>
                                <li><strong>Sin email:</strong> <?php echo count($destinatarios) - $con_email; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-secondary">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-download"></i> Exportar</h6>
                            <div class="export-buttons">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="exportarLista('csv')">
                                    <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" 
                                        onclick="exportarLista('texto')">
                                    <i class="bi bi-file-text"></i> Texto
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-dark" 
                                        onclick="imprimirLista()">
                                    <i class="bi bi-printer"></i> Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            function copiarTexto(texto, boton, tipo = 'email') {
                // Crear un elemento temporal para copiar
                var tempInput = document.createElement('input');
                tempInput.value = texto;
                document.body.appendChild(tempInput);
                
                // Seleccionar y copiar
                tempInput.select();
                tempInput.setSelectionRange(0, 99999);
                
                try {
                    var successful = document.execCommand('copy');
                    if (successful) {
                        // Mostrar notificación visual
                        var originalHTML = boton.innerHTML;
                        if (tipo === 'email') {
                            boton.innerHTML = '<i class="bi bi-check"></i> Copiado';
                        } else {
                            boton.innerHTML = '<i class="bi bi-check"></i> Nombre copiado';
                        }
                        boton.classList.remove('btn-outline-secondary', 'btn-outline-info');
                        boton.classList.add('btn-outline-success');
                        
                        // Restaurar después de 2 segundos
                        setTimeout(function() {
                            boton.innerHTML = originalHTML;
                            boton.classList.remove('btn-outline-success');
                            if (tipo === 'email') {
                                boton.classList.add('btn-outline-secondary');
                            } else {
                                boton.classList.add('btn-outline-info');
                            }
                        }, 2000);
                    }
                } catch (err) {
                    console.error('Error al copiar: ', err);
                }
                
                // Limpiar
                document.body.removeChild(tempInput);
            }
            
            // Función para exportar a CSV
            function exportarLista(formato) {
                var destinatarios = <?php echo json_encode($destinatarios); ?>;
                var asunto = "<?php echo addslashes($cita['asunto']); ?>";
                var fecha = "<?php echo $fecha_inicio->format('d/m/Y H:i'); ?>";
                var estado = "<?php echo $cita['estado']; ?>";
                
                var contenido = '';
                
                if (formato === 'csv') {
                    // Cabecera CSV
                    contenido = "Nombre,Email\n";
                    destinatarios.forEach(function(dest) {
                        contenido += '"' + (dest.nombre || 'Sin nombre') + '","' + (dest.correo || '') + '"\n';
                    });
                    
                    // Crear y descargar archivo
                    descargarArchivo(contenido, 'destinatarios_cita_<?php echo $id_cita; ?>.csv', 'text/csv');
                } else if (formato === 'texto') {
                    // Formato de texto plano
                    contenido = "LISTA DE DESTINATARIOS\n";
                    contenido += "=======================\n";
                    contenido += "Cita: " + asunto + "\n";
                    contenido += "Fecha: " + fecha + "\n";
                    contenido += "Estado: " + estado + "\n";
                    contenido += "Total: " + destinatarios.length + " destinatarios\n\n";
                    
                    destinatarios.forEach(function(dest, index) {
                        contenido += (index + 1) + ". " + (dest.nombre || 'Sin nombre') + "\n";
                        contenido += "   Email: " + (dest.correo || 'No disponible') + "\n\n";
                    });
                    
                    descargarArchivo(contenido, 'destinatarios_cita_<?php echo $id_cita; ?>.txt', 'text/plain');
                }
            }
            
            function descargarArchivo(contenido, nombre, tipo) {
                var blob = new Blob([contenido], {type: tipo + ';charset=utf-8;'});
                var link = document.createElement("a");
                
                if (navigator.msSaveBlob) { // IE 10+
                    navigator.msSaveBlob(blob, nombre);
                } else {
                    var url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", nombre);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
            
            function imprimirLista() {
                var ventana = window.open('', '_blank');
                var contenido = '<html><head><title>Destinatarios - Cita #<?php echo $id_cita; ?></title>';
                contenido += '<style>body{font-family:Arial,sans-serif;padding:20px;}';
                contenido += 'h1{color:#2c3e50;} .destinatario{border-bottom:1px solid #ccc;padding:10px 0;}';
                contenido += '.email{color:#3498db;} .resumen{background:#f8f9fa;padding:15px;border-radius:5px;margin:20px 0;}</style>';
                contenido += '</head><body>';
                
                contenido += '<h1>Destinatarios de la cita</h1>';
                contenido += '<div class="resumen">';
                contenido += '<p><strong>Cita:</strong> <?php echo htmlspecialchars($cita['asunto']); ?></p>';
                contenido += '<p><strong>Fecha:</strong> <?php echo $fecha_inicio->format('d/m/Y H:i'); ?></p>';
                contenido += '<p><strong>Estado:</strong> <?php echo $cita['estado']; ?></p>';
                contenido += '<p><strong>Total destinatarios:</strong> <?php echo count($destinatarios); ?></p>';
                contenido += '</div>';
                
                contenido += '<h2>Lista de destinatarios</h2>';
                
                <?php foreach ($destinatarios as $index => $dest): ?>
                    contenido += '<div class="destinatario">';
                    contenido += '<h3><?php echo $index + 1; ?>. <?php echo htmlspecialchars($dest['nombre'] ?? 'Sin nombre'); ?></h3>';
                    contenido += '<p class="email"><strong>Email:</strong> <?php echo htmlspecialchars($dest['correo'] ?? 'No disponible'); ?></p>';
                    contenido += '</div>';
                <?php endforeach; ?>
                
                contenido += '<div style="margin-top:30px;text-align:center;color:#777;font-size:0.9em;">';
                contenido += '<p>Generado el <?php echo date('d/m/Y H:i'); ?> por <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>';
                contenido += '</div>';
                
                contenido += '</body></html>';
                
                ventana.document.write(contenido);
                ventana.document.close();
                ventana.print();
            }
            
            // Para navegadores modernos (Clipboard API)
            if (navigator.clipboard) {
                // Sobreescribir función copiarTexto
                window.copiarTexto = function(texto, boton, tipo = 'email') {
                    navigator.clipboard.writeText(texto).then(function() {
                        var originalHTML = boton.innerHTML;
                        if (tipo === 'email') {
                            boton.innerHTML = '<i class="bi bi-check"></i> Copiado';
                        } else {
                            boton.innerHTML = '<i class="bi bi-check"></i> Nombre copiado';
                        }
                        boton.classList.remove('btn-outline-secondary', 'btn-outline-info');
                        boton.classList.add('btn-outline-success');
                        
                        setTimeout(function() {
                            boton.innerHTML = originalHTML;
                            boton.classList.remove('btn-outline-success');
                            if (tipo === 'email') {
                                boton.classList.add('btn-outline-secondary');
                            } else {
                                boton.classList.add('btn-outline-info');
                            }
                        }, 2000);
                    }).catch(function(err) {
                        console.error('Error al copiar: ', err);
                        // Fallback a método antiguo
                        copiarTextoFallback(texto, boton, tipo);
                    });
                };
                
                function copiarTextoFallback(texto, boton, tipo) {
                    var tempInput = document.createElement('input');
                    tempInput.value = texto;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    tempInput.setSelectionRange(0, 99999);
                    
                    try {
                        document.execCommand('copy');
                        var originalHTML = boton.innerHTML;
                        if (tipo === 'email') {
                            boton.innerHTML = '<i class="bi bi-check"></i> Copiado';
                        } else {
                            boton.innerHTML = '<i class="bi bi-check"></i> Nombre copiado';
                        }
                        boton.classList.remove('btn-outline-secondary', 'btn-outline-info');
                        boton.classList.add('btn-outline-success');
                        
                        setTimeout(function() {
                            boton.innerHTML = originalHTML;
                            boton.classList.remove('btn-outline-success');
                            if (tipo === 'email') {
                                boton.classList.add('btn-outline-secondary');
                            } else {
                                boton.classList.add('btn-outline-info');
                            }
                        }, 2000);
                    } catch (err) {
                        console.error('Error al copiar: ', err);
                    }
                    
                    document.body.removeChild(tempInput);
                }
            }
        </script>
        <?php
    } else {
        // No hay destinatarios
        ?>
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <h5>No hay destinatarios</h5>
            <p>Esta cita no tiene destinatarios registrados.</p>
            <div class="cita-info mt-3">
                <p><strong>Cita:</strong> <?php echo htmlspecialchars($cita['asunto']); ?></p>
                <p><strong>Fecha:</strong> <?php echo $fecha_inicio->format('d/m/Y H:i'); ?></p>
            </div>
        </div>
        <?php
    }
} else {
    echo '<div class="alert alert-warning">No se encontró la cita o no tiene permisos para verla.</div>';
}

$stmt->close();
?>