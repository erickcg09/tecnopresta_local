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
date_default_timezone_set('America/Costa_Rica'); // Configuraci車n del timezone
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];
$logdependencia = $_SESSION['dependencia'];
$logregional = $_SESSION['direccionreg'];
$logcircuito = $_SESSION['circuito'];
$fechaHoraServidor = date('Y-m-d H:i:s');

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexi車n a MySQL: " . mysqli_connect_error();
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Reporte nacional filtrado</title>

    <!-- Bootstrap core CSS -->
    <script src="js/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
      /* Tu CSS personalizado */
    </style>

    <link href="css/dashboard.css" rel="stylesheet">
  </head>
  <body>
    
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">TecnoPresta</a>
  <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <input class="form-control form-control-dark w-100" type="text" id="FiltrarContenido" placeholder="Buscar" aria-label="Search">
  <div class="navbar-nav">
    <div class="nav-item text-nowrap">
      <a class="nav-link px-3" href="gameover.php">Cerrar sesi&oacute;n</a>
    </div>
  </div>
</header>

<?php
// Incluye el men迆 izquierdo
include('menu/menu_izquierdo.php');
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
<h2 class="text-center mt-3">Reporte nacional de inventario filtrado por clase y fuente presupuestaria</h2><br><br>
<button type="button" class="btn btn-success" onclick="tableToExcel('testTable', 'Resumen')"><i class="bi bi-file-earmark-excel-fill"></i> Excel</button>

<?php
// Verifica si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene el valor del select 'fondos'
    $fondos = $_POST['opciones'] ?? [];
    $idags = $_POST['idags'] ?? [];

    // Validación para evitar consultas vacías
    if (empty($fondos) || empty($idags)) {
        echo '<p>Por favor seleccione al menos una opción para realizar el filtro.</p>';
        exit();
    }

    // Convierte los arrays en cadenas de valores separados por comas
    $id_ag_list = implode(',', $idags);
    $id_fondos = implode(',', $fondos);

    $sql2 = "SELECT COUNT(t_placa.id_placa) as total_activos
             FROM t_placa
             WHERE t_placa.id_fondos IN ($id_fondos)
             AND t_placa.id_activo IN (
                 SELECT t_activo.id_activo
                 FROM t_activo
                 INNER JOIN t_activo_general ON t_activo.id_ag = t_activo_general.id_ag
                 WHERE t_activo_general.id_ag IN ($id_ag_list)
             )";
    $result2 = $link->query($sql2);

    if ($result2) {
        $row = $result2->fetch_assoc();
    } else {
        echo "Error en la consulta: " . $link->error;
    }

    $sql = "SELECT t_fondos.fondos, t_placa.id_estado, t_placa.id_lugar, t_placa.enuso, COUNT(t_placa.id_placa) as total_registros
            FROM t_placa
            INNER JOIN t_fondos ON t_placa.id_fondos = t_fondos.id_fondos
            INNER JOIN t_activo ON t_placa.id_activo = t_activo.id_activo
            INNER JOIN t_activo_general ON t_activo.id_ag = t_activo_general.id_ag
            WHERE t_activo_general.id_ag IN ($id_ag_list)
            AND t_placa.id_fondos IN ($id_fondos)
            GROUP BY t_placa.id_fondos, t_placa.id_estado, t_placa.id_lugar, t_placa.enuso";
    $result = $link->query($sql);

    if ($result && $result->num_rows > 0) {
        $total_activos = 0;
        $activos_por_estado = [];
        while ($row = $result->fetch_assoc()) {
            $total_activos += $row['total_registros'];
            $estado = getEstado($row['id_estado']);
            $lugar = getLugar($row['id_lugar']);
            $enuso = $row['enuso'] == 1 ? 'En uso' : 'No en uso';

            if (!isset($activos_por_estado[$estado])) {
                $activos_por_estado[$estado] = [];
            }
            if (!isset($activos_por_estado[$estado][$lugar])) {
                $activos_por_estado[$estado][$lugar] = ['En uso' => 0, 'No en uso' => 0];
            }
            $activos_por_estado[$estado][$lugar][$enuso] += $row['total_registros'];
        }
    
        echo '<h5>Detalle de los activos</h5>';
        echo '<table class="table table-striped" id="testTable">';
        echo '<thead>';
        echo '<tr><th colspan="5" class="text-center">Total de activos: ' . htmlspecialchars($total_activos) . '</th></tr>';
        echo '<tr><th>Estado</th><th>Ubicaci&oacute;n</th><th>En uso</th><th>No en uso</th><th>Total</th></tr>';
        echo '</thead><tbody>';
        foreach ($activos_por_estado as $estado => $lugares) {
            foreach ($lugares as $lugar => $usos) {
                $total_por_lugar = $usos['En uso'] + $usos['No en uso'];
                echo '<tr>';
                echo '<td>' . htmlspecialchars($estado) . '</td>';
                echo '<td>' . htmlspecialchars($lugar) . '</td>';
                echo '<td>' . htmlspecialchars($usos['En uso']) . '</td>';
                echo '<td>' . htmlspecialchars($usos['No en uso']) . '</td>';
                echo '<td>' . htmlspecialchars($total_por_lugar) . '</td>';
                echo '</tr>';
            }
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No se encontraron resultados.</p>';
    }
}

// Funciones auxiliares
function getEstado($id_estado) {
    $estados = [1 => 'Muy bueno', 2 => 'Bueno', 3 => 'Regular', 4 => 'Malo', 5 => 'Robado'];
    return $estados[$id_estado] ?? 'Desconocido';
}

function getLugar($id_lugar) {
    $lugares = [1 => 'Bodega', 2 => 'Laboratorio', 3 => 'Sala de Robótica', 4 => 'Aulas', 5 => 'Biblioteca', 6 => 'Oficinas Administrativas'];
    return $lugares[$id_lugar] ?? 'Desconocido';
}
?>
</main>
</div>
</div>

<!-- Secci車n de scripts -->
<script type="text/javascript">
$(document).ready(function () {
   $('#FiltrarContenido').keyup(function () {
        var ValorBusqueda = new RegExp($(this).val(), 'i');
        $('.BusquedaRapida tr').hide();
        $('.BusquedaRapida tr').filter(function () {
            return ValorBusqueda.test($(this).text());
        }).show();
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="js/tableToExcel.js"></script>
<script src="js/feather.min.js"></script>
<script src="js/Chart.min.js"></script>
<script src="js/dashboard.js"></script>

</body>
</html>