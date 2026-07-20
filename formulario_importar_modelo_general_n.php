<?php
if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}
/*
require_once __DIR__ . '/usuarioAzure.php';
$usuario_azure = obtenerUsuarioSesion();

if (!$usuario_azure) {
    header("Location: index.html");
    exit();
}*/
// ==== CONSTRUIR RUTA DE REGRESO =====
$ruta_regreso ='navegar.php?ruta=formulario_corregir_modelo_n.php';

/*$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logtipo = $_SESSION['tipo'];
$logcodigo = $_SESSION['codigo'];
*/
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corregir modelo del activo</title>
    <link href="bootstrap5/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    <!-- ESTILOS INSTITUCIONALES -->
    <link rel="stylesheet" href="assets/css/nueva-identidad.css">
    <link rel="stylesheet" href="css/formulario_menu_principal.css"/>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">

    <script src="js/jquery-3.7.1.min.js"></script>
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
<body class="layout-page">
  <?php include 'partials/header.php'; ?>
    <main class="flex-grow-1">
    

        <div class="container mt-5">
            <!-- <h2>Usuario: <?php //echo $lognombre." ".$logcodigo;?></h2><br> -->
            <h4>Formulario importar modelo general para activos dotados por fondos p&uacute;blicos.</h4>

            <form action="importar_a_tabla_modelo_general_n.php" method="POST">
                <!-- Input para el ID del activo -->
                <div class="mb-3">
                    <label for="id_activo" class="form-label">ID del Activo</label>
                    <input 
                        type="text" 
                        class="form-control w-50" 
                        id="id_activo" 
                        name="id_activo" 
                        placeholder="Ingrese el ID del activo..." 
                        required 
                        onchange="buscarActivo()"
                    >
                </div>

                <!-- Input para mostrar la descripción -->
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción del Activo</label>
                    <input 
                        type="text" 
                        class="form-control w-50" 
                        id="descripcion" 
                        name="descripcion" 
                        placeholder="Descripción del activo..." 
                        readonly
                    >
                </div>

                <!-- Select para los fondos presupuestarios -->
                <div class="mb-3">
                    <label for="fondos" class="form-label">Fondo Presupuestario</label>
                    <select 
                        class="form-select my-3 w-50" 
                        id="fondos" 
                        name="fondos" 
                        aria-label="Seleccione el fondo presupuestario" 
                        required
                    >
                        <option value="0">Seleccione el fondo presupuestario ...</option>
                        <?php 
                            $querz = $link->query("SELECT * FROM t_fondos");
                            while ($valorez = mysqli_fetch_array($querz)) {
                                echo '<option value="'.$valorez['id_fondos'].'">'.$valorez['fondos'].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="bi bi-cloud-arrow-up"></i> Importar Modelo
                </button>
            </form>

        
            <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-card-checklist" viewBox="0 0 16 16">
                <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
                <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0M7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0"/>
                </svg>
            </div>
    </div>


    <!-- <footer class="bg-dark text-white pt-4 pb-4">
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
    </footer> -->
        <!-- Botón flotante Volver -->
        <a href="<?= htmlspecialchars($ruta_regreso) ?>" class="btn-disponibilidad" 
            style="bottom: 100px;" data-tooltip="Regresar">
                <i class="bi bi-arrow-left-circle-fill"></i>
        </a>
    </main>
    <?php include 'partials/footer.php'; ?>
    <script>
    function buscarActivo() {
        const idActivo = document.getElementById("id_activo").value;

        if (idActivo.trim() !== "") {
            fetch("buscar_activo_n.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `id_activo=${idActivo}`,
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    document.getElementById("descripcion").value = `${data.modelo} ${data.clase} ${data.marca} ${data.color}`;
                } else {
                    alert("No se encontró el activo especificado.");
                    document.getElementById("descripcion").value = "";
                }
            })
            .catch((error) => {
                console.error("Error al buscar el activo:", error);
                alert("Ocurrió un error al buscar el activo.");
            });
        }
    }
    </script>
        <script src="bootstrap5/js/bootstrap.bundle.min.js"></script>

</body>
</html>