<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obtener Token de Acceso - Microsoft Graph</title>
    <script type="text/javascript" src="https://alcdn.msauth.net/browser/2.21.0/js/msal-browser.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0078d4;
            border-bottom: 3px solid #0078d4;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            background-color: #f8f9fa;
        }
        .success {
            border-left: 5px solid #28a745;
            background-color: #d4edda;
        }
        .warning {
            border-left: 5px solid #ffc107;
            background-color: #fff3cd;
        }
        .info {
            border-left: 5px solid #17a2b8;
            background-color: #d1ecf1;
        }
        .token-box {
            background-color: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 14px;
            word-break: break-all;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
            margin: 10px 0;
        }
        button {
            background-color: #0078d4;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #106ebe;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
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
        .status {
            padding: 10px 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin: 2px;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .copy-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
        }
        .copy-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔑 Obtenedor de Token de Acceso - Microsoft Graph</h1>
        
        <div class="card info">
            <h3>⚙️ Configuración</h3>
            <p><strong>Client ID:</strong> <span id="clientId">be0e9b41-718d-4d2a-8c2f-d26eba67d767</span></p>
            <p><strong>Authority:</strong> https://login.microsoftonline.com/mep.go.cr</p>
            <p><strong>Scopes solicitados:</strong> 
                <span class="badge badge-info">User.Read</span>
                <span class="badge badge-info">OnlineMeetings.ReadWrite</span>
                <span class="badge badge-info">Calendars.ReadWrite</span>
                <span class="badge badge-info">openid</span>
                <span class="badge badge-info">profile</span>
            </p>
        </div>
        
        <div id="status" class="status">
            Presiona "Obtener Token" para comenzar
        </div>
        
        <div>
            <button id="loginBtn" onclick="loginAndGetToken()">🔑 Obtener Token</button>
            <button id="copyTokenBtn" onclick="copyToken()" disabled class="btn-secondary">📋 Copiar Token</button>
            <button id="logoutBtn" onclick="logout()" disabled class="btn-secondary">🚪 Cerrar Sesión</button>
            <button id="testGraphBtn" onclick="testGraphAPI()" disabled class="btn-success">🧪 Probar Graph API</button>
        </div>
        
        <div id="results" style="display: none;">
            <div class="card success">
                <h3>✅ Token de Acceso Obtenido</h3>
                <div class="token-box" id="tokenDisplay">Token aparecerá aquí...</div>
                <button class="copy-btn" onclick="copyToken()">Copiar Token</button>
                <button class="copy-btn" onclick="copyAsJSON()">Copiar como JSON</button>
            </div>
            
            <div class="card">
                <h3>📊 Información del Token</h3>
                <div id="tokenInfo">
                    <p><strong>Estado:</strong> <span id="tokenStatus">No disponible</span></p>
                    <p><strong>Tipo:</strong> <span id="tokenType">-</span></p>
                    <p><strong>Expira:</strong> <span id="tokenExpiry">-</span></p>
                    <p><strong>Usuario:</strong> <span id="tokenUser">-</span></p>
                    <p><strong>Audiencia:</strong> <span id="tokenAudience">-</span></p>
                </div>
            </div>
            
            <div class="card">
                <h3>🔍 Claims del Token (JWT decodificado)</h3>
                <div class="token-box" id="tokenClaims">Los claims aparecerán aquí...</div>
            </div>
            
            <div class="card" id="graphTestResult" style="display: none;">
                <h3>🧪 Prueba Graph API</h3>
                <div id="graphTestOutput"></div>
            </div>
        </div>
        
        <div class="card warning" style="margin-top: 30px;">
            <h3>📋 Instrucciones de Uso</h3>
            <ol>
                <li>Haz clic en "Obtener Token" para autenticarte con tu cuenta MEP</li>
                <li>El token se mostrará en la caja de arriba</li>
                <li>Puedes copiar el token con el botón "Copiar Token"</li>
                <li>Usa "Probar Graph API" para verificar que el token funciona</li>
                <li>Este token puede usarse para crear reuniones de Teams y eventos de calendario</li>
            </ol>
            <p><strong>Nota:</strong> El token expira en 1 hora. Necesitarás renovarlo después.</p>
        </div>
    </div>

    <script>
        // Configuración MSAL - AJUSTA ESTO SI ES NECESARIO
        const msalConfig = {
            auth: {
                clientId: "be0e9b41-718d-4d2a-8c2f-d26eba67d767",
                authority: "https://login.microsoftonline.com/mep.go.cr",
                redirectUri: window.location.origin + window.location.pathname,
                knownAuthorities: ["mep.go.cr"]
            },
            cache: {
                cacheLocation: "sessionStorage",
                storeAuthStateInCookie: false
            }
        };

        // Scopes requeridos para Microsoft Graph
        const graphScopes = {
            scopes: [
                "User.Read",
                "OnlineMeetings.ReadWrite",
                "Calendars.ReadWrite",
                "openid",
                "profile"
            ]
        };

        // Variables globales
        let msalInstance = null;
        let currentToken = null;
        let currentUser = null;

        // Inicializar MSAL
        function initializeMsal() {
            try {
                msalInstance = new msal.PublicClientApplication(msalConfig);
                updateStatus("MSAL inicializado correctamente", "info");
                return true;
            } catch (error) {
                updateStatus(`Error al inicializar MSAL: ${error.message}`, "error");
                return false;
            }
        }

        // Actualizar estado
        function updateStatus(message, type = "info") {
            const statusDiv = document.getElementById("status");
            let colorClass = "info";
            let icon = "ℹ️";
            
            if (type === "success") { colorClass = "success"; icon = "✅"; }
            if (type === "warning") { colorClass = "warning"; icon = "⚠️"; }
            if (type === "error") { colorClass = "error"; icon = "❌"; }
            
            statusDiv.innerHTML = `${icon} ${message}`;
            statusDiv.className = `status ${colorClass}`;
            console.log(`[STATUS] ${message}`);
        }

        // Iniciar sesión y obtener token
        async function loginAndGetToken() {
            try {
                updateStatus("Iniciando autenticación...", "warning");
                
                if (!msalInstance) {
                    if (!initializeMsal()) return;
                }

                // Verificar si ya hay sesión activa
                const accounts = msalInstance.getAllAccounts();
                if (accounts.length > 0) {
                    currentUser = accounts[0];
                    updateStatus(`Usuario ya autenticado: ${currentUser.username}`, "success");
                    await acquireTokenSilent();
                } else {
                    // Iniciar sesión interactiva
                    updateStatus("Redirigiendo a Microsoft Login...", "warning");
                    await msalInstance.loginPopup({
                        ...graphScopes,
                        prompt: "select_account"
                    });
                    
                    const accounts = msalInstance.getAllAccounts();
                    if (accounts.length > 0) {
                        currentUser = accounts[0];
                        updateStatus(`Autenticación exitosa: ${currentUser.username}`, "success");
                        await acquireTokenSilent();
                    }
                }
                
            } catch (error) {
                updateStatus(`Error en autenticación: ${error.message}`, "error");
                console.error("Error detallado:", error);
            }
        }

        // Obtener token silenciosamente
        async function acquireTokenSilent() {
            try {
                updateStatus("Obteniendo token de acceso...", "warning");
                
                const response = await msalInstance.acquireTokenSilent({
                    scopes: graphScopes.scopes,
                    account: currentUser,
                    forceRefresh: false
                });

                if (response && response.accessToken) {
                    currentToken = response.accessToken;
                    displayToken(response);
                    updateStatus("✅ Token obtenido exitosamente", "success");
                    
                    // Habilitar botones
                    document.getElementById("copyTokenBtn").disabled = false;
                    document.getElementById("logoutBtn").disabled = false;
                    document.getElementById("testGraphBtn").disabled = false;
                    document.getElementById("results").style.display = "block";
                }
                
            } catch (error) {
                console.warn("Error silencioso, intentando interactivo:", error);
                await acquireTokenPopup();
            }
        }

        // Obtener token via popup
        async function acquireTokenPopup() {
            try {
                updateStatus("Solicitando token interactivamente...", "warning");
                
                const response = await msalInstance.acquireTokenPopup({
                    scopes: graphScopes.scopes
                });

                if (response && response.accessToken) {
                    currentToken = response.accessToken;
                    displayToken(response);
                    updateStatus("✅ Token obtenido interactivamente", "success");
                    
                    // Habilitar botones
                    document.getElementById("copyTokenBtn").disabled = false;
                    document.getElementById("logoutBtn").disabled = false;
                    document.getElementById("testGraphBtn").disabled = false;
                    document.getElementById("results").style.display = "block";
                }
                
            } catch (error) {
                updateStatus(`Error al obtener token: ${error.message}`, "error");
            }
        }

        // Mostrar token en la UI
        function displayToken(tokenResponse) {
            // Mostrar token completo
            document.getElementById("tokenDisplay").textContent = tokenResponse.accessToken;
            
            // Mostrar información básica del token
            document.getElementById("tokenType").textContent = tokenResponse.tokenType || "Bearer";
            document.getElementById("tokenExpiry").textContent = tokenResponse.expiresOn ? 
                new Date(tokenResponse.expiresOn).toLocaleString() : "Desconocido";
            document.getElementById("tokenUser").textContent = tokenResponse.account?.username || "Desconocido";
            document.getElementById("tokenStatus").innerHTML = '<span style="color: green;">✅ Válido</span>';
            
            // Decodificar y mostrar claims del token
            try {
                const tokenParts = tokenResponse.accessToken.split('.');
                if (tokenParts.length === 3) {
                    const claims = JSON.parse(atob(tokenParts[1]));
                    
                    document.getElementById("tokenAudience").textContent = claims.aud || "Desconocida";
                    
                    // Formatear claims para mostrar
                    let claimsText = "";
                    for (const [key, value] of Object.entries(claims)) {
                        let formattedValue = value;
                        
                        // Formatear fechas
                        if (key === 'iat' || key === 'exp' || key === 'nbf') {
                            formattedValue = new Date(value * 1000).toLocaleString() + ` (${value})`;
                        }
                        
                        // Formatear arrays
                        if (Array.isArray(value)) {
                            formattedValue = value.join(', ');
                        }
                        
                        claimsText += `${key}: ${formattedValue}\n`;
                    }
                    
                    document.getElementById("tokenClaims").textContent = claimsText;
                }
            } catch (e) {
                console.warn("No se pudieron decodificar los claims:", e);
                document.getElementById("tokenClaims").textContent = "No se pudieron decodificar los claims del token";
            }
        }

        // Copiar token al portapapeles
        async function copyToken() {
            if (!currentToken) {
                updateStatus("No hay token para copiar", "warning");
                return;
            }
            
            try {
                await navigator.clipboard.writeText(currentToken);
                updateStatus("✅ Token copiado al portapapeles", "success");
                
                // Feedback visual
                const btn = document.getElementById("copyTokenBtn");
                const originalText = btn.textContent;
                btn.textContent = "✅ Copiado!";
                setTimeout(() => {
                    btn.textContent = originalText;
                }, 2000);
            } catch (err) {
                updateStatus("❌ Error al copiar: " + err.message, "error");
            }
        }

        // Copiar como JSON estructurado
        async function copyAsJSON() {
            if (!currentToken) return;
            
            try {
                const tokenObj = {
                    access_token: currentToken,
                    token_type: "Bearer",
                    expires_in: 3600,
                    scope: graphScopes.scopes.join(' '),
                    timestamp: new Date().toISOString(),
                    user: currentUser?.username || "Desconocido"
                };
                
                await navigator.clipboard.writeText(JSON.stringify(tokenObj, null, 2));
                updateStatus("✅ Token copiado como JSON", "success");
            } catch (err) {
                updateStatus("❌ Error al copiar JSON: " + err.message, "error");
            }
        }

        // Probar Graph API con el token
        async function testGraphAPI() {
            if (!currentToken) {
                updateStatus("No hay token para probar", "warning");
                return;
            }
            
            try {
                updateStatus("Probando conexión con Microsoft Graph...", "warning");
                
                const response = await fetch("https://graph.microsoft.com/v1.0/me", {
                    headers: {
                        "Authorization": `Bearer ${currentToken}`,
                        "Content-Type": "application/json"
                    }
                });
                
                const resultDiv = document.getElementById("graphTestOutput");
                const container = document.getElementById("graphTestResult");
                
                if (response.ok) {
                    const userData = await response.json();
                    resultDiv.innerHTML = `
                        <div class="success">
                            <strong>✅ Conexión exitosa con Microsoft Graph</strong><br><br>
                            <strong>Usuario:</strong> ${userData.displayName}<br>
                            <strong>Email:</strong> ${userData.mail || userData.userPrincipalName}<br>
                            <strong>ID:</strong> ${userData.id}<br>
                            <strong>Departamento:</strong> ${userData.department || "No especificado"}<br>
                            <strong>Cargo:</strong> ${userData.jobTitle || "No especificado"}
                        </div>
                    `;
                    updateStatus("✅ Graph API funciona correctamente", "success");
                } else {
                    const errorText = await response.text();
                    resultDiv.innerHTML = `
                        <div class="error">
                            <strong>❌ Error en Graph API (${response.status})</strong><br><br>
                            <strong>Detalles:</strong> ${errorText}
                        </div>
                    `;
                    updateStatus(`❌ Error en Graph API: ${response.status}`, "error");
                }
                
                container.style.display = "block";
                
            } catch (error) {
                updateStatus(`Error al probar Graph API: ${error.message}`, "error");
            }
        }

        // Cerrar sesión
        async function logout() {
            try {
                if (msalInstance) {
                    await msalInstance.logoutPopup();
                    updateStatus("Sesión cerrada exitosamente", "success");
                    
                    // Limpiar UI
                    currentToken = null;
                    currentUser = null;
                    
                    document.getElementById("copyTokenBtn").disabled = true;
                    document.getElementById("logoutBtn").disabled = true;
                    document.getElementById("testGraphBtn").disabled = true;
                    document.getElementById("results").style.display = "none";
                    document.getElementById("graphTestResult").style.display = "none";
                    
                    // Resetear campos
                    document.getElementById("tokenDisplay").textContent = "Token aparecerá aquí...";
                    document.getElementById("tokenClaims").textContent = "Los claims aparecerán aquí...";
                    document.getElementById("graphTestOutput").innerHTML = "";
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
                const accounts = msalInstance?.getAllAccounts();
                if (accounts && accounts.length > 0) {
                    currentUser = accounts[0];
                    updateStatus(`Sesión activa detectada: ${currentUser.username}`, "success");
                    acquireTokenSilent();
                }
            }, 500);
        });
    </script>
</body>
</html>