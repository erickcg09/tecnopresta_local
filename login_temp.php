<!DOCTYPE html>
<html>
<head>
    <title>Login con Azure - Scopes Corregidos</title>
    <script src="https://alcdn.msauth.net/browser/2.21.0/js/msal-browser.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-info" id="status">Estado: Listo para iniciar sesión</div>
        <button id="loginBtn" class="btn btn-primary">Iniciar Sesión</button>
        <div id="result" class="mt-3"></div>
    </div>

    <script>
        // Configuración MSAL
        const msalConfig = {
            auth: {
                clientId: "be0e9b41-718d-4d2a-8c2f-d26eba67d767",
                authority: "https://login.microsoftonline.com/mep.go.cr",
                redirectUri: "https://tecnopresta.mep.go.cr/index.html"
            },
            cache: {
                cacheLocation: "sessionStorage",
                storeAuthStateInCookie: false
            }
        };

        const msalInstance = new msal.PublicClientApplication(msalConfig);
        
        // SCOPES CORREGIDOS - Solo usar scopes válidos
        const loginRequest = {
            scopes: [
                "User.Read", 
                "Calendars.Read",
                "openid", 
                "profile",
                "email"
            ]
        };

        // Elementos UI
        const statusElement = document.getElementById("status");
        const resultElement = document.getElementById("result");
        const loginBtn = document.getElementById("loginBtn");

        function updateStatus(message) {
            statusElement.textContent = `Estado: ${message}`;
            console.log(`Status: ${message}`);
        }

        // Evento de login
        loginBtn.addEventListener("click", async () => {
            updateStatus("Iniciando proceso de login...");
            try {
                const loginResponse = await msalInstance.loginPopup(loginRequest);
                updateStatus("Login exitoso, obteniendo datos del usuario...");
                console.log("Login Response:", loginResponse);
                await getGraphData(loginResponse.account);
            } catch (error) {
                updateStatus(`Error en login: ${error.message}`);
                console.error("Login Error:", error);
                resultElement.innerHTML = `<div class="alert alert-danger">Error en login: ${error.message}</div>`;
            }
        });

        // Función para obtener datos de Graph
        async function getGraphData(account) {
            updateStatus("Solicitando token para Microsoft Graph...");
            
            const tokenRequest = {
                scopes: ["User.Read", "Calendars.ReadWrite"], // Scopes corregidos
                account: account
            };

            try {
                const tokenResponse = await msalInstance.acquireTokenSilent(tokenRequest);
                updateStatus("Token obtenido, consultando Microsoft Graph...");
                console.log("Token Response:", tokenResponse);

                // Obtener información básica del usuario
                const userResponse = await fetch("https://graph.microsoft.com/v1.0/me", {
                    headers: { 
                        Authorization: `Bearer ${tokenResponse.accessToken}`,
                        "Content-Type": "application/json"
                    }
                });

                if (!userResponse.ok) {
                    throw new Error(`Error Graph API: ${userResponse.status} ${userResponse.statusText}`);
                }

                const userData = await userResponse.json();
                updateStatus("Datos básicos obtenidos, buscando foto...");
                console.log("User Data:", userData);

                // Intentar obtener foto
                let photoUrl = "";
                try {
                    const photoResponse = await fetch("https://graph.microsoft.com/v1.0/me/photo/$value", {
                        headers: { Authorization: `Bearer ${tokenResponse.accessToken}` }
                    });
                    
                    if (photoResponse.ok) {
                        const photoBlob = await photoResponse.blob();
                        photoUrl = URL.createObjectURL(photoBlob);
                        updateStatus("Foto obtenida exitosamente");
                    } else {
                        updateStatus("Usuario no tiene foto de perfil");
                    }
                } catch (photoError) {
                    console.log("Error obteniendo foto:", photoError);
                    updateStatus("No se pudo obtener la foto");
                }

                // Obtener información adicional del perfil
                let managerInfo = "No disponible";
                try {
                    const managerResponse = await fetch("https://graph.microsoft.com/v1.0/me/manager", {
                        headers: { Authorization: `Bearer ${tokenResponse.accessToken}` }
                    });
                    if (managerResponse.ok) {
                        const managerData = await managerResponse.json();
                        managerInfo = managerData.displayName || "Disponible pero sin nombre";
                    }
                } catch (managerError) {
                    console.log("Error obteniendo información del manager:", managerError);
                }

                // Preparar datos finales
                const userInfo = {
                    email: userData.mail || userData.userPrincipalName,
                    displayName: userData.displayName,
                    id: userData.id,
                    jobTitle: userData.jobTitle || "No especificado",
                    department: userData.department || "No especificado",
                    officeLocation: userData.officeLocation || "No especificado",
                    mobilePhone: userData.mobilePhone || "No especificado",
                    businessPhones: userData.businessPhones || [],
                    manager: managerInfo,
                    photo: photoUrl,
                    accessToken: tokenResponse.accessToken ? "***TOKEN_PRESENTE***" : "No disponible",
                    rawData: userData
                };

                // Mostrar en UI
                resultElement.innerHTML = `
                    <div class="alert alert-success">
                        <h4>¡Login Exitoso!</h4>
                        <p><strong>Usuario:</strong> ${userInfo.displayName}</p>
                        <p><strong>Email:</strong> ${userInfo.email}</p>
                        <p><strong>Puesto:</strong> ${userInfo.jobTitle}</p>
                        <p><strong>Departamento:</strong> ${userInfo.department}</p>
                        <p><strong>Manager:</strong> ${userInfo.manager}</p>
                        ${photoUrl ? `<img src="${photoUrl}" class="img-thumbnail" width="100">` : ''}
                    </div>
                    <h5>Datos completos:</h5>
                    <pre class="bg-light p-3">${JSON.stringify(userInfo, null, 2)}</pre>
                `;

                updateStatus("Enviando datos al backend...");
                
                // Enviar al backend
                await sendToBackend(userInfo);

            } catch (error) {
                updateStatus(`Error obteniendo datos: ${error.message}`);
                console.error("Graph Data Error:", error);
                resultElement.innerHTML = `<div class="alert alert-danger">Error obteniendo datos: ${error.message}</div>`;
            }
        }

        // Función para enviar al backend
        async function sendToBackend(userData) {
            try {
                updateStatus("Enviando datos a nuevo_ingreso.php...");
                
                // Crear copia sin el token completo por seguridad
                const dataToSend = {
                    email: userData.email,
                    displayName: userData.displayName,
                    id: userData.id,
                    jobTitle: userData.jobTitle,
                    department: userData.department,
                    officeLocation: userData.officeLocation,
                    mobilePhone: userData.mobilePhone,
                    manager: userData.manager,
                    hasPhoto: !!userData.photo
                };

                const response = await fetch('nuevo_ingreso.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(dataToSend)
                });
                
                console.log("Response del backend:", response);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.text();
                console.log("Respuesta completa del backend:", result);
                
                updateStatus("Datos enviados al backend exitosamente");
                
                // Mostrar respuesta del backend
                resultElement.innerHTML += `
                    <div class="alert alert-info mt-3">
                        <h5>Respuesta del backend (nuevo_ingreso.php):</h5>
                        <pre>${result}</pre>
                    </div>
                `;
                
            } catch (error) {
                updateStatus(`Error enviando al backend: ${error.message}`);
                console.error("Backend Error:", error);
                resultElement.innerHTML += `<div class="alert alert-warning">Error enviando al backend: ${error.message}</div>`;
            }
        }

        updateStatus("Aplicación cargada correctamente");
    </script>
</body>
</html>