<?php
require_once 'bd.php';
//require_once 'conexion.php';
session_start();

header('Content-Type: application/json'); //Devuelve la información en formato Json


echo json_encode([
    "debug_sesion" => $_SESSION
]);
//exit();

$funcionario_json = $_SESSION['funcionario'] ?? null;
echo json_encode([
    "debug_sesion" => $funcionario_json
]);
//** SE CARGA LA SESIÓN Y DATOS DEL USUARIO */
//if (!isset($_SESSION['funcionario']) || empty($_SESSION['funcionario'])) {
try {
    // Se valida la Sesión
    if (!isset($_SESSION['usuario_id'])){
        echo json_encode(["error" => "Sesión no válida"]);
        //header('location: index.html');
        exit();
    }
    //Se obtien el Id desde la sesión del usuario
    $funcionario_id = $_SESSION['usuario_id'];

    // Conexión a la BD
    $conexionBD=BD::crearInstancia();    //Se crea la instancia de la conexión
    //$funcionario = $_SESSION['funcionario'] ?? null; //Obtiene el id del funcionario de la sesión, o null si no está definido
        //$funcionario_id = $datos_funcionario['id'] ?? null;
    /**
     * Validación -- Usuario existe
     */
    $sql = "SELECT * FROM usuarios WHERE id = ? LIMIT 1"; //Prepara la consulta SQL para seleccionar el usuario con el id del funcionario
    $consulta = $conexionBD->prepare($sql); //Prepara la consulta SQL para seleccionar el usuario con el id del funcionario
    //$consulta->bindValue(1, $funcionario, PDO::PARAM_INT); //vincula el valor del id del funcionario a la consulta
    $consulta->execute([$funcionario_id]); //Ejecuta la consulta con el id del funcionario
    //$usuario = [ 'id' => $funcionario]; //Devuelve el usuario encontrado, o null.
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    // Si no existe el usuario, se debe de crear en la BD
    if (!$usuario) {
        // Si no se encuentra el usuario, debe de guardar el funcionario en la tabla de usuarios
    /*$sql = "INSERT INTO usuarios (id) VALUES (?)"; //Prepara la consulta SQL para insertar el id del funcionario en la tabla de usuarios
        $consulta = $conexionBD->prepare($sql); //Prepara la consulta SQL para insertar el id del funcionario en la tabla de usuarios
        //$consulta->bindValue(1, $funcionario, PDO::PARAM_INT); //Vincula el valor del id del funcionario a la consulta
        $consulta->execute([$funcionario]); //Ejecuta la consulta para insertar el id del funcionario en la tabla de usuarios
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC); //Devuelve el usuario recién insertado

        //print_r("Usuario no existe en la BD, se ha creado un nuevo registro para el funcionario");
        */
        echo json_encode(["error" => "Usuario no Registrado. Se debe INSERTAR en la BD"]);
        exit();
    }
    /**
     * PERMISOS - Consulta los permisos
     */
    //Si el usuario si está en la tabla de usuarios, se puede continuar consultando los roles y permisos del usuario para mostrar el menú principal correspondiente a su rol
    //Se carga los módulos y acciones diponibles para el usuario, para mostrar el menú principal correspondiente a su ro
    $sql = "SELECT
                u.id AS usuario_id,
                u.nombre AS usuario_nombre,
                ur.subsistema_id,
                s.nombre AS subsistema,
                ur.codigo_pres,
                r.id_rol AS rol_id,
                r.rol,
                m.id AS modulo_id,
                m.nombre AS modulo,
                m.ruta_base,
                ac.id AS accion_id,
                ac.nombre AS accion,
                p.id AS permiso_id
            FROM usuarios u
            JOIN usuarios_roles ur ON ur.usuario_id = u.id
            JOIN t_roles r ON r.id_rol = ur.rol_id
            JOIN subsistemas s ON s.id = ur.subsistema_id
            JOIN roles_permisos rp  ON rp.rol_id = r.id_rol
            JOIN permisos p ON p.id = rp.permiso_id
            JOIN modulos m ON m.id = p.modulo_id
                        AND m.eliminado = 0
            JOIN acciones ac  ON ac.id = p.accion_id
                        AND ac.eliminado = 0
            WHERE u.eliminado = 0 AND u.id = ?"; //Prepara la consulta SQL para obtener los roles y permisos del usuario

    $consulta = $conexionBD->prepare($sql); //Prepara la consulta SQL para obtener los roles y permisos del usuario
    //$consulta->bindValue(1, $funcionario, PDO::PARAM_INT); //Vincula el valor del id del funcionario a la consulta
    $consulta->execute([$funcionario_id]); //Ejecuta la consulta para obtener los roles y permisos del usuario
    $permisos = $consulta->fetchAll(PDO::FETCH_ASSOC); //Devuelve todos los permisos del usuario

    /**
     * AGREPAR - Agrupar por Subsistema
     */

    //Extraigo unicamente los subsistemas, lo cuales son los cards del menú principal
    $menu = [];
    foreach ($permisos as $permiso){
        // if (!in_array($permiso['subsistema'], $subsistemas)){ //Si el subsistema del permiso no está en la lista de susbsistemas, se agrega a la lista de susbsistemas.
        //     $subsistemas[] = $permiso['subsistema']; //Agrega el subsistema a la lista.
        // }
        $subsistema = $permiso['subsistema'];

        if (!isset($menu[$subsistema])) {
            $menu[$subsistema] = [
                "nombre" => $subsistema,
                "descripcion" => "Acceso al módulo " .$subsistema,
                "imagen" => "img/default.png",
                "modulos" => []
            ];
        }

        $menu[$subsistema]["modulos"][] = [
            "id" => $permiso['modulo_id'],
            "nombre" => $permiso['modulo'],
            "ruta" => $permiso['ruta_base']
        ];
    }
        // Respuesta FINAL
        echo json_encode(array_values($menu));

} catch (Exception $e) {
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}

    // echo json_encode([
    //     'usuario' => $usuario, //Devuelve el usuario.
    //     'subsistemas' => $subsistemas, //Devuelve la lista de subsistemas del usuario
    //     'permisos' => $permisos //Devuelve la lista de permisos del usuario
    // ]);
    // exit;
    // }
    /*
     //Se decodifica la variagle de sesión del funcionario
$funcionario_json = $_SESSION['funcionario'] ?? null;

$funcionario_data = json_decode($funcionario_json, true);

$datos_funcionario = [];
$datos_funcionario =[
    'id' => $funcionario_data['Id_Empleado'],
    'nombre' => $funcionario_data['Nombre'],
    'apellidos' => $funcionario_data['Apellido1']. ' ' . $funcionario_data['Apellido2'],
    'cedula' => $funcionario_data['EMPCED'],
    'correo' => $funcionario_data['Correo_Electronico_Oficial'],
    'clasePuesto' => $funcionario_data['Clase_Puesto'],
    'especialidad' => $funcionario_data['Especialidad'],
    'idRegional' => $funcionario_data['DireccionesRegionales'],
    'regional' => $funcionario_data['NombreRegional'],
    'codigo_presu' => $funcionario_data['CentrosEducativosDondeTrabaja'],
    'dependencia' => $funcionario_data['Dependencia'],
    'circuito' => $funcionario_data['Circuito'],
    'idEmpleado' => $funcionario_data['Id_Empleado'],
    'sexo' => $funcionario_data['Sexo']

];
// Ahora sí accedes correctamente
$funcionario_id = $datos_funcionario['id'] ?? null;

//print_r($_SESSION); //Imprime el contenido de la sesión para verificar que el id del funcionario está presente

//header('Content-Type: application/json'); //Devuelve la información en formato Json

try {
     */
?>