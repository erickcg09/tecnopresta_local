<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificador de Token para Microsoft Teams</title>
    <script type="text/javascript" src="https://alcdn.msauth.net/browser/2.21.0/js/msal-browser.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4646ff 0%, #7b2cbf 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 900px;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(90deg, #4646ff 0%, #7b2cbf 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .teams-badge {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 14px;
            display: inline-block;
            margin-top: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #4646ff;
        }
        
        .section h3 {
            color: #4646ff;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .scope-list {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }
        
        .scope-item {
            padding: 10px 15px;
            margin: 8px 0;
            background: white;
            border-radius: 5px;
            border-left: 4px solid #4646ff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .scope-item.teams {
            border-left-color: #7b2cbf;
            background: #f3e8ff;
        }
        
        .scope-status {
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
        }
        
        .status-granted {
            background: #d4edda;
            color: #155724;
        }
        
        .status-missing {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .btn {
            background: #4646ff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 5px;
        }
        
        .btn:hover {
            background: #3a3ae0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(70, 70, 255, 0.4);
        }
        
        .btn-teams {
            background: #7b2cbf;
        }
        
        .btn-teams:hover {
            background: #6a27a8;
            box-shadow: 0 5px 15px rgba(123, 44, 191, 0.4);
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 25px 0;
            gap: 10px;
        }
        
        .token-details {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            border: 1px solid #ddd;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .test-results {
            margin-top: 20px;
        }
        
        .test-item {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            background: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .test-icon {
            font-size: 20px;
        }
        
        .loader {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(70, 70, 255, 0.2);
            border-top: 3px solid #4646ff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 600px) {
            .content {
                padding: 15px;
            }
            
            .btn-container {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Verificador de Token para Microsoft Teams</h1>
            <p>Verifica tokens de acceso para operaciones en Microsoft Teams</p>
            <div class="teams-badge">Microsoft Graph API + Teams Integration</div>
        </div>
        
        <div class="content">
            <div class="section">
                <h3>🔧 Configuración del Cliente</h3>
                <p><strong>Client ID:</strong> be0e9b41-718d-4d2a-8c2f-d26eba67d767</p>
                <p><strong>Tenant:</strong> mep.go.cr (Azure AD)</p>
                <p><strong>API:</strong> Microsoft Graph</p>
            </div>
            
            <div class="section">
                <h3>📋 Permisos Requeridos para Teams</h3>
                <ul class="scope-list">
                    <li class="scope-item teams">
                        <span>🔄 OnlineMeetings.ReadWrite</span>
                        <span class="scope-status status-pending" id="scope-teams">Pendiente</span>
                    </li>
                    <li class="scope-item">
                        <span>👤 User.Read</span>
                        <span class="scope-status status-pending" id="scope-user">Pendiente</span>
                    </li>
                    <li class="scope-item">
                        <span>📅 Calendars.ReadWrite</span>
                        <span class="scope-status status-pending" id="scope-calendar">Pendiente</span>
                    </li>
                </ul>
            </div>
            
            <div class="btn-container">
                <button id="checkBtn" class="btn" onclick="checkTeamsToken()">
                    <span id="checkIcon">🔍</span> Verificar Token Teams
                </button>
                <button id="acquireBtn" class="btn btn-teams" onclick="acquireTeamsToken()">
                    <span id="acquireIcon">🔑</span> Obtener Token Teams
                </button>
                <button id="testBtn" class="btn btn-success" onclick="testTeamsAccess()" disabled>
                    <span id="testIcon">🧪</span> Probar Acceso a Teams
                </button>
                <button id="clearBtn" class="btn btn-danger" onclick="clearToken()">
                    <span id="clearIcon">🗑️</span> Limpiar Token
                </button>
            </div>
            
            <div class="section">
                <h3>📊 Estado del Token</h3>
                <div id="tokenStatus">
                    <p>Estado: <strong>No verificado</strong></p>
                    <p>Usuario: <strong>No identificado</strong></p>
                    <p>Expiración: <strong>N/A</strong></p>
                </div>
                
                <div id="teamsAccess" style="display: none; margin-top: 15px; padding: 15px; background: #e7f3ff; border-radius: 5px;">
                    <h4 style="color: #004085; margin-bottom: 10px;">✅ Acceso a Teams Disponible</h4>
                    <p id="accessMessage">El token tiene permisos para operaciones de Teams</p>
                </div>
            </div>
            
            <div class="section" id="testResultsSection" style="display: none;">
                <h3>🧪 Resultados de Prueba</h3>
                <div id="testResults" class="test-results">
                    <!-- Los resultados de las pruebas se mostrarán aquí -->
                </div>
            </div>
            
            <div class="section">
                <h3>🔍 Detalles del Token</h3>
                <div id="tokenDetails" class="token-details">
                    No hay token disponible
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuración específica para Teams vía Microsoft Graph
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
            }
        };

        // Scopes específicos para Teams
        const teamsScopes = {
            scopes: [
                "User.Read",
                "OnlineMeetings.ReadWrite",
                "Calendars.ReadWrite",
                "openid",
                "profile"
            ]
        };

        let msalInstance = null;
        let currentToken = null;

        // Inicializar MSAL
        function initializeMsal() {
            if (!msalInstance) {
                msalInstance = new msal.PublicClientApplication(msalConfig);
                console.log("MSAL inicializado para Teams");
            }
            return msalInstance;
        }

        // Verificar token específico para Teams
        async function checkTeamsToken() {
            try {
                showLoading('checkBtn', 'checkIcon');
                updateStatus("Verificando token de Teams...", "info");
                
                const msalApp = initializeMsal();
                const accounts = msalApp.getAllAccounts();
                
                if (accounts.length === 0) {
                    updateStatus("No hay usuario autenticado", "error");
                    updateScopeStatus('teams', 'missing');
                    updateScopeStatus('user', 'missing');
                    updateScopeStatus('calendar', 'missing');
                    return;
                }
                
                const account = accounts[0];
                updateStatus(`Usuario encontrado: ${account.username}`, "success");
                
                // Intentar obtener token silenciosamente
                try {
                    const tokenResponse = await msalApp.acquireTokenSilent({
                        ...teamsScopes,
                        account: account,
                        forceRefresh: false
                    });
                    
                    currentToken = tokenResponse;
                    displayTokenDetails(tokenResponse);
                    verifyTeamsScopes(tokenResponse);
                    
                    // Habilitar botón de prueba
                    document.getElementById('testBtn').disabled = false;
                    
                } catch (silentError) {
                    // Token expirado, intentar renovar
                    updateStatus("Token expirado, intentando renovar...", "warning");
                    
                    try {
                        const renewedToken = await msalApp.acquireTokenSilent({
                            ...teamsScopes,
                            account: account,
                            forceRefresh: true
                        });
                        
                        currentToken = renewedToken;
                        displayTokenDetails(renewedToken);
                        verifyTeamsScopes(renewedToken);
                        document.getElementById('testBtn').disabled = false;
                        
                    } catch (refreshError) {
                        updateStatus("Se requiere login interactivo", "warning");
                        updateScopeStatus('teams', 'missing');
                        updateScopeStatus('user', 'missing');
                        updateScopeStatus('calendar', 'missing');
                    }
                }
                
            } catch (error) {
                updateStatus(`Error: ${error.message}`, "error");
            } finally {
                hideLoading('checkBtn', '🔍');
            }
        }

        // Obtener nuevo token para Teams
        async function acquireTeamsToken() {
            try {
                showLoading('acquireBtn', 'acquireIcon');
                updateStatus("Solicitando token para Teams...", "info");
                
                const msalApp = initializeMsal();
                
                // Login interactivo
                const loginResponse = await msalApp.loginPopup({
                    ...teamsScopes,
                    prompt: "select_account"
                });
                
                if (loginResponse.account) {
                    // Obtener token de acceso
                    const tokenResponse = await msalApp.acquireTokenPopup(teamsScopes);
                    currentToken = tokenResponse;
                    
                    displayTokenDetails(tokenResponse);
                    verifyTeamsScopes(tokenResponse);
                    document.getElementById('testBtn').disabled = false;
                    
                    updateStatus(`Token obtenido para: ${loginResponse.account.username}`, "success");
                }
                
            } catch (error) {
                updateStatus(`Error al obtener token: ${error.message}`, "error");
            } finally {
                hideLoading('acquireBtn', '🔑');
            }
        }

        // Verificar scopes específicos de Teams
        function verifyTeamsScopes(tokenResponse) {
            const tokenScopes = tokenResponse.scopes || [];
            
            // Verificar OnlineMeetings.ReadWrite (Teams meetings)
            const hasTeamsScope = tokenScopes.some(s => 
                s.toLowerCase().includes('onlinemeetings.readwrite')
            );
            updateScopeStatus('teams', hasTeamsScope ? 'granted' : 'missing');
            
            // Verificar User.Read
            const hasUserScope = tokenScopes.some(s => 
                s.toLowerCase().includes('user.read')
            );
            updateScopeStatus('user', hasUserScope ? 'granted' : 'missing');
            
            // Verificar Calendars.ReadWrite
            const hasCalendarScope = tokenScopes.some(s => 
                s.toLowerCase().includes('calendars.readwrite')
            );
            updateScopeStatus('calendar', hasCalendarScope ? 'granted' : 'missing');
            
            // Mostrar panel de acceso si tiene permisos de Teams
            if (hasTeamsScope) {
                document.getElementById('teamsAccess').style.display = 'block';
                document.getElementById('accessMessage').textContent = 
                    'Permisos de Teams confirmados. Puede crear/leer reuniones de Teams.';
            }
        }

        // Actualizar estado de scope
        function updateScopeStatus(scope, status) {
            const element = document.getElementById(`scope-${scope}`);
            element.textContent = status === 'granted' ? 'Concedido' : 
                                 status === 'missing' ? 'Faltante' : 'Pendiente';
            element.className = `scope-status status-${status}`;
        }

        // Probar acceso real a Teams API
        async function testTeamsAccess() {
            if (!currentToken) {
                updateStatus("No hay token disponible para probar", "error");
                return;
            }
            
            try {
                showLoading('testBtn', 'testIcon');
                document.getElementById('testResultsSection').style.display = 'block';
                const resultsDiv = document.getElementById('testResults');
                resultsDiv.innerHTML = '';
                
                // Test 1: Verificar usuario
                resultsDiv.innerHTML += createTestItem("Verificando usuario...", "loading");
                const userTest = await testGraphAPI('https://graph.microsoft.com/v1.0/me');
                resultsDiv.innerHTML = resultsDiv.innerHTML.replace(
                    createTestItem("Verificando usuario...", "loading"),
                    createTestItem(`Usuario: ${userTest.displayName || userTest.userPrincipalName}`, 
                                 userTest.success ? "success" : "error")
                );
                
                // Test 2: Verificar capacidad para Teams (permissions)
                resultsDiv.innerHTML += createTestItem("Verificando permisos de Teams...", "loading");
                const teamsTest = await testGraphAPI('https://graph.microsoft.com/v1.0/me/onenote/notebooks', false);
                resultsDiv.innerHTML = resultsDiv.innerHTML.replace(
                    createTestItem("Verificando permisos de Teams...", "loading"),
                    createTestItem("Permisos de Teams verificados", 
                                 teamsTest.success || teamsTest.error?.code === 'AccessDenied' ? "success" : "error")
                );
                
                // Test 3: Intentar crear una reunión de prueba
                resultsDiv.innerHTML += createTestItem("Probando API de reuniones de Teams...", "loading");
                const meetingsTest = await testGraphAPI('https://graph.microsoft.com/v1.0/me/onlineMeetings', true);
                resultsDiv.innerHTML = resultsDiv.innerHTML.replace(
                    createTestItem("Probando API de reuniones de Teams...", "loading"),
                    createTestItem(`API de reuniones: ${meetingsTest.success ? 'Accesible' : 'No accesible'}`, 
                                 meetingsTest.success ? "success" : "warning")
                );
                
                updateStatus("Pruebas completadas", "success");
                
            } catch (error) {
                updateStatus(`Error en pruebas: ${error.message}`, "error");
            } finally {
                hideLoading('testBtn', '🧪');
            }
        }

        // Función auxiliar para probar API
        async function testGraphAPI(endpoint, isPost = false) {
            try {
                const options = {
                    headers: {
                        'Authorization': `Bearer ${currentToken.accessToken}`,
                        'Content-Type': 'application/json'
                    }
                };
                
                let response;
                if (isPost) {
                    // Para reuniones, hacer un GET primero para verificar acceso
                    options.method = 'GET';
                    response = await fetch(endpoint, options);
                    
                    if (response.status === 403 || response.status === 401) {
                        return { success: false, error: await response.json() };
                    }
                } else {
                    response = await fetch(endpoint, options);
                }
                
                if (response.ok) {
                    const data = await response.json();
                    return { success: true, ...data };
                } else {
                    return { 
                        success: false, 
                        error: { 
                            code: response.status,
                            message: response.statusText
                        }
                    };
                }
            } catch (error) {
                return { success: false, error: { message: error.message } };
            }
        }

        // Mostrar detalles del token
        function displayTokenDetails(tokenResponse) {
            const detailsDiv = document.getElementById('tokenDetails');
            const statusDiv = document.getElementById('tokenStatus');
            
            // Decodificar JWT
            try {
                const tokenParts = tokenResponse.accessToken.split('.');
                const claims = JSON.parse(atob(tokenParts[1]));
                
                detailsDiv.innerHTML = `
                    <strong>Token Type:</strong> ${tokenResponse.tokenType}<br>
                    <strong>Expira:</strong> ${new Date(tokenResponse.expiresOn).toLocaleString()}<br>
                    <strong>Scopes:</strong> ${tokenResponse.scopes.join(', ')}<br>
                    <strong>User ID:</strong> ${claims.oid || claims.sub}<br>
                    <strong>Issuer:</strong> ${claims.iss}<br>
                `;
                
                statusDiv.innerHTML = `
                    <p>Estado: <strong style="color: green;">✅ Válido</strong></p>
                    <p>Usuario: <strong>${tokenResponse.account.username}</strong></p>
                    <p>Expiración: <strong>${new Date(tokenResponse.expiresOn).toLocaleTimeString()}</strong></p>
                `;
                
            } catch (e) {
                detailsDiv.textContent = `Token válido pero no decodificable: ${e.message}`;
            }
        }

        // Limpiar token
        function clearToken() {
            sessionStorage.clear();
            localStorage.removeItem('msal.' + msalConfig.auth.clientId + '.idtoken');
            msalInstance = null;
            currentToken = null;
            
            updateStatus("Token limpiado", "info");
            document.getElementById('tokenDetails').textContent = "No hay token disponible";
            document.getElementById('tokenStatus').innerHTML = `
                <p>Estado: <strong>No verificado</strong></p>
                <p>Usuario: <strong>No identificado</strong></p>
                <p>Expiración: <strong>N/A</strong></p>
            `;
            document.getElementById('testBtn').disabled = true;
            document.getElementById('teamsAccess').style.display = 'none';
            updateScopeStatus('teams', 'pending');
            updateScopeStatus('user', 'pending');
            updateScopeStatus('calendar', 'pending');
        }

        // Helper functions
        function updateStatus(message, type) {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }

        function createTestItem(message, status) {
            const icons = {
                loading: '🔄',
                success: '✅',
                error: '❌',
                warning: '⚠️'
            };
            return `<div class="test-item">
                <span class="test-icon">${icons[status]}</span>
                <span>${message}</span>
            </div>`;
        }

        function showLoading(buttonId, iconId) {
            const button = document.getElementById(buttonId);
            const icon = document.getElementById(iconId);
            button.disabled = true;
            icon.innerHTML = '<div class="loader"></div>';
        }

        function hideLoading(buttonId, iconText) {
            const button = document.getElementById(buttonId);
            const icon = document.getElementById(iconId);
            button.disabled = false;
            icon.textContent = iconText;
        }

        // Inicializar al cargar
        document.addEventListener('DOMContentLoaded', function() {
            initializeMsal();
            console.log("Verificador de Teams inicializado");
        });
    </script>
</body>
</html>