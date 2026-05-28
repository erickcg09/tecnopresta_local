<?php
if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

function obtenerUsuarioSesion() {
    
    if (!isset($_SESSION['funcionario']) || empty($_SESSION['funcionario'])) {
        return null;
    }
    //return $_SESSION['funcionario'];

    $f = $_SESSION['funcionario'];
    // Validar sesión secundaria
    $auth = $f['auth'] ?? [];
    
    return [
        "id" => $f['Id_Empleado'] ?? null, 
        "nombre" => $f['Nombre'] ?? '',
        "apellidos" => ($f['Apellido1'] ?? '') . ' ' . ($f['Apellido2'] ?? ''),
        "cedula" => $f['EMPCED'] ?? null,
        "correo" => $f['Correo_Electronico_Oficial'] ?? null,
        "clasePuesto" => $f['Clase_Puesto'] ?? null,
        "especialidad" => $f['Especialidad'] ?? null,
        "idRegional" => $f['DireccionesRegionales'] ?? null,
        "regional" => $f['NombreRegional'] ?? null,
        "codigoPresu" => $f['CentrosEducativosDondeTrabaja'] ?? null,
        "dependencia" => $f['Dependencia'] ?? null,
        "circuito" => $f['Circuito'] ?? null,
        "idEmpleado" => $f['Id_Empleado'] ?? null,
        "sexo" => $f['Sexo'] ?? null,

        "fotoPerfil" => $f['fotoPerfil'] ?? 'assets/img/avatarH.svg', // Asegúrate de que el campo correcto esté aquí
        // === Datos de Autorización =====
        "esRoot" => $auth['es_root'] ?? false, //True indica que el usuario es root
        "roles" => $auth['roles'] ?? [], //Carga los roles del usuario
        "rutasPermitidas" => $auth['rutas_permitidas'] ?? [] //Rutas a las que tiene acceso este usuario
    ];
}

//print_r(obtenerUsuarioSesion());

?>