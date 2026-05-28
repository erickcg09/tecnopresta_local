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
    printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($link));
    exit();
}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcorreo = $_SESSION['correomep'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
date_default_timezone_set('America/Costa_Rica');

// Obtener la fecha actual para límites
$hoy = date('Y-m-d');
$primerDiaMes = date('Y-m-01');
$ultimoDiaMes = date('Y-m-t');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Reporte de Citas - TecnoPresta</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .teams-purple {
            color: #6264A7 !important;
        }
        
        .btn-teams {
            background-color: #6264A7 !important;
            border-color: #6264A7 !important;
            color: white !important;
        }
        
        .btn-teams:hover {
            background-color: #4B4D95 !important;
            border-color: #4B4D95 !important;
            color: white !important;
        }
        
        .card-header-teams {
            background-color: #6264A7 !important;
            color: white !important;
        }
        
        .required-label::after {
            content: " *";
            color: red;
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
                        <a class="nav-link" href="administrar_citas_teams.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>   
                </ul>
                <span class="navbar-text text-light">
                    <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($lognombre); ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header card-header-teams py-3">
                        <h4 class="mb-0">
                            <i class="bi bi-microsoft-teams"></i> Generar Reporte de Citas - Microsoft Teams
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Seleccione el intervalo de fechas para generar un reporte en PDF de todas sus citas programadas en Microsoft Teams.
                            El reporte incluirá citas en estado: programadas, realizadas y canceladas.
                        </p>
                        
                        <form action="generar_reporte_citas_pdf.php" method="POST" target="_blank" onsubmit="desactivarBoton()">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="fecha_inicio" class="form-label required-label">
                                        <i class="bi bi-calendar-plus"></i> Fecha de Inicio
                                    </label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                           value="<?php echo $primerDiaMes; ?>" required
                                           max="<?php echo $hoy; ?>">
                                    <div class="form-text">Seleccione la fecha inicial del reporte</div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="fecha_fin" class="form-label required-label">
                                        <i class="bi bi-calendar-check"></i> Fecha de Fin
                                    </label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                           value="<?php echo $ultimoDiaMes; ?>" required
                                           max="<?php echo $hoy; ?>">
                                    <div class="form-text">Seleccione la fecha final del reporte</div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> <strong>Nota:</strong> 
                                El reporte PDF incluirá todas sus citas dentro del rango seleccionado, 
                                con espacio para firma digital al final del documento.
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="ver_citas_de_soportista.php" class="btn btn-outline-secondary me-md-2">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-teams" id="btnGenerar">
                                    <i class="bi bi-file-earmark-pdf"></i> Generar Reporte PDF
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-footer bg-light">
                        <small class="text-muted">
                            <i class="bi bi-clock-history"></i> Última actualización: <?php echo date('d/m/Y H:i:s'); ?>
                        </small>
                    </div>
                </div>
                
                <!-- Información adicional -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-question-circle"></i> Información sobre el reporte</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-check-circle text-success"></i> 
                                <strong>Citas realizadas:</strong> Se mostrarán en color verde
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-clock text-warning"></i> 
                                <strong>Citas programadas:</strong> Se mostrarán en color azul
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-x-circle text-danger"></i> 
                                <strong>Citas canceladas:</strong> Se mostrarán en color rojo
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-download"></i> 
                                <strong>Formato:</strong> El reporte se descargará automáticamente en formato PDF
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación de fechas
        document.getElementById('fecha_inicio').addEventListener('change', function() {
            var fechaFin = document.getElementById('fecha_fin');
            if (this.value > fechaFin.value) {
                fechaFin.value = this.value;
            }
        });
        
        document.getElementById('fecha_fin').addEventListener('change', function() {
            var fechaInicio = document.getElementById('fecha_inicio');
            if (this.value < fechaInicio.value) {
                fechaInicio.value = this.value;
            }
        });
        
        // Función para desactivar el botón al enviar
        function desactivarBoton() {
            var btn = document.getElementById('btnGenerar');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass"></i> Generando PDF...';
        }
    </script>
</body>
</html>