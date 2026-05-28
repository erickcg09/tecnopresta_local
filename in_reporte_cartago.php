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
date_default_timezone_set('America/Costa_Rica');
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
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    echo "Error cargando el conjunto de caracteres utf8";
}

// DEFINIR LOS ARRAYS ANTES DE CUALQUER USO
$id_ag_list = [2, 4, 179, 190, 286, 337, 338, 523, 318];
$id_fondos = [2];
$codigos_placa = [
        1724, 1725, 1726, 1727, 1728, 1729, 1731, 1738, 1739, 
        1743, 1751, 1752, 1753, 1754, 1755, 1759, 1760, 1763, 
        1770, 1771, 1773, 1774, 1776, 1777, 1778, 1779, 1780, 
        1781, 1782, 1783, 1786, 1787, 1788, 1790, 1797, 1800, 
        1801, 1803, 1804, 1805, 1806, 1807, 1808, 1809, 1817, 
        1824, 1827, 1828, 1829, 1831, 1835, 1836, 1837, 1838, 
        1839, 1843, 1847, 1848, 1853, 1854, 1855, 1858, 1866, 
        1869, 1871, 1876, 1880, 1884, 1885, 1890, 1891, 1894, 
        1899, 1900, 1908, 1909, 1910, 1911, 1912, 1913, 1916, 
        1917, 1919, 1921, 1926, 1928, 1929, 1930, 1931, 1932, 
        1933, 4048, 4049, 4050, 4051, 4052, 4053, 4056, 4058, 
        4059, 4060, 4061, 4064, 4065, 4067, 4185, 4535, 4536, 
        4853, 4855, 4856, 4858, 4965, 4967, 5082, 5830, 5834, 
        5979, 5987, 6032, 6137, 6152, 6216, 6372, 6384, 6503, 
        6533, 6581, 6742];

// Verificar que los arrays estén definidos y no estén vacíos
if (!isset($codigos_placa) || !is_array($codigos_placa)) {
    $codigos_placa = []; // Asignar array vacío si no está definido
}

$total_codigos = count($codigos_placa);
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Reporte nacional filtrado</title>

    <script src="js/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
      .table-responsive { overflow-x: auto; }
      .busqueda-rapida { margin-bottom: 20px; }
      .total-activos { font-weight: bold; background-color: #f8f9fa; }
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
      <a class="nav-link px-3" href="gameover.php">Cerrar sesión</a>
    </div>
  </div>
</header>

<?php include('menu/menu_izquierdo.php'); ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
  <h2 class="text-center mt-3">Reporte nacional de inventario filtrado</h2>
  <div class="alert alert-info">
    <strong>Filtros aplicados:</strong> Códigos de placa específicos (<?php echo count($codigos_placa); ?>)
  </div>
  
  <button type="button" class="btn btn-success mb-3" onclick="tableToExcel('testTable', 'Resumen')">
    <i class="bi bi-file-earmark-excel-fill"></i> Exportar a Excel
  </button>

<?php

// Convertir arrays a strings para la consulta SQL
$id_ag_list_str = implode(',', $id_ag_list);
$id_fondos_str = implode(',', $id_fondos);
$codigos_placa_str = implode(',', $codigos_placa);

// Consulta para el total de activos
$sql_count = "SELECT COUNT(t_placa.id_placa) as total_activos
              FROM t_placa
              WHERE t_placa.id_fondos IN ($id_fondos_str)
              AND t_placa.codigo IN ($codigos_placa_str)
              AND t_placa.id_activo IN (
                  SELECT t_activo.id_activo
                  FROM t_activo
                  INNER JOIN t_activo_general ON t_activo.id_ag = t_activo_general.id_ag
                  WHERE t_activo_general.id_ag IN ($id_ag_list_str)
              )";

$result_count = $link->query($sql_count);
$total_activos = 0;

if ($result_count) {
    $row = $result_count->fetch_assoc();
    $total_activos = $row['total_activos'] ?? 0;
} else {
    echo '<div class="alert alert-danger">Error al contar los activos: ' . $link->error . '</div>';
}

// Consulta principal para los detalles
$sql = "SELECT t_fondos.fondos, t_placa.id_estado, t_placa.id_lugar, t_placa.enuso, 
               COUNT(t_placa.id_placa) as total_registros
        FROM t_placa
        INNER JOIN t_fondos ON t_placa.id_fondos = t_fondos.id_fondos
        INNER JOIN t_activo ON t_placa.id_activo = t_activo.id_activo
        INNER JOIN t_activo_general ON t_activo.id_ag = t_activo_general.id_ag
        WHERE t_activo_general.id_ag IN ($id_ag_list_str)
        AND t_placa.id_fondos IN ($id_fondos_str)
        AND t_placa.codigo IN ($codigos_placa_str)
        GROUP BY t_placa.id_fondos, t_placa.id_estado, t_placa.id_lugar, t_placa.enuso
        ORDER BY t_placa.id_estado, t_placa.id_lugar";

$result = $link->query($sql);

if ($result && $result->num_rows > 0) {
    $activos_por_estado = [];
    
    while ($row = $result->fetch_assoc()) {
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

    echo '<div class="table-responsive">
            <table class="table table-striped table-hover" id="testTable">
              <thead class="table-dark">
                <tr>
                  <th colspan="5" class="text-center">Total de activos: ' . $total_activos . '</th>
                </tr>
                <tr>
                  <th>Estado</th>
                  <th>Ubicación</th>
                  <th>En uso</th>
                  <th>No en uso</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody class="busqueda-rapida">';

    foreach ($activos_por_estado as $estado => $lugares) {
        $first_row = true;
        
        foreach ($lugares as $lugar => $usos) {
            $total_por_lugar = $usos['En uso'] + $usos['No en uso'];
            
            echo '<tr>';
            echo $first_row ? '<td rowspan="' . count($lugares) . '">' . htmlspecialchars($estado) . '</td>' : '';
            echo '<td>' . htmlspecialchars($lugar) . '</td>';
            echo '<td>' . $usos['En uso'] . '</td>';
            echo '<td>' . $usos['No en uso'] . '</td>';
            echo '<td class="total-activos">' . $total_por_lugar . '</td>';
            echo '</tr>';
            
            $first_row = false;
        }
    }

    echo '</tbody></table></div>';
} else {
    echo '<div class="alert alert-warning">No se encontraron resultados con los filtros aplicados.</div>';
}

// Funciones auxiliares
function getEstado($id_estado) {
    $estados = [
        1 => 'Muy bueno', 
        2 => 'Bueno', 
        3 => 'Regular', 
        4 => 'Malo', 
        5 => 'Robado'
    ];
    return $estados[$id_estado] ?? 'Desconocido';
}

function getLugar($id_lugar) {
    $lugares = [
        1 => 'Bodega', 
        2 => 'Laboratorio', 
        3 => 'Sala de Robótica', 
        4 => 'Aulas', 
        5 => 'Biblioteca', 
        6 => 'Oficinas Administrativas'
    ];
    return $lugares[$id_lugar] ?? 'Desconocido';
}
?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/tableToExcel.js"></script>
<script src="js/feather.min.js"></script>
<script src="js/Chart.min.js"></script>
<script src="js/dashboard.js"></script>

<script>
$(document).ready(function() {
    $('#FiltrarContenido').keyup(function() {
        const searchText = $(this).val().toLowerCase();
        $('.busqueda-rapida tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.indexOf(searchText) > -1);
        });
    });
    
    feather.replace();
});
</script>

</body>
</html>