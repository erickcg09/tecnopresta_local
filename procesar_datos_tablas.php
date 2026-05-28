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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['activosIzquierda']) && isset($data['activosDerecha'])) {
        $activosIzquierda = $data['activosIzquierda'];
        $activosDerecha = $data['activosDerecha'];

        // Verifica los valores de los input
        if (isset($data['id_fondos']) && isset($data['codigo']) && isset($data['id_lugar'])) {
            $id_fondos = $data['id_fondos'];
            $codigo = $data['codigo'];
            $id_lugar = $data['id_lugar'];



            // Construye la consulta SQL para actualizar los registros con la ubicacion actual
            $sql = "UPDATE t_placa SET id_lugar = ? WHERE id_placa = ? AND codigo = ? AND id_fondos = ?";

            // Prepara la consulta
            $stmt = $link->prepare($sql);

            // Verifica si la preparación de la consulta fue exitosa
            if ($stmt === false) {
                die("Error al preparar la consulta: " . $link->error);
            }

            // Itera sobre los activos de la tabla derecha y actualiza los registros con la ubicacion actual
            foreach ($activosDerecha as $activo) {
                $idplaca = $activo['idplaca'];
                // Actualiza el registro con el valor de la ubicacion
                $stmt->bind_param("iiii", $id_lugar, $idplaca, $codigo, $id_fondos);
                if ($stmt->execute() === false) {
                    die("Error al ejecutar la consulta: " . $stmt->error);
                }
            }

            // Cierra la conexión a la base de datos
            $stmt->close();

            // Después de actualizar con la ubicacion actual, ahora actualiza con 0
            // Construye la consulta SQL para actualizar los registros con el valor 0
            $sql = "UPDATE t_placa SET id_lugar = 0 WHERE id_placa = ? AND codigo = ? AND id_fondos = ?";

            // Prepara la consulta
            $stmt = $link->prepare($sql);

            // Verifica si la preparación de la consulta fue exitosa
            if ($stmt === false) {
                die("Error al preparar la consulta: " . $link->error);
            }

            // Itera sobre los activos de la tabla izquierda y actualiza los registros con el valor 0
            foreach ($activosIzquierda as $activo) {
                $idplaca = $activo['idplaca'];
                $stmt->bind_param("iii", $idplaca, $codigo, $id_fondos);
                if ($stmt->execute() === false) {
                    die("Error al ejecutar la consulta: " . $stmt->error);
                }
            }

            // Cierra la conexión a la base de datos
            $stmt->close();

            // Preparar la respuesta en formato JSON
            $response = array(
                'message' => 'Operación exitosa',
                'redirect' => 'formulario_agregar_ubicacion_general.php'
            );
            echo json_encode($response);
        } else {
            // Preparar la respuesta en formato JSON para manejar errores
            $response = array(
                'error' => 'No se recibieron datos de los input correctamente.'
            );
            echo json_encode($response);
        }
    } else {
        // Preparar la respuesta en formato JSON para manejar errores
        $response = array(
            'error' => 'No se recibieron datos de las tablas correctamente.'
        );
        echo json_encode($response);
    }
} else {
    // Preparar la respuesta en formato JSON para manejar errores
    $response = array(
        'error' => 'Solicitud no válida.'
    );
    echo json_encode($response);
}
?>