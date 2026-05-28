<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Permisos MSAL.js</title>
    <script type="text/javascript" src="https://alcdn.msauth.net/browser/2.21.0/js/msal-browser.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0078d4;
            border-bottom: 2px solid #0078d4;
            padding-bottom: 10px;
        }
        .status-box {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            font-family: monospace;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        button {
            background-color: #0078d4;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        button:hover {
            background-color: #106ebe;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .permission-list {
            list-style-type: none;
            padding: 0;
        }
        .permission-item {
            padding: 8px;
            margin: 5px 0;
            background-color: #f8f9fa;
            border-left: 4px solid #0078d4;
        }
        .token-details {
            word-break: break-all;
            white-space: pre-wrap;
            max-height: 200px;
            overflow-y: auto;
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0078d4;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificación de Permisos MSAL.js & Microsoft Graph</h1>
        
        <div id="status" class="status-box info">
            Presiona "Iniciar Verificación" para comenzar
        </div>
        
        <h2>📋 Permisos Requeridos:</h2>
        <ul class="permission-list">
            <li class="permission-item">✅ User.Read (lectura de perfil de usuario)</li>
            <li class="permission-item">✅ OnlineMeetings.ReadWrite (crear/leer reuniones de Teams)</li>
            <li class="permission-item">✅ Calendars.ReadWrite (lectura/escritura de calendario)</li>
        </ul>
        
        <h2>⚙️ Configuración:</h2>
        <div id="config" class="status-box info">
            Client ID: be0e9b41-718d-4d2a-8c2f-d26eba67d767<br>
            Authority: https://login.microsoftonline.com/mep.go.cr<br>
            Redirect URI: https://tecnopresta.mep.go.cr/index.html
        </div>
        
        <div>
            <button id="loginBtn" onclick="loginAndGetToken()">Iniciar Verificación</button>
            <button id="logoutBtn" onclick="logout()" disabled>Cerrar Sesión</button>
            <button id="checkTokenBtn" onclick="checkExistingToken()" disabled>Verificar Token Existente</button>
        </div>
        
        <div id="results" style="display: none;">
            <h2>📊 Resultados:</h2>
            <div id="tokenResult" class="status-box"></div>
            <div id="errorDetails" class="status-box"></div>
            
            <h3>🔑 Detalles del Token:</h3>
            <div id="tokenDetails" class="status-box token-details"></div>
            
            <h3>👤 Información del Usuario:</h3>
            <div id="userInfo" class="status-box"></div>
            
            <h3>🔍 Permisos en Token:</h3>
            <div id="scopesInfo" class="status-box"></div>
        </div>
    </div>

    <script>
        // Configuración MSAL
        const msalConfig = {
            auth: {
                clientId: "be0e9b41-718d-4d2a-8c2f-d26eba67d767",
                authority: "https://login.microsoftonline.com/mep.go.cr",
                redirectUri: "https://tecnopresta.mep.go.cr/index.html",
                knownAuthorities: ["mep.go.cr"]
            },
            cache: {
                cacheLocation: "sessionStorage",
                storeAuthStateInCookie: false
            },
            system: {
                loggerOptions: {
                    loggerCallback: (level, message, containsPii) => {
                        if (!containsPii) {
                            console.log(`[MSAL] ${message}`);
                        }
                    },
                    logLevel: msal.LogLevel.Verbose
                }
            }
        };

        // Scopes requeridos
        const requiredScopes = {
            scopes: [
                "User.Read",
                "OnlineMeetings.ReadWrite",
                "Calendars.ReadWrite",
                "openid",
                "profile"
            ],
            extraScopesToConsent: ["OnlineMeetings.ReadWrite", "Calendars.ReadWrite"]
        };

        // Variables globales
        let msalInstance = null;
        let account = null;

        // Inicializar MSAL
        function initializeMsal() {
            try {
                msalInstance = new msal.PublicClientApplication(msalConfig);
                updateStatus("MSAL inicializado correctamente", "success");
                return true;
            } catch (error) {
                updateStatus(`Error al inicializar MSAL: ${error.message}`, "error");
                return false;
            }
        }

        // Actualizar estado en la UI
        function updateStatus(message, type = "info") {
            const statusDiv = document.getElementById("status");
            statusDiv.textContent = message;
            statusDiv.className = `status-box ${type}`;
            console.log(`[STATUS] ${type}: ${message}`);
        }

        // Mostrar error detallado
        function showErrorDetails(error) {
            const errorDiv = document.getElementById("errorDetails");
            let errorHtml = `<strong>Error:</strong> ${error.message}<br><br>`;
            
            if (error.errorCode) {
                errorHtml += `<strong>Código de Error:</strong> ${error.errorCode}<br>`;
            }
            
            if (error.subError) {
                errorHtml += `<strong>Sub-error:</strong> ${error.subError}<br>`;
            }
            
            if (error.correlationId) {
                errorHtml += `<strong>Correlation ID:</strong> ${error.correlationId}<br>`;
            }
            
            // Diagnóstico basado en códigos de error comunes
            if (error.errorCode === "interaction_required") {
                errorHtml += `<br><strong>Diagnóstico:</strong> Se requiere interacción del usuario (consentimiento o re-autenticación)<br>`;
            } else if (error.errorCode === "consent_required") {
                errorHtml += `<br><strong>Diagnóstico:</strong> Se requiere consentimiento para los permisos<br>`;
            } else if (error.errorCode === "invalid_grant") {
                errorHtml += `<br><strong>Diagnóstico:</strong> Token inválido o expirado. Intente cerrar sesión y volver a iniciar<br>`;
            } else if (error.errorCode === "unauthorized_client") {
                errorHtml += `<br><strong>Diagnóstico:</strong> Cliente no autorizado. Verifique la configuración de la aplicación en Azure AD<br>`;
            }
            
            errorDiv.innerHTML = errorHtml;
            errorDiv.className = "status-box error";
            errorDiv.style.display = "block";
        }

        // Iniciar sesión y obtener token
        async function loginAndGetToken() {
            try {
                updateStatus("Iniciando proceso de autenticación...", "warning");
                
                if (!msalInstance) {
                    if (!initializeMsal()) return;
                }

                // Intentar obtener cuenta existente
                const accounts = msalInstance.getAllAccounts();
                if (accounts.length > 0) {
                    account = accounts[0];
                    updateStatus(`Usuario ya autenticado: ${account.username}`, "success");
                    await getTokenSilently();
                } else {
                    // Iniciar sesión interactiva
                    await msalInstance.loginPopup({
                        ...requiredScopes,
                        prompt: "select_account"
                    });
                    
                    const accounts = msalInstance.getAllAccounts();
                    if (accounts.length > 0) {
                        account = accounts[0];
                        updateStatus(`Autenticación exitosa: ${account.username}`, "success");
                        await getTokenSilently();
                    }
                }
                
                document.getElementById("logoutBtn").disabled = false;
                document.getElementById("checkTokenBtn").disabled = false;
                
            } catch (error) {
                updateStatus(`Error en autenticación: ${error.message}`, "error");
                showErrorDetails(error);
            }
        }

        // Obtener token silenciosamente
        async function getTokenSilently() {
            try {
                updateStatus("Obteniendo token de acceso...", "warning");
                
                if (!account) {
                    throw new Error("No hay usuario autenticado");
                }

                // Solicitar token con todos los scopes requeridos
                const response = await msalInstance.acquireTokenSilent({
                    scopes: requiredScopes.scopes,
                    account: account,
                    forceRefresh: false
                });

                if (response && response.accessToken) {
                    displayTokenDetails(response);
                    await verifyPermissions(response);
                    await getUserInfo(response.accessToken);
                    
                    updateStatus("✅ Token obtenido exitosamente. Permisos verificados.", "success");
                    document.getElementById("results").style.display = "block";
                }
                
            } catch (error) {
                console.warn("Error en adquisición silenciosa:", error);
                
                if (error instanceof msal.InteractionRequiredAuthError) {
                    updateStatus("Se requiere interacción. Intentando obtener token interactivamente...", "warning");
                    await getTokenPopup();
                } else {
                    updateStatus(`Error al obtener token: ${error.message}`, "error");
                    showErrorDetails(error);
                }
            }
        }

        // Obtener token via popup
        async function getTokenPopup() {
            try {
                const response = await msalInstance.acquireTokenPopup({
                    scopes: requiredScopes.scopes,
                    prompt: "select_account"
                });
                
                if (response && response.accessToken) {
                    displayTokenDetails(response);
                    await verifyPermissions(response);
                    await getUserInfo(response.accessToken);
                    
                    updateStatus("✅ Token obtenido interactivamente. Permisos verificados.", "success");
                    document.getElementById("results").style.display = "block";
                }
                
            } catch (error) {
                updateStatus(`Error en obtención interactiva: ${error.message}`, "error");
                showErrorDetails(error);
            }
        }

        // Verificar permisos en el token
        async function verifyPermissions(tokenResponse) {
            const scopesDiv = document.getElementById("scopesInfo");
            const tokenScopes = tokenResponse.scopes || [];
            
            let html = `<strong>Scopes en el token:</strong><br><ul>`;
            
            requiredScopes.scopes.forEach(scope => {
                const hasScope = tokenScopes.some(s => s.toLowerCase().includes(scope.toLowerCase()));
                const icon = hasScope ? "✅" : "❌";
                html += `<li>${icon} ${scope}</li>`;
            });
            
            html += `</ul>`;
            
            // Verificar scopes faltantes
            const missingScopes = requiredScopes.scopes.filter(scope => 
                !tokenScopes.some(s => s.toLowerCase().includes(scope.toLowerCase()))
            );
            
            if (missingScopes.length > 0) {
                html += `<br><strong style="color: #dc3545;">Scopes faltantes:</strong><br>`;
                html += missingScopes.join(", ");
                html += `<br><br><strong>Posibles soluciones:</strong><br>`;
                html += `1. Verificar que la aplicación tenga los permisos configurados en Azure AD<br>`;
                html += `2. Solicitar consentimiento del administrador<br>`;
                html += `3. Verificar que el usuario tenga los permisos necesarios<br>`;
            }
            
            scopesDiv.innerHTML = html;
            scopesDiv.className = missingScopes.length > 0 ? "status-box warning" : "status-box success";
        }

        // Mostrar detalles del token
        function displayTokenDetails(tokenResponse) {
            const tokenDiv = document.getElementById("tokenResult");
            const detailsDiv = document.getElementById("tokenDetails");
            
            // Decodificar JWT para ver claims
            const token = tokenResponse.accessToken;
            const tokenParts = token.split('.');
            let decodedClaims = {};
            
            if (tokenParts.length === 3) {
                try {
                    const claims = JSON.parse(atob(tokenParts[1]));
                    decodedClaims = claims;
                } catch (e) {
                    console.warn("No se pudo decodificar el JWT:", e);
                }
            }
            
            // Mostrar resultado básico
            tokenDiv.innerHTML = `
                <strong>Token obtenido:</strong> ✅<br>
                <strong>Tipo:</strong> ${tokenResponse.tokenType}<br>
                <strong>Expira:</strong> ${new Date(tokenResponse.expiresOn).toLocaleString()}<br>
                <strong>ID único:</strong> ${tokenResponse.account.homeAccountId}
            `;
            tokenDiv.className = "status-box success";
            
            // Mostrar detalles técnicos
            let details = `<strong>Claims del token:</strong>\n`;
            details += `• aud (audiencia): ${decodedClaims.aud || 'N/A'}\n`;
            details += `• iss (emisor): ${decodedClaims.iss || 'N/A'}\n`;
            details += `• iat (emitido en): ${new Date(decodedClaims.iat * 1000).toLocaleString() || 'N/A'}\n`;
            details += `• exp (expira en): ${new Date(decodedClaims.exp * 1000).toLocaleString() || 'N/A'}\n`;
            details += `• name: ${decodedClaims.name || 'N/A'}\n`;
            details += `• oid (object ID): ${decodedClaims.oid || 'N/A'}\n`;
            details += `• preferred_username: ${decodedClaims.preferred_username || 'N/A'}\n`;
            details += `• scp (scopes): ${decodedClaims.scp || 'N/A'}\n`;
            details += `• roles: ${decodedClaims.roles ? decodedClaims.roles.join(', ') : 'N/A'}\n\n`;
            
            details += `<strong>Token (primeros 100 chars):</strong>\n`;
            details += token.substring(0, 100) + "...";
            
            detailsDiv.textContent = details;
            detailsDiv.className = "status-box info";
        }

        // Obtener información del usuario desde Graph
        async function getUserInfo(accessToken) {
            try {
                updateStatus("Obteniendo información del usuario...", "warning");
                
                const response = await fetch("https://graph.microsoft.com/v1.0/me", {
                    headers: {
                        "Authorization": `Bearer ${accessToken}`,
                        "Content-Type": "application/json"
                    }
                });
                
                if (response.ok) {
                    const userData = await response.json();
                    const userDiv = document.getElementById("userInfo");
                    
                    userDiv.innerHTML = `
                        <strong>Nombre:</strong> ${userData.displayName}<br>
                        <strong>Email:</strong> ${userData.mail || userData.userPrincipalName}<br>
                        <strong>ID:</strong> ${userData.id}<br>
                        <strong>Prueba Graph API:</strong> ✅ Exitosa
                    `;
                    userDiv.className = "status-box success";
                } else {
                    throw new Error(`Graph API error: ${response.status}`);
                }
                
            } catch (error) {
                const userDiv = document.getElementById("userInfo");
                userDiv.innerHTML = `<strong>Error al obtener info de usuario:</strong> ${error.message}`;
                userDiv.className = "status-box error";
            }
        }

        // Verificar token existente
        async function checkExistingToken() {
            try {
                updateStatus("Verificando token existente...", "warning");
                
                if (!msalInstance) {
                    if (!initializeMsal()) return;
                }
                
                const accounts = msalInstance.getAllAccounts();
                if (accounts.length === 0) {
                    updateStatus("No hay sesiones activas. Inicie sesión primero.", "warning");
                    return;
                }
                
                account = accounts[0];
                updateStatus(`Usuario encontrado: ${account.username}`, "success");
                await getTokenSilently();
                
            } catch (error) {
                updateStatus(`Error al verificar token: ${error.message}`, "error");
                showErrorDetails(error);
            }
        }

        // Cerrar sesión
        async function logout() {
            try {
                if (msalInstance) {
                    await msalInstance.logoutPopup();
                    updateStatus("Sesión cerrada exitosamente", "success");
                    
                    // Resetear UI
                    document.getElementById("logoutBtn").disabled = true;
                    document.getElementById("checkTokenBtn").disabled = true;
                    document.getElementById("results").style.display = "none";
                    document.getElementById("userInfo").innerHTML = "";
                    document.getElementById("scopesInfo").innerHTML = "";
                    document.getElementById("tokenDetails").textContent = "";
                    document.getElementById("errorDetails").style.display = "none";
                    
                    account = null;
                }
            } catch (error) {
                updateStatus(`Error al cerrar sesión: ${error.message}`, "error");
            }
        }

        // Inicializar al cargar la página
        document.addEventListener("DOMContentLoaded", function() {
            initializeMsal();
            
            // Verificar si ya hay una sesión activa
            setTimeout(() => {
                checkExistingToken();
            }, 1000);
        });
    </script>
</body>
</html>