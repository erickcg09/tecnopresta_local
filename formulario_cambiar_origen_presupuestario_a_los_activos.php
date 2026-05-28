<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 2, 3, 4, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su director(a) institucional para que se los brinde, si es usted prestador o inventariador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corregir origen presupuestario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
            .card-custom {
                background-color: #f0f4f8; /* Color frío y suave */
                border: none;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }
            .card-body {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .card-img {
                width: 50px;
                height: 50px;
                object-fit: cover;
                border-radius: 50%;
                align-self: flex-end;
            }
            .btn-custom {
                background-color: #007bff; /* Color frío */
                border: none;
                border-radius: 5px;
            }
            .icon-large {
            font-size: 2em; /* Ajusta el tamaño del ícono */
            }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md bg-dark navbar-dark">
        <img src="img/logodelgobierno.png" width="35" height="30" alt="" loading="lazy">
        <a class="navbar-brand" href="formulario_menu_principal.html">Tecnopresta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="inventario_mantenimiento.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply-all" viewBox="0 0 16 16">
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

<div class="container mt-5">
    <h2>Usuario: <?php echo $lognombre . " " . $logcodigo; ?></h2><br>
    <h4>Formulario para cambiar el origen presupuestario de los activos.</h4>
    <div class="mb-3">
        <!-- Select para elegir el origen presupuestario -->
        <select class="form-select my-3 w-50" id="origenPresupuestario" name="origenPresupuestario" required>
            <option value="0">Seleccione un origen presupuestario...</option>
            <?php
            // Consulta para obtener los orígenes presupuestarios
            $queryOrigen = $link->query("SELECT * FROM t_fondos");
            while ($origen = mysqli_fetch_array($queryOrigen)) {
                echo '<option value="' . $origen['id_fondos'] . '">' . $origen['fondos'] . '</option>';
            }
            ?>
        </select>
    </div>
    <form action="actualizar_origen_presupuestario.php" method="POST">
        <!-- Select para elegir el nuevo fondo presupuestario -->
        <div id="fondosPresupuestarios">
            <!-- Aquí se cargarán los fondos presupuestarios excluyendo el seleccionado -->
        </div>
        <!-- Tabla para mostrar los activos -->
        <div id="mostrarActivos">
            <!-- Aquí se cargarán los activos asociados al origen seleccionado -->
        </div>
        <!-- Botón para enviar el formulario 
        <button type="submit" class="btn btn-primary my-3">Actualizar Origen Presupuestario</button> -->
    </form>
</div>


<footer class="bg-dark text-white pt-4 pb-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0">Por favor, asegúrese de ingresar la información solicitada en cada instancia.</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <div class="border border-light p-3">
                    <p class="mb-0">© 2024 Ministerio de Educación Pública. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
    $(document).ready(function () {
        // Evento cuando se selecciona un origen presupuestario
        $('#origenPresupuestario').change(function () {
            var idOrigen = $(this).val();

            if (idOrigen != 0) {
                // Cargar los fondos presupuestarios excluyendo el seleccionado
                $.ajax({
                    url: 'cargar_fondos_presupuestarios.php',
                    type: 'POST',
                    data: { id_fondos: idOrigen },
                    success: function (response) {
                        $('#fondosPresupuestarios').html(response);
                    }
                });

                // Cargar los activos asociados al origen seleccionado
                $.ajax({
                    url: 'cargar_activos_por_origen.php',
                    type: 'POST',
                    data: { id_fondos: idOrigen },
                    success: function (response) {
                        $('#mostrarActivos').html(response);
                    }
                });
            }
        });
    });
</script>
<script>
// Espera a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", function () {
    // Delegación de eventos para el checkbox principal
    document.addEventListener("change", function (event) {
        if (event.target && event.target.id === "selectAll") {
            const checkboxes = document.querySelectorAll(".selectall");
            checkboxes.forEach(checkbox => {
                checkbox.checked = event.target.checked;
            });
        }
    });

    // Delegación de eventos para cargar datos dinámicamente
    document.querySelector("#cargarTabla").addEventListener("click", function () {
        // Simulación de llamada AJAX
        fetch("ruta_al_archivo_php_que_devuelve_la_tabla.php")
            .then(response => response.text())
            .then(html => {
                // Insertar la tabla cargada dinámicamente en el contenedor
                document.querySelector("#tablaContenedor").innerHTML = html;
            })
            .catch(error => console.error("Error al cargar los datos:", error));
    });
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


</body>
</html>