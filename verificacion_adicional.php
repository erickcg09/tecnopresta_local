<?php
session_start();
$tienellave = in_array($_SESSION['tipo'], [1,7]);
if ($tienellave == false){
    echo '<script language = javascript>
    alert("No tienes permisos")
    self.location = "formulario_menu_inventario.html"
    </script>';
}

require_once("conexion.php");
$link = $mysqli;
if (mysqli_connect_errno()) {
    echo "Error de conexion a mysql: " . mysqli_connect_error();
    exit();
}

// Verificar si la variable de sesión 'cedula' existe
if (!isset($_SESSION['cedula'])) {
    echo '<script language = javascript>
    alert("Sesión no válida")
    self.location = "formulario_menu_inventario.html"
    </script>';
    exit();
}

$logusuario = $_SESSION['cedula'];

// Consultar si el usuario está en la tabla de permisos especiales
$query = "SELECT cedula, funcionario FROM t_permiso_ext WHERE cedula = ?";
$stmt = mysqli_prepare($link, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $logusuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        // Usuario autorizado - crear variable de sesión adicional
        $_SESSION['adicional'] = 'autorizado';
        
        // Redirigir a formulario_seleccionar_analista.php
        echo '<script language = javascript>
        alert("Acceso autorizado")
        self.location = "formulario_seleccionar_analista.php"
        </script>';
    } else {
        // Usuario NO autorizado - redirigir a administracion_plataforma_soporte.php
        echo '<script language = javascript>
        alert("No tienes acceso especial")
        self.location = "administracion_plataforma_soporte.php"
        </script>';
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Error en la consulta: " . mysqli_error($link);
    echo '<script language = javascript>
    alert("Error en el sistema")
    self.location = "formulario_menu_inventario.html"
    </script>';
}

// Cerrar conexión
mysqli_close($link);
?>