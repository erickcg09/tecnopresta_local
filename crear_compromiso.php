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

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_menor = $_POST['nombre_menor'];
    $documento_menor = $_POST['documento_menor'];
    $nombre_responsable = $_POST['nombre_responsable'];
    $documento_responsable = $_POST['documento_responsable'];
    $dato_adicional = $_POST['dato_adicional'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    
    try {
        // Insertar en la base de datos
        $sql = "INSERT INTO compromisos (nombre_menor, documento_menor, nombre_responsable, documento_responsable, dato_adicional, fecha_inicio, fecha_vencimiento, institucion, representante_institucion, codigo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_menor, $documento_menor, $nombre_responsable, $documento_responsable, $dato_adicional, $fecha_inicio, $fecha_vencimiento, $institucion, $representante, $codigo]);
        
        $id_compromiso = $pdo->lastInsertId();
        
        // Generar PDF del contrato con FPDF
        $pdf_filename = generarPDF($id_compromiso, $nombre_menor, $documento_menor, $nombre_responsable, 
                                $documento_responsable, $dato_adicional, $fecha_inicio, 
                                $fecha_vencimiento);
        
        // Actualizar la base de datos con el nombre del archivo
        $sql = "UPDATE compromisos SET archivo_contrato = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$pdf_filename, $id_compromiso]);
        
        $success = "Compromiso creado exitosamente!";
    } catch(PDOException $e) {
        $error = "Error al crear el compromiso: " . $e->getMessage();
    }
}

function generarPDF($id_compromiso, $nombre_menor, $doc_menor, $nombre_resp, $doc_resp, $dato_adicional, $inicio, $vencimiento) {
    require_once('fpdf/fpdf.php');
    
    global $institucion, $representante, $cedula_juridica;
    
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Encabezado
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'COMPROMISO DE PRESTAMO Y USO RESPONSABLE', 0, 1, 'C');
    $pdf->Cell(0, 10, 'DE ACTIVO ELECTRONICO', 0, 1, 'C');
    $pdf->Ln(10);
    
    // Sección 1: Partes del contrato
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Entre:', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, utf8_decode("- Institución Educativa: ").$institucion.utf8_decode(", representada por ").$representante.utf8_decode(", con cédula ").$cedula_juridica.utf8_decode(", denominada en adelante \"EL PRESTADOR\"."));
    $pdf->MultiCell(0, 7, utf8_decode("- Responsable Legal: ").$nombre_resp.utf8_decode(", con cédula ").$doc_resp.utf8_decode(", en representación del menor ").$nombre_menor.utf8_decode(", denominado en adelante \"EL USUARIO\"."));
    $pdf->Ln(10);
    
    // Sección 2: Objeto del contrato
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, '1. OBJETO', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, utf8_decode("\"EL PRESTADOR\" cede en préstamo a \"EL USUARIO\" el siguiente activo electrónico propiedad de la institución, para uso exclusivo en actividades educativas durante el periodo comprendido entre ").$inicio.utf8_decode(" y ").$vencimiento.utf8_decode("."));
    $pdf->Ln(5);
    
    // Detalles del equipo
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Descripción del activo:'), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, utf8_decode($dato_adicional));
    $pdf->Ln(10);
    
    // Sección 3: Obligaciones
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, '2. OBLIGACIONES DEL USUARIO Y RESPONSABLE LEGAL', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    
    $obligaciones = array(
        utf8_decode("Uso adecuado: Utilizar el dispositivo únicamente para fines educativos."),
        utf8_decode("Cuidados básicos: Mantener el equipo en lugar seguro, libre de humedad, polvo o calor extremo."),
        utf8_decode("Seguridad: Notificar inmediatamente a la institución en caso de robo, pérdida o daño."),
        utf8_decode("Devolución: Entregar el equipo al vencimiento del plazo en condiciones aceptables.")
    );
    
    foreach ($obligaciones as $item) {
        $pdf->Cell(10, 7, utf8_decode('•'), 0, 0);
        $pdf->MultiCell(0, 7, $item);
    }
    $pdf->Ln(10);
    
    // Sección 4: Responsabilidades
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, '3. RESPONSABILIDADES DEL PRESTADOR', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, utf8_decode("Proporcionar el equipo en condiciones óptimas y brindar orientación técnica básica sobre su uso correcto."));
    $pdf->Ln(10);
    
    // Sección 5: Sanciones
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, '4. SANCIONES POR INCUMPLIMIENTO', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, utf8_decode("El responsable legal asumirá los costos de reparación o reposición por daños o negligencia. La institución podrá revocar el préstamo por uso indebido."));
    $pdf->Ln(10);
    
    // Firmas
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(95, 10, utf8_decode('POR EL PRESTADOR:'), 0, 0);
    $pdf->Cell(95, 10, utf8_decode('POR EL USUARIO:'), 0, 1);
    $pdf->Ln(20);
    
    $pdf->Cell(95, 10, $representante, 0, 0);
    $pdf->Cell(95, 10, $nombre_resp, 0, 1);
    
    $pdf->Cell(95, 10, utf8_decode('Cédula: ').$cedula_juridica, 0, 0);
    $pdf->Cell(95, 10, utf8_decode('Cédula: ').$doc_resp, 0, 1);
    $pdf->Ln(15);
    
    $pdf->Cell(95, 10, '__________________________', 0, 0);
    $pdf->Cell(95, 10, '__________________________', 0, 1);
    
    $pdf->Cell(95, 10, 'Firma y sello', 0, 0);
    $pdf->Cell(95, 10, 'Firma responsable', 0, 1);
    
    $fecha_actual = date('d/m/Y');
    $pdf->Cell(95, 10, utf8_decode('Fecha: ').$fecha_actual, 0, 0);
    $pdf->Cell(95, 10, utf8_decode('Fecha: ').$fecha_actual, 0, 1);
    
    // Guardar el archivo
    $filename = "contrato_".$id_compromiso.".pdf";
    $pdf->Output("contratos/".$filename, 'F');
    
    return $filename;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Compromiso</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .required:after {
            content: " *";
            color: red;
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
                        <a class="nav-link" href="listar_compromisos.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>   
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4">Crear Nuevo Compromiso</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div class="text-center mt-3">
                <a href="listar_compromisos.php" class="btn btn-primary">Ver todos los compromisos</a>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!isset($success)): ?>
        <form method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nombre_menor" class="form-label required">Nombre del Menor</label>
                    <input type="text" class="form-control" id="nombre_menor" name="nombre_menor" required>
                </div>
                <div class="col-md-6">
                    <label for="documento_menor" class="form-label required">Documento del Menor</label>
                    <input type="text" class="form-control" id="documento_menor" name="documento_menor" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nombre_responsable" class="form-label required">Nombre del Responsable Legal</label>
                    <input type="text" class="form-control" id="nombre_responsable" name="nombre_responsable" required>
                </div>
                <div class="col-md-6">
                    <label for="documento_responsable" class="form-label required">Documento del Responsable</label>
                    <input type="text" class="form-control" id="documento_responsable" name="documento_responsable" required>
                </div>
            </div>
            
            <div class="mb-3">
                <input type="hidden" id="dato_adicional" name="dato_adicional" required>
                <div class="card">
                    <div class="card-header">
                        Datos del Equipo
                    </div>
                    <div class="card-body">
                        <div id="equipo-seleccionado" class="mb-3" style="display:none;">
                            <h6>Equipo seleccionado:</h6>
                            <p id="equipo-info"></p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEquipo">
                            <i class="bi bi-laptop"></i> Seleccionar Equipo
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="fecha_inicio" class="form-label required">Fecha de Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                </div>
                <div class="col-md-6">
                    <label for="fecha_vencimiento" class="form-label required">Fecha de Vencimiento</label>
                    <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary me-md-2">
                    <i class="bi bi-save"></i> Guardar Compromiso
                </button>
                <a href="listar_compromisos.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <!-- Modal para seleccionar equipo -->
    <div class="modal fade" id="modalEquipo" tabindex="-1" aria-labelledby="modalEquipoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEquipoLabel">Seleccionar Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipoEquipo" class="form-label required">Tipo de Equipo</label>
                                <select class="form-select" id="tipoEquipo" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Laptop">Laptop</option>
                                    <option value="Tableta">Tableta</option>
                                    <option value="Computadora de escritorio">Computadora de escritorio</option>
                                    <option value="Proyector">Proyector</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="placa" class="form-label required">Placa institucional</label>
                                <input type="text" class="form-control" id="placa" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="serie" class="form-label required">Número de serie</label>
                                <input type="text" class="form-control" id="serie" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="marca" class="form-label required">Marca</label>
                                <input type="text" class="form-control" id="marca" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="modelo" class="form-label required">Modelo</label>
                                <input type="text" class="form-control" id="modelo" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Accesorios incluidos</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="cargador">
                                    <label class="form-check-label" for="cargador">Cargador/Adaptador</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="estuche">
                                    <label class="form-check-label" for="estuche">Estuche/Funda protectora</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mouse">
                                    <label class="form-check-label" for="mouse">Mouse</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarDatos()">Guardar Datos</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function guardarDatos() {
            // Validar campos obligatorios
            const camposRequeridos = ['tipoEquipo', 'placa', 'serie', 'marca', 'modelo'];
            let valido = true;
            
            camposRequeridos.forEach(id => {
                const campo = document.getElementById(id);
                if (!campo.value) {
                    campo.classList.add('is-invalid');
                    valido = false;
                } else {
                    campo.classList.remove('is-invalid');
                }
            });
            
            if (!valido) {
                alert('Por favor complete todos los campos obligatorios marcados con *');
                return;
            }
            
            // Obtener valores
            const tipoEquipo = document.getElementById('tipoEquipo').value;
            const placa = document.getElementById('placa').value;
            const serie = document.getElementById('serie').value;
            const marca = document.getElementById('marca').value;
            const modelo = document.getElementById('modelo').value;
            const cargador = document.getElementById('cargador').checked;
            const estuche = document.getElementById('estuche').checked;
            const mouse = document.getElementById('mouse').checked;
            const observaciones = document.getElementById('observaciones').value;
            
            // Construir texto descriptivo
            let datos = `Tipo: ${tipoEquipo} | Placa: ${placa} | Serie: ${serie} | Marca: ${marca} | Modelo: ${modelo}`;
            
            // Agregar accesorios
            const accesorios = [];
            if (cargador) accesorios.push('con cargador');
            if (estuche) accesorios.push('con estuche');
            if (mouse) accesorios.push('con mouse');
            if (accesorios.length > 0) {
                datos += ' | ' + accesorios.join(', ');
            }
            
            // Agregar observaciones si existen
            if (observaciones) {
                datos += ` | Observaciones: ${observaciones}`;
            }
            
            // Asignar al campo hidden
            document.getElementById('dato_adicional').value = datos;
            
            // Mostrar resumen en el formulario
            document.getElementById('equipo-info').textContent = datos;
            document.getElementById('equipo-seleccionado').style.display = 'block';
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEquipo'));
            modal.hide();
        }
        
        // Validar fechas al enviar el formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const fechaInicio = new Date(document.getElementById('fecha_inicio').value);
            const fechaFin = new Date(document.getElementById('fecha_vencimiento').value);
            
            if (fechaInicio > fechaFin) {
                alert('La fecha de inicio no puede ser posterior a la fecha de vencimiento');
                e.preventDefault();
            }
            
            if (!document.getElementById('dato_adicional').value) {
                alert('Debe seleccionar un equipo para el préstamo');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>