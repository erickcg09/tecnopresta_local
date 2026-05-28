<?php
// Iniciar la sesión
session_start();
session_destroy();
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

// URL de tu aplicación (ajusta según tu entorno)
$appBaseUrl = "https://tecnopresta.mep.go.cr/";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de sesión</title>
    <script>
        // Función para limpieza completa
        function limpiezaCompleta() {
            console.log('Iniciando limpieza completa de sesiones...');
            
            // 1. Limpiar todas las cookies de forma más agresiva
            const dominios = ['', '.https://tecnopresta.mep.go.cr/', 'https://tecnopresta.mep.go.cr/'];
            const cookies = document.cookie.split(";");
            
            cookies.forEach(cookie => {
                const nombre = cookie.split("=")[0].trim();
                dominios.forEach(dominio => {
                    document.cookie = `${nombre}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=${dominio};`;
                    document.cookie = `${nombre}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
                });
            });

            // 2. Limpiar localStorage específico de MSAL y la aplicación
            const clavesMSAL = [
                'msal.idtoken',
                'msal.access.token.key',
                'msal.refresh.token.key',
                'msal.client.info',
                'msal.error.description',
                'msal.error'
            ];
            
            // Limpiar claves específicas de MSAL
            clavesMSAL.forEach(clave => {
                localStorage.removeItem(clave);
                sessionStorage.removeItem(clave);
            });
            
            // 3. Limpiar todo el localStorage y sessionStorage (nuclear)
            localStorage.clear();
            sessionStorage.clear();
            
            // 4. Limpiar IndexedDB (si MSAL lo está usando)
            limpiarIndexedDB();
            
            // 5. Forzar cierre de sesión en Azure AD
            cerrarSesionAzureAD();
        }

        // Función para limpiar IndexedDB (donde MSAL a veces almacena datos)
        function limpiarIndexedDB() {
            if (window.indexedDB) {
                indexedDB.databases().then(databases => {
                    databases.forEach(db => {
                        if (db.name && db.name.includes('msal')) {
                            indexedDB.deleteDatabase(db.name);
                        }
                    });
                }).catch(console.warn);
            }
        }

        // Función para cerrar sesión en Azure AD
        function cerrarSesionAzureAD() {
            const azureLogoutUrl = 'https://login.microsoftonline.com/common/oauth2/logout';
            const postLogoutRedirectUri = encodeURIComponent('<?php echo $appBaseUrl; ?>');
            
            // URL completa de logout de Azure
            const logoutUrl = `${azureLogoutUrl}?post_logout_redirect_uri=${postLogoutRedirectUri}`;
            
            console.log('Redirigiendo a Azure AD logout...');
            
            // Redirigir después de un pequeño delay para asegurar la limpieza local
            setTimeout(() => {
                window.location.href = logoutUrl;
            }, 500);
        }

        // Función de verificación para desarrollo
        function verificarLimpieza() {
            console.log('Verificando limpieza:');
            console.log('- Cookies:', document.cookie);
            console.log('- localStorage length:', localStorage.length);
            console.log('- sessionStorage length:', sessionStorage.length);
            
            const tieneMSAL = Array.from({length: localStorage.length}, (_, i) => 
                localStorage.key(i)
            ).some(key => key && key.includes('msal'));
            
            console.log('- ¿Quedan claves MSAL?:', tieneMSAL);
        }

        // Ejecutar cuando la página cargue
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== INICIANDO PROCESO DE LOGOUT COMPLETO ===');
            
            // Ejecutar limpieza
            limpiezaCompleta();
            
            // Verificación (solo en desarrollo)
            setTimeout(verificarLimpieza, 100);
            
            // Redirección de emergencia si Azure no responde
            setTimeout(() => {
                if (!window.location.href.includes('login.microsoftonline.com')) {
                    console.warn('Azure AD no respondió, redirigiendo directamente...');
                    window.location.href = '<?php echo $appBaseUrl; ?>/login';
                }
            }, 5000);
        });
    </script>
</head>
<body>
    <div style="text-align: center; margin-top: 50px;">
        <h1>Cerrando sesión de forma segura...</h1>
        <p>Estamos limpiando todas tus sesiones activas.</p>
        <div style="margin-top: 20px;">
            <div style="display: inline-block; width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        </div>
        <p style="margin-top: 20px; font-size: 14px; color: #666;">
            Serás redirigido automáticamente en unos segundos.
        </p>
    </div>

    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</body>
</html>
