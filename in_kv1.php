<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su administrador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katherine v1.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .splash-screen {
            position: fixed;
            width: 100%;
            height: 100%;
            background-color: #18191a;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        h1 {
            position: relative;
            z-index: 1;
            color: #d4af37; /* Color dorado champán */
            animation: fadeIn 3s ease-in-out;
        }
        h4 {
            position: relative;
            z-index: 1;
            color: rgb(232, 232, 238);
            animation: fadeIn 3s ease-in-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="splash-screen">
        <div class="text-center">
            <h1 class="display-5">Inventario Nacional</h1>
            <h4>Versi&oacute;n 1.0</h4>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                window.location.href = 'in_panel_reporte_ministra.php'; // URL de la página a la que deseas redirigir
            }, 3000); // Duración de la pantalla de inicio en milisegundos (3 segundos)
        });
    </script>
</body>
</html>
