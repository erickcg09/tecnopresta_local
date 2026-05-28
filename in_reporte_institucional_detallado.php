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
date_default_timezone_set('America/Costa_Rica'); //configuro un nuevo timezone
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];


$logdependencia = $_SESSION['dependencia'];
$logregional = $_SESSION['direccionreg'];
$logcircuito = $_SESSION['circuito'];
$fechaHoraServidor = date('Y-m-d H:i:s');

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


?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Reporte institucional filtrado</title>
 

    <!-- Bootstrap core CSS -->
    <script src="js/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .ui-autocomplete-row
      {
        padding:8px;
        background-color: #f4f4f4;
        border-bottom:1px solid #ccc;
        font-weight:bold;
      }
      .ui-autocomplete-row:hover
      {
        background-color: #ddd;
      }

      .dropbtn {
      background-color: #2b2827;
      color: white;
      padding: 16px;
      font-size: 16px;
      border: none;
      cursor: pointer;
    }

    .dropbtn:hover, .dropbtn:focus {
      background-color: #808b96;
    }

    #myInput {
      box-sizing: border-box;
      background-image: url('matricula_imagenes/searchicon.png');
      background-position: 14px 12px;
      background-repeat: no-repeat;
      font-size: 16px;
      padding: 14px 20px 12px 45px;
      border: none;
      border-bottom: 1px solid #ddd;
    }

    #myInput:focus {outline: 3px solid #ddd;}

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f6f6f6;
      min-width: 230px;
      overflow: auto;
      border: 1px solid #ddd;
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
    }

    .dropdown a:hover {background-color: #ddd;}

    .show {display: block;}
    
    
    </style>

    
    <!-- Custom styles for this template -->
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
            // Incluye el menu izquierdo
            include('menu/menu_izquierdo.php');
?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4"> <!-- Apertura del main donde se coloca la informacion del panel derecho -->
<h2 class = "text-center mt-3">Reporte nacional de inventario filtrado por instituci&oacute;n, clase y fuente presupuestaria</h2><br><br>


<?php
// Obtener variables del formulario
$codigo = htmlspecialchars($_POST['codigo']);
$fondos = intval($_POST['fondos']);
$idags = $_POST['idags'];  

// Convierte el array en una cadena de valores separados por comas
$id_ag_list = implode(',', array_map('intval', $idags));

$id_fondos = $fondos;

// Consulta SQL preparada para contar activos
$sql2 = "SELECT COUNT(t_placa.id_placa) as total_activos
        FROM t_placa
        WHERE t_placa.id_fondos = ?
        AND t_placa.id_activo IN (
            SELECT t_activo.id_activo
            FROM t_activo
            INNER JOIN t_activo_general ON t_activo.id_ag = t_activo_general.id_ag
            WHERE t_activo_general.id_ag IN ($id_ag_list) AND t_placa.codigo = ?
        )";
$stmt2 = $link->prepare($sql2);
$stmt2->bind_param('is', $id_fondos, $codigo);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2) {
    $row = $result2->fetch_assoc();
    echo '<h4 class="text-center">Total de activos: ' . htmlspecialchars($row['total_activos']) . '</h4>';
} else {
    echo "Error en la consulta: " . htmlspecialchars($link->error);
}

// Consulta SQL preparada para obtener detalles de los activos
$sql = "SELECT t_fondos.fondos, t_placa.id_estado, t_placa.id_lugar, COUNT(t_placa.id_placa) as total_registros
        FROM t_placa
        INNER JOIN t_fondos ON t_placa.id_fondos = t_fondos.id_fondos
        INNER JOIN t_activo ON t_placa.id_activo = t_activo.id_activo
        INNER JOIN t_activo_general ON t_activo.id_ag = t_activo_general.id_ag
        WHERE t_activo_general.id_ag IN ($id_ag_list) AND t_placa.codigo = ?
        AND t_placa.id_fondos = ?
        GROUP BY t_placa.id_fondos, t_placa.id_estado, t_placa.id_lugar";
$stmt = $link->prepare($sql);
$stmt->bind_param('si', $codigo, $id_fondos);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<h5>Detalle de los activos</h5>';
    echo '<table class="table table-striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Fondos</th>';
    echo '<th>Estado</th>';
    echo '<th>Ubicación</th>';
    echo '<th>Total de activos</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['fondos']) . '</td>';
        echo '<td>' . htmlspecialchars(getEstado($row['id_estado'])) . '</td>';
        echo '<td>' . htmlspecialchars(getLugar($row['id_lugar'])) . '</td>';
        echo '<td>' . htmlspecialchars($row['total_registros']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>No se encontraron resultados.</p>';
}

// Función para obtener el nombre del estado
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

// Función para obtener el nombre del lugar
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






    </main> <!-- Cierre del main -->
  </div>
</div>

<!-- Seccion de modals -->
<script type="text/javascript">
$(document).ready(function () {
   (function($) {
       $('#FiltrarContenido').keyup(function () {
            var ValorBusqueda = new RegExp($(this).val(), 'i');
            $('.BusquedaRapida tr').hide();
             $('.BusquedaRapida tr').filter(function () {
                return ValorBusqueda.test($(this).text());
              }).show();
                })
      }(jQuery));
});
</script>


    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="js/feather.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/dashboard.js"></script>

  </body>
</html>