<?php
session_start();

// Verifica si el usuario tiene permisos
$tienellave = in_array($_SESSION['tipo'], [1, 7]);
if (!$tienellave) {
    echo '<script language="javascript">
    alert("No tienes permisos, por favor contacte a su administrador");
    window.location.href = "formulario_menu_principal.html";
    </script>';
    exit();
}

// Configuración de zona horaria y variables de sesión
date_default_timezone_set('America/Costa_Rica');
$logusuario = $_SESSION['cedula'];
$lognombre = $_SESSION['nombre'];
$logcodigo = $_SESSION['codigo'];
$logdependencia = $_SESSION['dependencia'];
$logregional = $_SESSION['direccionreg'];
$logcircuito = $_SESSION['circuito'];
$fechaHoraServidor = date('Y-m-d H:i:s');

// Conexión a la base de datos
require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexión a MySQL: " . mysqli_connect_error();
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
    <title>Reporte nacional filtrado</title>
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
    <input class="form-control form-control-dark w-100" type="text" id="FiltrarContenido" placeholder="Buscar" aria-label="Search">
    <div class="navbar-nav">
        <a class="nav-link px-3" href="gameover.php">Cerrar sesión</a>
    </div>
</header>

<?php include('menu/menu_izquierdo.php'); ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <h2 class="text-center mt-3">Reporte nacional de inventario filtrado por clase y fuente presupuestaria</h2>
    <form action="in_generar_reporte_nacional_filtrado.php" method="post">
        <!-- Botón de enviar -->
        <div class="d-grid gap-2">
            <button class="btn btn-primary" type="submit">Consultar</button>
        </div>

        <!-- Checkboxes para fuentes presupuestarias -->
        <?php
        $fuentes = [
            1 => "MEP",
            2 => "FONATEL PROGRAMA 3 SUTEL",
            3 => "JUNTA ADMINISTRATIVA (RECURSOS PROPIOS)",
            4 => "PNTM TECNOAPRENDER",
            5 => "BEYCRA",
            6 => "JUNTA DE EDUCACIÓN (RECURSOS PROPIOS)",
            7 => "PRONIE-MEP-FOD",
            8 => "DONACIONES DE OTROS",
            9 => "LEY 7372"
        ];
        foreach ($fuentes as $valor => $etiqueta) {
            echo "<div class='form-check'>
                    <input class='form-check-input' type='checkbox' value='$valor' id='fuente_$valor' name='opciones[]'>
                    <label class='form-check-label' for='fuente_$valor'>$etiqueta</label>
                  </div>";
        }
        ?>

        <!-- Tabla con clases disponibles -->
        <p>Por favor seleccione la clase a filtrar, pueden ser varias:</p>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Seleccione</th>
                    <th>Clase</th>
                </tr>
            </thead>
            <tbody class="BusquedaRapida">
                <?php
                $consulta = mysqli_query($link, "SELECT id_ag, clase FROM t_activo_general ORDER BY clase ASC");
                while ($activos = mysqli_fetch_array($consulta)) {
                    echo "<tr>
                            <td><input type='checkbox' name='idags[]' value='{$activos['id_ag']}'/></td>
                            <td>{$activos['clase']}</td>
                          </tr>";
                }
                mysqli_close($link);
                ?>
            </tbody>
        </table>
    </form>
</main>
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

    <script src="js/feather.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>