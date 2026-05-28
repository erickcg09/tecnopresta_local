<!DOCTYPE html>
<html>
<head>
    <title>Monitor de Token Azure AD</title>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            max-width: 1000px; 
            margin: 20px auto; 
            padding: 20px;
            background: #f5f5f5;
        }
        .panel { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .status { 
            padding: 15px; 
            border-radius: 5px; 
            margin: 10px 0;
            border-left: 5px solid #ccc;
        }
        .status.healthy { border-left-color: #4CAF50; background: #E8F5E8; }
        .status.warning { border-left-color: #FF9800; background: #FFF3E0; }
        .status.error { border-left-color: #F44336; background: #FFEBEE; }
        button { 
            background: #0078d4; 
            color: white; 
            border: none; 
            padding: 10px 15px; 
            margin: 5px; 
            border-radius: 4px; 
            cursor: pointer;
        }
        button:hover { background: #106ebe; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        .log { 
            background: #1e1e1e; 
            color: #00ff00; 
            padding: 15px; 
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        .hidden { display: none; }
    </style>
</head>
<body>
    <div class="panel">
        <h1>🔐 Monitor de Token Azure AD</h1>
        
        <div class="controls">
            <button onclick="verificarEstado()">🔄 Verificar Estado</button>
            <button onclick="renovarToken()">🔄 Renovar Token</button>
            <button onclick="verificarRenovacionAutomatica()">⏰ Probar Renovación Auto</button>
            <button onclick="limpiarLog()">🧹 Limpiar Log</button>
        </div>

        <div id="status" class="status">
            <h3>Estado del Sistema</h3>
            <div id="statusContent">Presiona "Verificar Estado" para comenzar...</div>
        </div>

        <div id="tokenInfo" class="hidden">
            <h3>Información del Token</h3>
            <pre id="tokenInfoContent"></pre>
        </div>
    </div>

    <div class="panel">
        <h3>📊 Registro de Eventos</h3>
        <div id="log" class="log"></div>
    </div>

    <script>
        // Estado del sistema
        let ultimaVerificacion = null;
        let estadoActual = 'desconocido';

        // Elementos DOM
        const statusElement = document.getElementById('status');
        const statusContent = document.getElementById('statusContent');
        const logElement = document.getElementById('log');
        const tokenInfo = document.getElementById('tokenInfo');
        const tokenInfoContent = document.getElementById('tokenInfoContent');

        // Función para agregar logs
        function log(mensaje, tipo = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            
            let icono = '📄';
            if (tipo === 'error') icono = '❌';
            if (tipo === 'success') icono = '✅';
            if (tipo === 'warning') icono = '⚠️';
            
            logEntry.innerHTML = `<span style="color: #888;">[${timestamp}]</span> ${icono} ${mensaje}`;
            logElement.appendChild(logEntry);
            logElement.scrollTop = logElement.scrollHeight;
        }

        // Función para limpiar log
        function limpiarLog() {
            logElement.innerHTML = '';
            log('Log limpiado manualmente');
        }

        // Verificar estado del token
        async function verificarEstado() {
            log('Verificando estado del token...');
            
            try {
                const response = await fetch('debug_token.php', {
                    credentials: 'include'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                ultimaVerificacion = new Date();
                
                // Actualizar UI según estado
                if (data.token_present) {
                    statusElement.className = 'status healthy';
                    statusContent.innerHTML = `
                        <strong>✅ TOKEN VÁLIDO</strong><br>
                        <strong>Session ID:</strong> ${data.session_id}<br>
                        <strong>Creado:</strong> ${data.token_info?.issued_at || 'N/A'}<br>
                        <strong>Expira:</strong> ${data.token_info?.expires_at || 'N/A'}<br>
                        <strong>Estado:</strong> ${data.token_info?.has_expired ? '❌ EXPIRADO' : '✅ VÁLIDO'}<br>
                        <strong>Última verificación:</strong> ${ultimaVerificacion.toLocaleTimeString()}
                    `;
                    
                    // Mostrar detalles del token
                    tokenInfoContent.textContent = JSON.stringify(data, null, 2);
                    tokenInfo.classList.remove('hidden');
                    
                    log('Token encontrado y válido', 'success');
                    estadoActual = 'healthy';
                    
                } else {
                    statusElement.className = 'status error';
                    statusContent.innerHTML = `
                        <strong>❌ TOKEN NO ENCONTRADO</strong><br>
                        <strong>Session ID:</strong> ${data.session_id}<br>
                        <strong>Mensaje:</strong> No hay token en la sesión PHP<br>
                        <strong>Última verificación:</strong> ${ultimaVerificacion.toLocaleTimeString()}
                    `;
                    log('Token no encontrado en sesión PHP', 'error');
                    estadoActual = 'error';
                }
                
            } catch (error) {
                statusElement.className = 'status error';
                statusContent.innerHTML = `
                    <strong>❌ ERROR DE CONEXIÓN</strong><br>
                    <strong>Error:</strong> ${error.message}<br>
                    Verifica que debug_token.php esté disponible.
                `;
                log(`Error al verificar estado: ${error.message}`, 'error');
                estadoActual = 'error';
            }
        }

        // Renovar token manualmente
        async function renovarToken() {
            log('Iniciando renovación manual de token...');
            
            if (typeof getTokenForCitas === 'undefined') {
                log('ERROR: getTokenForCitas no está disponible', 'error');
                alert('Error: auth_citas_integration.js no está cargado');
                return;
            }
            
            try {
                const token = await getTokenForCitas({ pushToServer: true });
                
                if (token) {
                    log('✅ Token renovado exitosamente', 'success');
                    // Verificar el nuevo estado después de 1 segundo
                    setTimeout(verificarEstado, 1000);
                } else {
                    log('❌ No se pudo renovar el token (retornó null)', 'error');
                }
            } catch (error) {
                log(`❌ Error en renovación: ${error.message}`, 'error');
            }
        }

        // Probar renovación automática
        async function verificarRenovacionAutomatica() {
            log('Probando renovación automática...');
            
            if (!window.getTokenForCitas) {
                log('ERROR: Sistema de renovación automática no disponible', 'error');
                return;
            }
            
            // Simular la renovación que hace auth_citas_integration.js
            try {
                const token = await window.getTokenForCitas({ pushToServer: false });
                if (token) {
                    log('✅ Renovación automática funcionando correctamente', 'success');
                } else {
                    log('⚠️ Renovación automática retornó null', 'warning');
                }
            } catch (error) {
                log(`❌ Error en renovación automática: ${error.message}`, 'error');
            }
        }

        // Verificación automática periódica
        function iniciarMonitorAutomatico() {
            // Verificar cada 2 minutos
            setInterval(() => {
                log('Verificación automática...');
                verificarEstado();
            }, 2 * 60 * 1000);
            
            log('Monitor automático iniciado (cada 2 minutos)');
        }

        // Inicialización
        window.addEventListener('load', function() {
            log('🚀 Panel de monitorización inicializado');
            log('Sistema listo para verificar tokens Azure AD');
            verificarEstado();
            iniciarMonitorAutomatico();
        });

        // Exponer funciones globalmente para debugging
        window.monitorToken = {
            verificarEstado,
            renovarToken,
            verificarRenovacionAutomatica,
            limpiarLog
        };
    </script>
</body>
</html>