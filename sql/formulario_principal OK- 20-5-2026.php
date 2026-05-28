<?php
/**
 * FUNICONES:
 *  - Validar sesión Azure
 *  - Crear usuarios automáticamente si no están en la tabla Usuarios
 *  - Sincronizar roles automáticamente (revisión de T_Lista_Blanca)
 *  - Construción del menú Dinámico
 *  - Actualización del último acceo del usuario.
 * ===================================================================
 */
declare(strict_types=1); //Habilita el modo estricto para una mejor gestión de tipos de datos y errores en tiempo de ejecución

require_once 'bd.php';
//require_once 'conexion.php';
//require_once '../usuarioAzure.php';
require_once __DIR__ . '/../usuarioAzure.php';

//session_start(); // Asegúrate de iniciar la sesión para acceder a las variables de sesión

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Devuelve la información en formato Json
header('Content-Type: application/json'); 

try {
    
    // *** 1. SE VALIDA LA SESION AZURE *****
    $usuario_azure = obtenerUsuarioSesion(); //Obtiene los datos del Azure captados en el archivo usuarioAzure.php
    // Guarda debug de sesión
    $debug = [
        //"session" => $_SESSION,
        "DatosFuncionarios" => $usuario_azure

    ];
    //Se valida existe la sesion
    if (!$usuario_azure) {
        echo json_encode([
            "error" => "Sesión Inválida",
            "debug" => $debug
        ]);
        exit();
    }

    // === 2. OBTENER DATOS DE USUARIO ====
    //Obtiene la Cédula desde el azure
    $funcionario_cedula = trim($usuario_azure['cedula'] ?? '');

    //Obtiene le nombre completo del usuario
    $funcionario_nombre = trim($usuario_azure['nombre'] ?? '');

    // El correo del funcionario obtenido del Azure, o un valor por defecto si no está disponible
    $funcionario_correo = trim($usuario_azure['correo'] ?? '');

    //Obteien el código presupestario desde el azure.
    $funcionario_codigo_presu = trim($usuario_azure['codigoPresu'] ?? ''); 

    // El ID de Azure del funcionario obtenido del Azure, o un valor por defecto si no está disponible
    $funcionario_azure_id = trim($usuario_azure['azure_id'] ?? '');

    // El sexo del funcionario obtenido del Azure, o un valor por defecto si no está disponible
    $sexoAzure = strtolower(trim($usuario_azure['sexo'] ?? '')); 

    // ==== VALIDAR DATOS OBLIGATORIOS ===

    if (empty($funcionario_cedula)) {
        throw new Exception("La cédula del usuario es requerida.");
    }
    if (empty($funcionario_codigo_presu)) {
        throw new Exception("El código presupuestario es requerido.");
    }

    // ==== Mapear el SEXO del Azure al formato esperado en la base de datos ====
       $sexo = null;

    if ($sexoAzure === 'm' || $sexoAzure === 'masculino' || $sexoAzure === 'hombre') {
        $sexo = 1;
    }
    if ($sexoAzure === 'f' || $sexoAzure === 'femenino' || $sexoAzure === 'mujer') {
        $sexo = 2;
    }
    
    // === Roles válidos para insertar ====
     $rolesValidos = [2, 3, 4, 7]; //2=administrador | 3 = Prestador | 4 = Inventariador | 7 = Consultor
    
    // ***  CONEXIÓN A LA BD ****
    $conexionBD=BD::crearInstancia();    //Se crea la instancia de la conexión

    // === INICIAR TRANSACCIÓN === Para integridad de la BD
    if (!$conexionBD->inTransaction()) { //Verifica si hay una transacción activa
        $conexionBD->beginTransaction();
    }
    

    //* *** 3. BUSCA EL USUARIO ****  // Tabla Usuarios
    $usuario = buscaUsuario($conexionBD, $funcionario_cedula); //Busca el usuario en la base de datos para obtener su id y verificar si es superadmin o no

    $rolActualId = 0; //Variable para almacenar el id del rol actual.
    // A.2.1 ===  4. SI EL USUARIO NO EXISTE ( se debe de crear en la BD) ===
    if (!$usuario) {
    
        // ==== A.2.1.1 ... INSERTAR EL USUARIO =====
        $sql = "INSERT INTO usuarios (
            cedula, 
            nombre, 
            correo, 
            azure_id, 
            sexo, 
            created_at, 
            updated_at
            ) 
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())"; //Prepara la consulta SQL para insertar el id del funcionario en la tabla de usuarios
        
        $consulta = $conexionBD->prepare($sql); //Prepara la consulta SQL para insertar el id del funcionario en la tabla de usuarios
        
        $consulta->execute([
            $funcionario_cedula, 
            $funcionario_nombre, 
            $funcionario_correo,
            $funcionario_azure_id,
            $sexo
        ]); //Ejecuta la consulta para insertar el id del funcionario en la tabla de usuarios
      
        // === OBTENER EL USUARIO RECIEN INSERTADO ====
        $usuario_id =(int)$conexionBD->lastInsertId(); //Obtiene el id del usuario recién insertado en la base de datos para su uso posterior en la consulta de permisos
        
        // ==== A.2.1.2 CONSULTAR USUARIO EN T_LISTA_BLANCA: Si obtiene el rol, sino está, es USUARIO BÁSICO ====
        $rolListaBlanca = verificarListaBlanca($conexionBD, $funcionario_cedula, $funcionario_codigo_presu); //Verifica si el usuario tiene un rol asignado en la lista blanca según su cédula y código presupuestario, 
        
        // ==== SI USUARIO EXISTE EN LISTA BLANCA ======
        if (!empty($rolListaBlanca)) { // Si el usuario tiene un rol asignado en la lista blanca, se asigna ese rol al usuario en la tabla usuarios_roles
            //===== A.2.1.3.1. ASIGNA LOS ROLES DE ACUERDO A LA LISTA BLANCA //  1 = Root | 2= Administrador | 3= Prestador | 4= Invetariador | 7 = Consultor
            $rol_id = (int)$rolListaBlanca['id_rol']; //Obtiene el id del rol asignado al usuario en la lista blanca para su uso posterior en la consulta de permisos
            // === VALIDAR ROL ====
            if (in_array($rol_id, $rolesValidos)) { // Si el rol de la LISTA BLANCA es válido (está en el arreglo)
                insertarRolUsuario($conexionBD, $usuario_id, $rol_id, 1, $funcionario_codigo_presu); //Ejecuta la consulta para asignar el rol por defecto al usuario                
            };
        } else { // ASIGNAR ROL SOLICITANTE (usuario básico) --- No existe en la tabla Lista Blanca
            // ===== A.2.1.3.2 ASIGNA EL ROL DE SOLICITANTE ==== 
            insertarRolUsuario($conexionBD, $usuario_id, 5, 1, $funcionario_codigo_presu); //Ejecuta la consulta para asignar el rol por defecto al usuario
        }
        // ==== RECONSULTAR USUARIO =====
         $usuario = buscaUsuario($conexionBD, $funcionario_cedula); 

         //=== OBTENER EL ROL ACTUAL PARA LUEGO CONSTRUIR EL MENÚ CORRECTAMENTE ===
        $rolActual = obtenerRolUsuario($conexionBD, $usuario_id, $funcionario_codigo_presu);
        if (!empty($rolActual) && isset($rolActual['rol_id'])) {
            $rolActualId = (int)$rolActual['rol_id']; //Obtiene el id del rol actual asignado al usuario en la tabla usuarios_roles para su uso posterior en la construcción del menú
        }

    } else { // === A.2.2. USUARIO EXISTE: Existe en la tabla Usuarios ======
        
        $usuario_id = (int) $usuario['id'] ?? 0; //Obtiene el id del usuario encontrado en la base de datos para su uso posterior en la consulta de permisos
        
        // A.2.2.1 VERIFICAR  ROL ACTUAL EN LA TABLA USUARIOS_ROLES -- si el usuario tiene un rol asignado en la tabla usuarios_roles según su id y código presupuestario
        //$rolesAsignados = existeRolUsuario($conexionBD, $usuario_id, $funcionario_codigo_presu); 
        $rolActual = obtenerRolUsuario($conexionBD, $usuario_id, $funcionario_codigo_presu);
        
        //=== CONSULTAR ROL ESPERADO EN LISTA BLANCA =====
        $rolListaBlanca = verificarListaBlanca($conexionBD, $funcionario_cedula, $funcionario_codigo_presu); 

        // === sI EXISTE EL ROL ACTUAL ====
        if ($rolActual) {
            
            $rolActualId = (int)$rolActual['rol_id'];  //Obtiene el id del rol actual asignado al usuario en la tabla usuarios_roles

            // == EXISTE EN LISTA BLANCA ===
            if ($rolListaBlanca) {
                
                $rolEsperado = (int)$rolListaBlanca['id_rol']; //Obtiene el id del rol asignado al usuario en la lista blanca para su uso posterior en la comparación
                
                // === VALIDA ROL ESPERADO (LISTA BLANCA), SI ESTÁ EN  ROLES VALIDOS ====
                if (in_array($rolEsperado, $rolesValidos)) { 
                    
                    // === A.2.2.1.1 COMPARA ROL Actual (BD) CON ROL ESPERADO (t_lista_blanca) == // === SI EL ROL DE LISTA BLANCA CAMBIÓ
                    if ($rolActualId !== $rolEsperado) { //Si el rol actual en la tabla usuarios_roles es diferente al rol asignado, se actualiza el rol del usuario en la tabla usuarios_roles para que coincida con el rol asignado al usuario en la lista blanca.
                        
                        // ACTUALIZAR ROL DEL USUARIO ==  //Ejecuta la consulta para actualizar el rol del usuario al nuevo rol de la lista blanca
                        actualizarRolUsuario($conexionBD, $usuario_id, $rolEsperado, $funcionario_codigo_presu);
                    }
                }
            } else { // NO EXISTE EN LISTA BLANCA ====  se Asigna el rol de solicitante (usuario básico)
                if ($rolActualId !== 5 ) { // Si el rol actual del usuario no es ya el rol de solicitante (id_rol = 5), se actualiza el rol del usuario a solicitante.
                    //insertarRolUsuario($conexionBD, $usuario_id, 5, 1, $funcionario_codigo_presu); //Ejecuta la consulta para actualizar el rol del usuario a solicitante
                    actualizarRolUsuario($conexionBD, $usuario_id, 5, $funcionario_codigo_presu); //Ejecuta la consulta para actualizar el rol del usuario a solicitante
                }
            }
        } else { // == NO EXISTE EL ROL ASIGNADO:  SI NO ESTÁ EN TABLA USUARIOS_ROLES ====, Verifica la tabla T_Lista_Blanca
            
            // ==== 2.1.2.1 = ASIGNA LOS ROLES DE ACUERDO A LA LISTA BLANCA
            if ($rolListaBlanca) { //EXISTE EN la tabla LISTA BLANCA, se ASGINA EL ROL ENCONTRADO. 
                $rolEsperado = (int)$rolListaBlanca['id_rol']; //Obtiene el id del rol asignado al usuario en la lista blanca para su uso posterior en la comparación
                // === VALIDAR ROL ===
                if (in_array($rolEsperado, $rolesValidos)) { // Si es un rol válido
                    insertarRolUsuario($conexionBD, $usuario_id, $rolEsperado, 1, $funcionario_codigo_presu); //Ejecuta la consulta para asignar el rol por defecto al usuario                    
                } 
            } else { //NO EXISTE en la tabla LISTA BLANCA, se le ASIGNA ROL SOLICITANTE (usuario básico)
                // ===== 2.1.2.2 ASIGNA EL ROL DE SOLICITANTE A TODOS LOS USUARIOS ==== 
                insertarRolUsuario($conexionBD, $usuario_id, 5, 1, $funcionario_codigo_presu); //Ejecuta la consulta para asignar el rol por defecto al usuario
            }
        }
    }

    // ACTUALIZA EL ÚLTIMO INICIO DE SESIÓN DEL USUARIO
    $sql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?"; //Prepara la consulta SQL para actualizar el último inicio de sesión del usuario
    $consulta = $conexionBD->prepare($sql); //Prepara la consulta SQL para actualizar el último inicio de sesión del usuario
    $consulta->execute([$usuario_id]); //Ejecuta la consulta para actualizar el último inicio de sesión del usuario

    // === CONSULTAR MENÚ ===
    //if ($usuario['superadmin'] == 1) {
    if ( $rolActualId === 1 ) { //Usuario es Root
        $permisos = consultaMenuRoot($conexionBD, $usuario_id);
    } else {
        $permisos = consultaMenuUsuarios($conexionBD, $usuario_id);
    }
    
    //=== CONSTRUIR EL MENU ====
    $menu = crearMenu($permisos);

    // ==== COMIIT DE LA TRANSACCIÓN =====
    $conexionBD->commit();

    // *** 7. RESPUESTA FINAL ****
    echo json_encode([
        "Ok" => true,
        "usuario" => $usuario['nombre'],
        //"dataS" => array_values($menu),
        "data" => $menu,
        "funcionario" => $usuario_azure, //Agrega los datos del funcionario obtenidos del Azure a la respuesta JSON para su uso en el frontend
        //"datos" => $_SESSION['funcionario'] ?? null, //Agrega los datos de la sesión completa del funcionario obtenidos del Azure a la respuesta JSON para su uso en el frontend
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    // == ROLLBACK si ocurre un error 
    if (isset($conexionBD) && $conexionBD->inTransaction()) {
        $conexionBD->rollBack();
    }

    echo json_encode([
        "OK" => false,
        "error" => $e->getMessage()
    ]);
}

// ** OK *** ==== FUNCIÓN: OBTENER ROL ACTUAL: para verificar si el usuario tiene un rol específico asignado en la tabla usuarios_roles según el código presupuestario
function obtenerRolUsuario(PDO $conexionBD, int $usuarioId, string $codigoPresu): array|false { //: array|false indica que la función puede devolver un arreglo (si se encuentra el rol) o false (si no se encuentra el rol)
    // Prepara la consulta SQL para verificar si el usuario tiene el rol específico asignado en la tabla usuarios_roles según el código presupuestario
    //AND rol_id = ? Un usuairo solo tiene un rol por codigo prresupuestario,
    $sql = "SELECT id, rol_id
            FROM usuarios_roles
            WHERE usuario_id = ?
            AND codigo_presu = ?
            LIMIT 1";

    $consulta = $conexionBD->prepare($sql);

    $consulta->execute([
        $usuarioId,
        $codigoPresu
    ]);
    return $consulta->fetch(PDO::FETCH_ASSOC); // Devuelve el registro si existe, o false si no existe, lo que indica si el usuario tiene el rol asignado o no
}
 
// *OK *** ==== VERIFITAR T_LISTA_BLANCA: Si obtiene el rol, sino está, es USUARIO BÁSICO ====
function verificarListaBlanca(PDO $conexionBD, string $cedula, string $codigoPresu): array|false {
// Prepara la consulta SQL para verificar si el usuario tiene un rol específico asignado en la tabla usuarios_roles según el código presupuestario
    $sql = "SELECT id_rol 
            FROM t_lista_blanca
            WHERE cedula = ?
            AND codigo = ?
            LIMIT 1";

    $consulta = $conexionBD->prepare($sql);

    $consulta->execute([
        $cedula,
        $codigoPresu
    ]);
    // $rol = $consulta->fetch(PDO::FETCH_ASSOC); //Devuelve el rol asignado al usuario en la lista blanca, o false si no tiene un rol asignado en la lista blanca. Si el usuario tiene más de un rol asignado en la lista blanca, se puede modificar esta lógica para asignar el rol más relevante o combinar los permisos de los roles asignados.
    return $consulta->fetch(PDO::FETCH_ASSOC); // Devuelve el registro si existe, o false si no existe, lo que indica si el usuario tiene el rol asignado o no
}

// * OK **** === FUNCIÓN: INSERTAR ROL ===
function insertarRolUsuario(PDO $conexionBD, int $usuarioId, int $rolId, int  $subsistemaId, string $codigoPresu): void {
   $sql = "INSERT INTO usuarios_roles
            (
                usuario_id,
                rol_id,
                subsistema_id,
                codigo_presu,
                created_at
            )
            VALUES (?, ?, ?, ?, NOW())";

    $consulta = $conexionBD->prepare($sql);

    $consulta->execute([
        $usuarioId,
        $rolId,
        $subsistemaId,
        $codigoPresu
    ]);
}

// * OK *** ==== FUNCIÓN: ACTUALIZAR ROL ===
function actualizarRolUsuario(PDO $conexionBD, int $usuarioId, int $rolId, string $codigoPresu): void {
    
    $sql = "UPDATE usuarios_roles
            SET
                rol_id = ?
            WHERE usuario_id = ?
            AND codigo_presu = ?";

    $consulta = $conexionBD->prepare($sql);

    $consulta->execute([$rolId, $usuarioId, $codigoPresu]);
}

//* OK ***  BUSCA EL USUARIO **** Busca en la tabla Usuarios
function buscaUsuario(PDO $conexionBD, string $cedula): array|false {
    $sql = "SELECT * FROM usuarios WHERE cedula = ? LIMIT 1"; //Prepara la consulta SQL para seleccionar el usuario con el id del funcionario
    
    $consulta = $conexionBD->prepare($sql); //Prepara la consulta SQL para seleccionar el usuario con el id del funcionario
    
    $consulta->execute([$cedula]); //Ejecuta la consulta con el id del funcionario
    
    return $consulta->fetch(PDO::FETCH_ASSOC); //Devuelve el usuario encontrado en la base de datos, o false si no se encuentra el usuario
}
// * OK *** ===== FUNCION: CONSULTAR MENU ROOT =====
function consultaMenuRoot(PDO $conexionBD, int $usuario_id): array {
    
    $sql = "SELECT 
            u.id AS usuario_id,
            u.nombre AS usuario_nombre,

            s.id AS subsistema_id,
            s.nombre AS subsistema,
            s.descripcion AS subsistema_descripcion,
            s.imagen AS subsistema_imagen,

            r.id_rol AS rol_id,
            r.rol,

            m.id AS modulo_id,
            m.nombre AS modulo,
            m.descripcion AS modulo_descripcion,
            m.ruta_base,
            m.imagen AS modulo_imagen,

            f.id AS formulario_id,
            f.nombre AS formulario,
            f.descripcion AS formulario_descripcion,
            f.ruta,
            f.imagen AS formulario_imagen,
            f.orden,

            ac.id AS accion_id,
            ac.nombre AS accion,

            NULL AS permiso_id

        FROM usuarios u

        INNER JOIN usuarios_roles ur ON ur.usuario_id = u.id

        INNER JOIN t_roles r ON r.id_rol = ur.rol_id

        INNER JOIN subsistemas s ON s.eliminado = 0

        INNER JOIN modulos m ON m.subsistema_id = s.id AND m.eliminado = 0

        INNER JOIN formularios f ON f.modulo_id = m.id AND f.eliminado = 0

        CROSS JOIN acciones ac

        WHERE u.eliminado = 0 AND u.id = ? AND r.id_rol = 1

        ORDER BY 
            s.nombre,
            m.nombre,
            f.orden,
            ac.nombre;";
    $consulta = $conexionBD->prepare($sql); //Prepara la consulta SQL para obtener los roles y permisos del usuario
    $consulta->execute([$usuario_id]); //Ejecuta la consulta para obtener los roles y permisos del usuario
    return $consulta->fetchAll(PDO::FETCH_ASSOC); //Devuelve todos los permisos del usuario
    
}
// * OK *** === FUNCIÓN: CONSULTAR MENU USUARIOS =====
function consultaMenuUsuarios(PDO $conexionBD, int $usuario_id): array {
  
    $sql = "SELECT 
                u.id AS usuario_id, 
                u.nombre AS usuario_nombre, 

                s.id AS subsistema_id,
                s.nombre AS subsistema, 
                s.descripcion AS subsistema_descripcion, 
                s.imagen as subsistema_imagen, 
                        
                ur.codigo_presu, 
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
            INNER JOIN subsistemas s ON s.id = ur.subsistema_id and s.eliminado = 0 
            INNER JOIN roles_permisos rp ON rp.rol_id = r.id_rol 
            INNER JOIN permisos p ON p.id = rp.permiso_id 
            INNER JOIN formularios f ON f.id = p.formulario_id AND f.eliminado = 0 
            INNER JOIN modulos m ON m.id = f.modulo_id AND m.subsistema_id = ur.subsistema_id 
            INNER JOIN acciones ac ON ac.id = p.accion_id 
            WHERE u.eliminado = 0 AND u.id = ? 
            ORDER BY s.nombre, m.nombre, f.orden, ac.nombre";

    $consulta = $conexionBD->prepare($sql); //Prepara la consulta SQL para obtener los roles y permisos del usuario
    $consulta->execute([$usuario_id]); //Ejecuta la consulta para obtener los roles y permisos del usuario
    return $consulta->fetchAll(PDO::FETCH_ASSOC); //Devuelve todos los permisos del usuario
}

// * ok *** === FUNCIÓN: CREAR MENU ====
function crearMenu(array $permisos): array {
    //* *** 5. CONSTUIR EL MENÚ DINÁMICO **** JSON ***
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
            //$rutaCompleta = basename($permiso['ruta']); // Extrae solo el nombre del archivo del formulario para evitar problemas con rutas relativas

            $rutaFormulario = '';
            //Valida si existe la ruta(nombre del archivo), si existe, Extrae solo el nombre del archivo del formulario para evitar problemas con rutas relativas
            if (!empty($permiso['ruta'])) { 
                $rutaFormulario = basename((string)$permiso['ruta'] ?? ''); //Extrae solo el nombre del archivo. Ej. formularios/usuarios.php => usuarios.php
            }
            $menu[$subsistemaId]["modulos"][$moduloId]['formularios'][$formularioId] = [ //Agrega la lista de formularios dentro de los módulos
                "id" => $formularioId,
                "nombre" => $permiso['formulario'],
                //"ruta" => $permiso['ruta'],
                //"ruta" => basename($permiso['ruta']), // Extrae solo el nombre del archivo del formulario para evitar problemas con rutas relativas
                "ruta" => $rutaFormulario,
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

    return array_values($menu);
}
?>