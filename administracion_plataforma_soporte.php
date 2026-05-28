<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false){
echo '<script language = javascript>
alert("No tienes permisos")
self.location = "index.html"
</script>';
}

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno())

{

echo "Error de conexion a mysql: " . mysqli_connect_error();

}

if (!mysqli_set_charset($link, "utf8")) {
    	echo "Error cargando el conjunto de caracteres utf8";
} else {

}


$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
$loginstitucion = $_SESSION['dependencia'];
$logcorreo = $_SESSION['correomep'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Color morado de Microsoft Teams */
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
        

    </style>
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
                        <a class="nav-link" href="panel_soporte.php">
                            <i class="bi bi-arrow-left-circle"></i> Regresar
                        </a>
                    </li>   
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenedor principal -->
    <div class="container mt-5">
        <div class="row g-4">

            <!-- Tarjeta: Reportes -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-bar-chart-line-fill text-primary display-4"></i>
                        <h5 class="card-title mt-2">Reportes</h5>
                        <p class="card-text">Consulta estadísticas y reportes de uso.</p>
                        <a href="formulario_reporte_centro_de_consultas.html" class="btn btn-primary">Ver Reportes</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Agregar Ingenieros -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-person-plus-fill text-success display-4"></i>
                        <h5 class="card-title mt-2">Agregar Ingeniero</h5>
                        <p class="card-text">Añade un nuevo ingeniero al sistema.</p>
                        <a href="formulario_agregar_analistas.php" class="btn btn-success">Agregar</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Modificar Ingenieros -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-pencil-square text-warning display-4"></i>
                        <h5 class="card-title mt-2">Modificar Ingeniero</h5>
                        <p class="card-text">Edita la información de los ingenieros.</p>
                        <a href="interfaz_principal_listado_ingenieros.php" class="btn btn-warning">Modificar</a>
                    </div>
                </div>
            </div>

            <!-- Nueva Tarjeta: Control de Citas -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm card-teams">
                    <div class="card-body">
                        <i class="bi bi-microsoft-teams teams-purple display-4"></i>
                        <h5 class="card-title mt-2">Control de Citas</h5>
                        <p class="card-text">Gestiona y administra todas las citas programadas.</p>
                        <a href="ver_citas_de_soportista.php" class="btn btn-teams">Administrar Citas</a>
                    </div>
                </div>
            </div>

            <!-- Nueva Tarjeta: Indicadores Técnicos del Servicio -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-app-indicator text-dark display-4"></i>
                        <h5 class="card-title mt-2">Atención de Soporte Técnico</h5>
                        <p class="card-text">Gestión de Asignaciones de Visita por Estado</p>
                        <a href="formulario_panel_visitas_sitio.html" class="btn btn-dark">Ingresar</a>
                    </div>
                </div>
            </div>   
            
            <!-- Nueva Tarjeta: Eviar Ingenieros/Tecnicos a un Centro -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-life-preserver text-primary display-4"></i>
                        <h5 class="card-title mt-2">Enviar Profesionales en Soporte</h5>
                        <p class="card-text">Gestiona la asignación y envio de soportistas a un centro.</p>
                        <a href="verificacion_adicional.php" class="btn btn-primary">Ingresar</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

</body>
</html>