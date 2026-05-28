<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false){
    echo '<script language = javascript>
    alert("No tienes permisos")
    self.location = "formulario_menu_inventario.html"
    </script>';
}
header('Content-Type: text/html; charset=utf-8'); // Cabecera HTTP para UTF-8
require_once("conexion.php");

$link = $mysqli;

if (mysqli_connect_errno()) {
    echo "Error de conexi車n a MySQL: " . mysqli_connect_error();
    exit();
}

// Configurar el conjunto de caracteres a UTF-8
if (!mysqli_set_charset($link, "utf8")) {
    die("Error cargando el conjunto de caracteres UTF-8: " . mysqli_error($link));
}

// Funci車n para convertir valoraci車n num谷rica en estrellas (usando Bootstrap Icons)
function convertirAEstrellas($valor) {
    $estrellas = "";
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $valor) {
            $estrellas .= "<i class='bi bi-star-fill text-warning'></i>"; // Estrella llena
        } else {
            $estrellas .= "<i class='bi bi-star text-secondary'></i>"; // Estrella vac赤a
        }
    }
    return $estrellas;
}
// $idcaso = $_GET['idcaso'];
?>

<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <title>Afinidad del ingeniero</title>
  </head>
  <body>
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
                        <a class="nav-link" href="formulario_seleccionar_analista.php">
                            <i class="bi bi-person-vcard"></i> Regresar
                        </a>
                    </li> 
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <h2 class="text-center my-4">Afinidad de los ingenieros</h2>
<?php
    // Consulta actualizada para incluir afinidad y kil車metros
    $query = "SELECT id_analista, nombre, foto, mantenimiento, redes, configuracion, kilometros 
              FROM t_analista 
             ";
    if ($stmt = mysqli_prepare($link, $query)) {
       // mysqli_stmt_bind_param($stmt, "i", $id_regional);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo "<table class='table table-bordered'>";
            echo "<thead><tr>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Mantenimiento</th>
                    <th>Redes</th>
                    <th>Configuraci&oacute;n</th>
                    <th>Km</th>
                  </tr></thead>";
            echo "<tbody>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td><img src='" . $row['foto'] . "' alt='Foto de " . $row['nombre'] . "' width='50'></td>";
                echo "<td>" . $row['nombre'] . "</td>";
                echo "<td>" . convertirAEstrellas($row['mantenimiento']) . "</td>"; // Estrellas para Mantenimiento
                echo "<td>" . convertirAEstrellas($row['redes']) . "</td>"; // Estrellas para Redes
                echo "<td>" . convertirAEstrellas($row['configuracion']) . "</td>"; // Estrellas para Configuraci車n
                echo "<td>" . $row['kilometros'] . " km</td>"; // Kil車metros
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No se encontraron analistas para esta regi&oacute;n.</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error en la consulta: " . mysqli_error($link);
    }


mysqli_close($link);
?>
    </div>
  </body>
</html>