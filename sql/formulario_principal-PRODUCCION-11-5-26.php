<?php
require_once 'bd.php';
//require_once 'conexion.php';
require_once '../usuarioAzure.php';

//session_start(); // Asegúrate de iniciar la sesión para acceder a las variables de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json'); //Devuelve la información en formato Json



try {
    
    // *** 1. SE VALIDA LA SESION *****
    $usuario_azure = obtenerUsuarioSesion(); //Obtiene los datos del Azure captados en el archivo usuarioAzure.php

    // Guarda debug de sesión
    $debug = [
        //"session" => $_SESSION,
        "DatosFuncionarios" => $usuario_azure

    ];
    
    if (!$usuario_azure) {
        echo json_encode([
            "error" => "Sesión Inválidad",
            "debug" => $debug
        ]);
        exit();
    }

    //Obtiene la Cédula desde el azure
    $funcionario_cedula = $usuario_azure['cedula'];

    // *** 2. CONEXIÓN A LA BD ****
    $conexionBD=BD::crearInstancia();    //Se crea la instancia de la conexión
    
    /**
      * *** 3. BUSCA EL USUARIO ****
     */
    $sql = "SELECT * FROM usuarios WHERE cedula = ? LIMIT 1"; //Prepara la consulta SQL para seleccionar el usuario con el id del funcionario
    
    $consulta = $conexionBD->prepare($sql); //Prepara la consulta SQL para seleccionar el usuario con el id del funcionario
    
    $consulta->execute([$funcionario_cedula]); //Ejecuta la consulta con el id del funcionario
    
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    // Si no existe el usuario, se debe de crear en la BD
    if (!$usuario) {
        // Si no se encuentra el usuario, debe de guardar el funcionario en la tabla de usuarios
    /*$sql = "INSERT INTO usuarios (id) VALUES (?)"; //Prepara la consulta SQL para insertar el id del funcionario en la tabla de usuarios
        $consulta = $conexionBD->prepare($sql); //Prepara la consulta SQL para insertar el id del funcionario en la tabla de usuarios
        //$consulta->bindValue(1, $funcionario, PDO::PARAM_INT); //Vincula el valor del id del funcionario a la consulta
        $consulta->execute([$funcionario]); //Ejecuta la consulta para insertar el id del funcionario en la tabla de usuarios
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC); //Devuelve el usuario recién insertado
        */
        echo json_encode([
            "error" => "Usuario no Registrado. Se debe INSERTAR en la BD",
            "debug" => $debug
            ]);
        exit();
    }

    $usuario_id = $usuario['id'];
    /**
     * PERMISOS - Consulta los permisos
     */
    //Si el usuario si está en la tabla de usuarios, se puede continuar consultando los roles y permisos del usuario para mostrar el menú principal correspondiente a su rol
    //Se carga los módulos y acciones diponibles para el usuario, para mostrar el menú principal correspondiente a su ro
    /*$sql = "SELECT
                u.id AS usuario_id,
                u.nombre AS usuario_nombre,
                ur.subsistema_id,
                s.nombre AS subsistema,
                s.descripcion as subsistemaDescripcion,
                s.imagen as imagen,
                ur.codigo_pres,
                r.id_rol AS rol_id,
                r.rol,
                m.id AS modulo_id,
                m.nombre AS modulo,
                m.ruta_base,
                m.descripcion as moduloDescripcion,
                m.imagen as moduloImagen,
                ac.id AS accion_id,
                ac.nombre AS accion,
                p.id AS permiso_id
            FROM usuarios u
            JOIN usuarios_roles ur ON ur.usuario_id = u.id
            JOIN t_roles r ON r.id_rol = ur.rol_id
            JOIN subsistemas s ON s.id = ur.subsistema_id
            JOIN roles_permisos rp ON rp.rol_id = r.id_rol
            JOIN permisos p ON p.id = rp.permiso_id
            JOIN modulos m ON m.id = p.modulo_id
                        AND m.eliminado = 0
                        AND m.subsistema_id = ur.subsistema_id
            JOIN acciones ac ON ac.id = p.accion_id
                        AND ac.eliminado = 0
            WHERE u.eliminado = 0 AND u.id = ?"; //Prepara la consulta SQL para obtener los roles y permisos del usuario
            */
    /**
     * *** 4. CONSULTA PERMISOS EN LA BD ***
     */
    $sql = "SELECT 
                u.id AS usuario_id, 
                u.nombre AS usuario_nombre, 

                s.id AS subsistema_id,
                s.nombre AS subsistema, 
                s.descripcion AS subsistema_descripcion, 
                s.imagen as subsistema_imagen, 
                
                ur.codigo_pres, 
                ur.subsistema_id, 
                
                r.id_rol AS rol_id, 
                r.rol, 
                
                m.id AS modulo_id, 
                m.nombre AS modulo, 
                m.descripcion AS modulo_descripcion, 
                m.ruta_base, 
                m.imagen as modulo_imagen, 
                
                f.id AS formulario_id, 
                f.nombre AS formulario, 
                f.descripcion AS formulario_descripcion, 
                f.ruta, 
                f.imagen AS formulario_imagen, 
                f.orden, 
                
                ac.id AS accion_id, 
                ac.nombre AS accion, 
                
                p.id AS permiso_id 
                
                FROM usuarios u 
                INNER JOIN usuarios_roles ur ON ur.usuario_id = u.id 
                INNER JOIN t_roles r ON r.id_rol = ur.rol_id 
                INNER JOIN subsistemas s ON s.id = ur.subsistema_id 
                INNER JOIN roles_permisos rp ON rp.rol_id = r.id_rol 
                INNER JOIN permisos p ON p.id = rp.permiso_id 
                INNER JOIN formularios f ON f.id = p.formulario_id AND f.eliminado = 0 
                INNER JOIN modulos m ON m.id = f.modulo_id AND m.subsistema_id = ur.subsistema_id 
                INNER JOIN acciones ac ON ac.id = p.accion_id 
                WHERE u.eliminado = 0 AND u.id = ? 
                ORDER BY s.nombre, m.nombre, f.orden, ac.nombre";

    $consulta = $conexionBD->prepare($sql); //Prepara la consulta SQL para obtener los roles y permisos del usuario
    //$consulta->bindValue(1, $funcionario, PDO::PARAM_INT); //Vincula el valor del id del funcionario a la consulta
    $consulta->execute([$usuario_id]); //Ejecuta la consulta para obtener los roles y permisos del usuario
    $permisos = $consulta->fetchAll(PDO::FETCH_ASSOC); //Devuelve todos los permisos del usuario

    /**
     * *** 5. CONSTUIR EL MENÚ DINÁMICO **** JSON ***
     */

    //Extraigo unicamente los subsistemas, lo cuales son los cards del menú principal
    $menu = [];
    foreach ($permisos as $permiso) {

        $subsistemaId = $permiso['subsistema_id'];
        $moduloId = $permiso['modulo_id'];
        $formularioId = $permiso['formulario_id'];

        // ** SUBSISTEMA ***
        if (!isset($menu[$subsistemaId])) {
            
            $menu[$subsistemaId] = [
                "id" => $subsistemaId,
                "nombre" => $permiso['subsistema'],
                "descripcion" => $permiso['subsistema_descripcion'] ?? '', //Aquí se puede agregar la descripción del subsistema si está disponible en la consulta SQL
                "imagen" => $permiso['subsistema_imagen'], //Aquí se puede agregar la ruta de la imagen correspondiente al subsistema
                "modulos" => []
            ];
        }

        // ** MODULOS ****
        //Si el módulo del permiso no está en la lista de módulos del subsistema, se agrega a la lista de módulos del subsistema.
        if (!isset($menu[$subsistemaId]["modulos"][$moduloId])) { 

            $menu[$subsistemaId]["modulos"][$moduloId] = [ //Agrega el módulo a la lista de módulos del subsistema
                "id" => $moduloId,
                "nombre" => $permiso['modulo'],
                "ruta" => $permiso['ruta_base'],
                //"ruta" => $rutaCompleta,
                "descripcion" => $permiso['modulo_descripcion'],
                "imagen" => $permiso['modulo_imagen'],
                "formularios" => []
            ];
         }

        // *** FORMULARIOS ***
        if (!isset($menu[$subsistemaId]["modulos"][$moduloId]['formularios'][$formularioId])) { 
            // ====== Contrucción de la ruta Completa (MODULO + FORMULARIO) =========
            //Construye la ruta completa del formulario concatenando la ruta base del módulo con la ruta del formulario, eliminando cualquier barra adicional al inicio o al final de cada parte.
            $rutaCompleta = basename($permiso['ruta']); // Extrae solo el nombre del archivo del formulario para evitar problemas con rutas relativas


            $menu[$subsistemaId]["modulos"][$moduloId]['formularios'][$formularioId] = [ //Agrega la lista de formularios dentro de los módulos
                "id" => $formularioId,
                "nombre" => $permiso['formulario'],
                //"ruta" => $permiso['ruta'],
                "ruta" => $rutaCompleta, //// Ruta real del archivo (segura)
                "descripcion" => $permiso['formulario_descripcion'],
                "imagen" => $permiso['formulario_imagen'],
                "orden" => $permiso['orden'],
                "acciones" => [] //Acciones (crear/editar/etc.)
            ];
        } 

        // *** ACCIONES ***
        $menu[$subsistemaId]["modulos"][$moduloId]['formularios'][$formularioId]['acciones'][] = [  //Agrega la lista de acciones dentro de los formularios
            "id" => $permiso['accion_id'],
            "nombre" => $permiso['accion']
        ];
    }

    /** *** 6. REINDEXAR ARREGLOS *** */
    foreach ($menu as &$subsistema) { //Reindexa los subsistemas
         //Reindexa los módulos dentro de cada subsistema
        foreach ($subsistema['modulos'] as &$modulo) {
            $modulo['formularios'] = array_values($modulo['formularios']); //Reindexa los formularios dentro de cada módulo
        }
        $subsistema['modulos'] = array_values($subsistema['modulos']); //Reindexa los módulos dentro de cada subsistema
    }

    // *** 7. RESPUESTA FINAL ****
    echo json_encode([
        "Ok" => true,
        "usuario" => $usuario['nombre'],
        "data" => array_values($menu)
    ], JSON_UNESCAPED_UNICODE);

    /*foreach ($permisos as $permiso){
        // if (!in_array($permiso['subsistema'], $subsistemas)){ //Si el subsistema del permiso no está en la lista de susbsistemas, se agrega a la lista de susbsistemas.
        //     $subsistemas[] = $permiso['subsistema']; //Agrega el subsistema a la lista.
        // }
        $subsistema = $permiso['subsistema'];
        $imagen = $permiso['imagen']; //Aquí se puede agregar la ruta de la imagen correspondiente al subsistema
        $descripcion = $permiso['subsistemaDescripcion'];
        //$moduloDescripcion = $permiso['moduloDescripcion'];

        if (!isset($menu[$subsistema])) {
            $menu[$subsistema] = [
                "nombre" => $subsistema,
                "descripcion" => $descripcion ?? '', //Aquí se puede agregar la descripción del subsistema si está disponible en la consulta SQL
                "imagen" => $imagen, //Aquí se puede agregar la ruta de la imagen correspondiente al subsistema
                "modulos" => []
            ];
        }

        //Agrupo por módulo dentro de cada subsistema
        $modulo_id = $permiso['modulo_id'];

        if (!isset($menu[$subsistema]["modulos"][$modulo_id])) { //Si el módulo del permiso no está en la lista de módulos del subsistema, se agrega a la lista de módulos del subsistema.
            $menu[$subsistema]["modulos"][$modulo_id] = [ //Agrega el módulo a la lista de módulos del subsistema
                "id" => $modulo_id,
                "nombre" => $permiso['modulo'],
                "ruta" => $permiso['ruta_base'],
                "descripcion" => $permiso['moduloDescripcion'],
                "imagen" => $permiso['moduloImagen'],
                "acciones" => []
            ];
        }

        /* Guarda acciones disponibles *
        $menu[$subsistema]["modulos"][$modulo_id]["acciones"][] = $permiso['accion']; //Agrega la acción a la lista de acciones del módulo del subsistema

    }
        // Respuesta FINAL
        foreach ($menu as &$item) {
            $item["modulos"] = array_values($item["modulos"]);
        }

        echo json_encode([
            "data" => array_values($menu)
        ]);

        */
        //echo json_encode(array_values($menu));
        /*echo json_encode([
            "data" => array_values($menu),
            "debug" => $debug
        ]);*/

} catch (Exception $e) {
    echo json_encode([
        "OK" => false,
        "error" => $e->getMessage()
    ]);
}

?>