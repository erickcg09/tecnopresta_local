<?php
// Iniciar la sesión
session_start();

// Destruir todas las variables de sesión
$_SESSION = [];

// Si se desea destruir la sesión completamente, también se debe destruir la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cierre de sesión</title>
    <script>
        // Eliminar cookies
        document.cookie.split(";").forEach(function(c) {
            document.cookie = c.trim().split("=")[0] + '=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/';
        });

        // Eliminar localStorage
        localStorage.clear();

        // Eliminar sessionStorage
        sessionStorage.clear();

        // Redirigir a la URL de cierre de sesión de Azure AD
        window.location.href = 'https://login.microsoftonline.com/common/oauth2/logout?post_logout_redirect_uri=https://tuaplicacion.com/logout';
    </script>
</head>
<body>
    <h1>Cerrando sesión...</h1>
</body>
</html>
