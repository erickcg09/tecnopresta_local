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
    die("Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link));
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
    <title>Panel de Herramientas</title>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                        <a class="nav-link" href="menu_inventario_nacional.php">
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

            <!-- Tarjeta: Permisos -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-person-fill-gear text-secondary display-4"></i>
                        <h5 class="card-title mt-2">Permisos</h5>
                        <p class="card-text">Consulta y establece permisos a usuarios.</p>
                        <a href="formulario_administracion_permisos.php" class="btn btn-secondary">Ingresar</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Localizar -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-geo-fill text-secondary display-4"></i>
                        <h5 class="card-title mt-2">Localizar Activo</h5>
                        <p class="card-text">Buscar un activo por placa /o serial.</p>
                        <a href="formulario_buscar_activo_cruzada.php" class="btn btn-secondary">Ingresar</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Redistribución -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-arrow-left-right text-secondary display-4"></i>
                        <h5 class="card-title mt-2">Redistribuir Activos</h5>
                        <p class="card-text">Pasar activos de un centro a otro.</p>
                        <a href="in_formulario_redistribuir.php" class="btn btn-secondary">Ingresar</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Verificar y Eliminar -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-database-fill-dash text-secondary display-4"></i>
                        <h5 class="card-title mt-2">Consultar y Eliminar</h5>
                        <p class="card-text">Consulta activos de centros.</p>
                        <a href="formulario_herramientas_con_tablas.php" class="btn btn-secondary">Ingresar</a>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta: Eliminar Activos de los Centros -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-trash-fill text-secondary display-4"></i>
                        <h5 class="card-title mt-2">Eliminaci&oacute;n Masiva</h5>
                        <p class="card-text">Eliminar activos de los centros educativos.</p>
                        <a href="herramienta_formulario_eliminar_activos.php" class="btn btn-secondary">Ingresar</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Editar Activos de los Centros -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-eraser-fill text-secondary display-4"></i>
                        <h5 class="card-title mt-2">Editar Tipo y Origen</h5>
                        <p class="card-text">Editar activos educativos su tipo y fondos.</p>
                        <a href="herramienta_formulario_editar_activos.php" class="btn btn-secondary">Ingresar</a>
                    </div>
                </div>
            </div>


            <!-- Tarjeta: Editar Placas y Series -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-upc-scan text-secondary display-4"></i>
                        <h5 class="card-title mt-2">Editar Placa y Series</h5>
                        <p class="card-text">Editar activos educativos placa y serial.</p>
                        <a href="herramienta_editar_masiva.php" class="btn btn-secondary">Ingresar</a>
                    </div>
                </div>
            </div>


            <!-- Tarjeta: Gestionar Centros Educativos -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-building-fill-gear text-secondary display-4"></i>
                        <h5 class="card-title mt-2">Gestionar Centros Educativos</h5>
                        <p class="card-text">Agregar, editar y eliminar .</p>
                        <a href="herramienta_gestionar_centro_educativo.php" class="btn btn-secondary">Ingresar</a>
                    </div>
                </div>
            </div>


            <!-- Tarjeta: Editar Ubicacion de Activos -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-geo-alt-fill text-secondary display-4"></i>
                        <h5 class="card-title mt-2">Editar Ubicaci&oacute;n</h5>
                        <p class="card-text">Permite modificar la ubicaci&oacute;n .</p>
                        <a href="herramienta_editar_ubicacion_activo.php" class="btn btn-secondary">Ingresar</a>
                    </div>
                </div>
            </div>


            <!-- Tarjeta: Generar Certificacion de Centro -->
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-emoji-sunglasses text-secondary display-4"></i>
                        <h5 class="card-title mt-2">Generar Certificaci&oacute;n</h5>
                        <p class="card-text">Permite generar certificaci&oacute;n a un centro .</p>
                        <a href="herramienta_generar_certificacion.php" class="btn btn-secondary">Ingresar</a>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

</body>
</html>