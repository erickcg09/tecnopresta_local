<?php
session_start();
$tienellave = ($_SESSION['tipo']==1 or $_SESSION['tipo']==2 or $_SESSION['tipo']==3 or $_SESSION['tipo']==4);
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

// Recoger los datos del formulario
$id_fondos = $_POST["id_fondos"];
$codigo = $_POST["codigo"];
$id_lugar = $_POST["id_lugar"];

$sinlugar = "0";

// Preparar la primera consulta SQL
$sql = "SELECT id_placa, placa, serial
        FROM t_placa
        WHERE codigo = ? AND id_fondos = ? AND id_lugar = ?
        ORDER BY placa";

// Preparar la declaración
$stmt = mysqli_prepare($link, $sql);

if ($stmt) {
    // Vincular los parámetros
    mysqli_stmt_bind_param($stmt, "iii", $codigo, $id_fondos, $sinlugar);

    // Ejecutar la declaración
    mysqli_stmt_execute($stmt);

    // Obtener el resultado
    $resultado = mysqli_stmt_get_result($stmt);

    // Procesar los resultados
    echo '
    <div class="row">
    <div class="col-md-5">
    <p><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-radar" viewBox="0 0 16 16">
    <path d="M6.634 1.135A7 7 0 0 1 15 8a.5.5 0 0 1-1 0 6 6 0 1 0-6.5 5.98v-1.005A5 5 0 1 1 13 8a.5.5 0 0 1-1 0 4 4 0 1 0-4.5 3.969v-1.011A2.999 2.999 0 1 1 11 8a.5.5 0 0 1-1 0 2 2 0 1 0-2.5 1.936v-1.07a1 1 0 1 1 1 0V15.5a.5.5 0 0 1-1 0v-.518a7 7 0 0 1-.866-13.847"/>
  </svg> Activos sin ubicar</p>
    <form>
    <table class="table table-bordered" id="tabla-izquierda">
        <thead>
            <tr>
                <th>Ident</th>
                <th>Placa y Serie</th>
            </tr>
        </thead>
        <tbody>';

    while ($fila = mysqli_fetch_array($resultado)) {
        $nombrecompleto = $fila['placa']." ".$fila['serial'];
        echo '<tr data-id="'. $fila['id_placa'] .'">
                <td>'. $fila['id_placa'] .'</td>
                <td>'. $nombrecompleto .'</td>
              </tr>';
    }

    echo ' <!-- Fin del ciclo que agrega los registros-->
        </tbody>
    </table>
    </div>
    <div class="col-md-1"><h5 class="my-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-right" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5m14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5"/>
    </svg>
</h5></div>';

    // Cerrar la declaración
    mysqli_stmt_close($stmt);
} else {
    echo "Error en la preparación de la consulta: " . mysqli_error($link);
}

// Preparar la segunda consulta SQL
$sql2 = "SELECT id_placa, placa, serial
        FROM t_placa
        WHERE codigo = ? AND id_fondos = ? AND id_lugar = ?
        ORDER BY placa";

// Preparar la declaración
$stmt2 = mysqli_prepare($link, $sql2);

if ($stmt2) {
    // Vincular los parámetros
    mysqli_stmt_bind_param($stmt2, "iii", $codigo, $id_fondos, $id_lugar);

    // Ejecutar la declaración
    mysqli_stmt_execute($stmt2);

    // Obtener el resultado
    $resultado2 = mysqli_stmt_get_result($stmt2);

    // Procesar los resultados
    echo '
    <div class="col-md-5">
    <p><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
    <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/>
    <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
  </svg> Activos ubicados lugar seleccionado</p>
    <table class="table table-bordered" id="tabla-derecha">
        <thead>
            <tr>
                <th>Ident</th>
                <th>Placa y Serie</th>
            </tr>
        </thead>
        <tbody>';

    while ($fila2 = mysqli_fetch_array($resultado2)) {
        $nombrecompleto2 = $fila2['placa']." ".$fila2['serial'];
        echo '<tr data-id="'. $fila2['id_placa'] .'">
        <td>'. $fila2['id_placa'] .'</td>
        <td>'. $nombrecompleto2 .'</td>
      </tr>';
    }

    echo ' <!-- Fin del ciclo que agrega los registros-->
        </tbody>
    </table>
    </form>
    </div>
    </div>';

    // Cerrar la declaración
    mysqli_stmt_close($stmt2);
} else {
    echo "Error en la preparación de la consulta: " . mysqli_error($link);
}

// Cerrar la conexión a la base de datos
mysqli_close($link);
?>