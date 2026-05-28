<?php
/*
================================================================
TecnoPresta — usuarioAzure.php
Propósito: Centraliza la lectura de los datos del usuario
           autenticado desde la sesión PHP.
           Es el único lugar donde se accede a $_SESSION
           para datos del usuario. Todos los demás archivos
           usan obtenerUsuarioSesion().

Flujo de datos:
  1. index.html → login con Azure (MSAL)
  2. sesionCargaSW.php → guarda datos en $_SESSION['funcionario']
  3. fotoAzure.php → guarda foto en $_SESSION['funcionario']['fotoPerfil']
  4. usuarioAzure.php → lee $_SESSION['funcionario'] y lo expone
  5. header.php → usa obtenerUsuarioSesion() para mostrar datos

Estructura esperada de $_SESSION['funcionario']:
  [
    'Nombre'               => 'María',
    'Apellidos'            => 'Rodríguez López',
    'Correo'               => 'maria.rodriguez@mep.go.cr',
    'Codigo_Presupuestario'=> '0001',
    'Dependencia'          => 'Liceo Nacional',
    'fotoPerfil'           => 'data:image/jpeg;base64,...'
  ]
================================================================
*/

/*
  Inicia sesión solo si no está iniciada.
  Usar require_once garantiza que este archivo se incluye
  una sola vez aunque múltiples archivos lo requieran.
*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
================================================================
FUNCIÓN: obtenerUsuarioSesion()
Propósito: Lee los datos del usuario desde la sesión PHP y
           los retorna en un array normalizado.

Retorna:
  Array con los datos del usuario si hay sesión activa.
  NULL si no hay sesión o los datos son inválidos.

Normalización de campos:
  Los campos de sesión vienen con nombres específicos desde
  sesionCargaSW.php (Nombre, Apellidos, etc. con mayúscula).
  Esta función los normaliza a minúsculas para consistencia
  en el resto del código PHP.
================================================================
*/

function obtenerUsuarioSesion(): ?array
{
    /*
      Verifica que exista la clave 'funcionario' en la sesión.
      sesionCargaSW.php guarda los datos bajo esta clave.
    */
    if (empty($_SESSION['funcionario'])) {
        return null;
    }

    $f = $_SESSION['funcionario'];

    /*
      Verifica que al menos el correo exista.
      Es el campo mínimo requerido para considerar la sesión válida.
    */
    if (empty($f['Correo']) && empty($f['correo'])) {
        return null;
    }

    /*
      Normaliza los datos al formato que espera header.php.
      Se soportan tanto mayúsculas (formato original de sesionCargaSW.php)
      como minúsculas (formato normalizado) para compatibilidad.
    */
    return [
        /*
          nombre y apellidos: se leen en mayúsculas (como vienen del WS)
          con fallback a minúsculas por compatibilidad.
        */
        'nombre'      => $f['Nombre']    ?? $f['nombre']    ?? '',
        'apellidos'   => $f['Apellidos'] ?? $f['apellidos'] ?? '',

        /*
          correo: identificador único del usuario en el sistema.
        */
        'correo'      => $f['Correo']    ?? $f['correo']    ?? '',

        /*
          dependencia: nombre del centro educativo o departamento.
          Viene del campo "Dependencia" de la sesión.
        */
        'dependencia' => $f['Dependencia'] ?? $f['dependencia'] ?? 'Dependencia no disponible',

        /*
          codigo_presupuestario: código del centro educativo activo.
        */
        'codigo_pres' => $f['Codigo_Presupuestario'] ?? $f['codigo_pres'] ?? '',

        /*
          fotoPerfil: base64 de la foto o ruta al avatar por defecto.

          CADENA DE FUENTES (en orden de prioridad):
          1. $_SESSION['funcionario']['fotoPerfil']
             ← guardado por fotoAzure.php durante el login
          2. Avatar SVG por defecto
             ← se usa si fotoAzure.php no guardó la foto todavía
             ← o si Graph API devolvió error durante el login

          NOTA: No se llama a Graph API aquí porque este archivo
          es PHP puro del lado del servidor. Graph requiere el
          token de acceso que vive en el lado del cliente (JS).
          La foto SIEMPRE debe guardarse en sesión durante el login
          via fotoAzure.php ANTES de que se llame a usuarioAzure.php.
        */
        'fotoPerfil'  => (isset($f['fotoPerfil']) && !empty($f['fotoPerfil']))
                          ? $f['fotoPerfil']
                          : 'assets/img/avatarH.svg',

        /*
          azure_id: Object ID del usuario en Azure AD.
          Puede no estar presente en sesiones antiguas.
        */
        'azure_id'    => $f['azure_id'] ?? $f['AzureId'] ?? '',
    ];
}

/*
================================================================
FUNCIÓN: sesionEsValida()
Propósito: Verifica rápidamente si hay una sesión activa válida
           sin retornar todos los datos.
           Útil para verificaciones de seguridad en controladores.

USO:
  if (!sesionEsValida()) {
      header('Location: /index.html');
      exit();
  }
================================================================

function sesionEsValida(): bool
{
    return !empty($_SESSION['funcionario'])
        && (!empty($_SESSION['funcionario']['Correo'])
            || !empty($_SESSION['funcionario']['correo']));
}*/
