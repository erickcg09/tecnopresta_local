// =======================================================
// CONFIGURACIÓN GENERAL
// =======================================================

// Modo automático: si estás en localhost, se desactiva Azure
const MODO_LOCAL = (location.hostname === "localhost" || location.hostname === "127.0.0.1");
console.log("HOST:", location.hostname);
console.log("MODO_LOCAL:", MODO_LOCAL);
// Usuario quemado para desarrollo local
const USUARIO_PRUEBA = "erick.cerdas.gonzalez@mep.go.cr";

// =======================================================
// CONFIGURACIÓN MSAL (SOLO PRODUCCIÓN)
// =======================================================

const msalConfig = {
    auth: {
        clientId: "be0e9b41-718d-4d2a-8c2f-d26eba67d767",
        authority: "https://login.microsoftonline.com/mep.go.cr",
        redirectUri: "https://tecnopresta.mep.go.cr/index.html"
    },
    cache: {
        cacheLocation: "sessionStorage",
        storeAuthStateInCookie: false,
    }
};

// Scopes para login inicial (sin cambios)
const loginRequest = {
    scopes: ["openid", "profile", "User.Read"]
};

// Scopes optimizados para citas - AGREGAR openid y profile
const graphRequest = {
    scopes: [
        "User.Read", 
        "Calendars.ReadWrite",
        "OnlineMeetings.ReadWrite",
        "openid",    // AGREGADO
        "profile"    // AGREGADO
    ]
};

// Configuración adicional para Graph API
const graphConfig = {
    graphMeEndpoint: "https://graph.microsoft.com/v1.0/me",
    graphMailEndpoint: "https://graph.microsoft.com/v1.0/me/messages",
    graphEventsEndpoint: "https://graph.microsoft.com/v1.0/me/events",
    graphOnlineMeetingsEndpoint: "https://graph.microsoft.com/v1.0/me/onlineMeetings",
    graphPhotoEndpoint: "https://graph.microsoft.com/v1.0/me/photo/$value"
};


// ==========================================
// INSTANCIA GLOBAL MSAL
// ==========================================
window.myMSALObj = window.myMSALObj || null;

//window.msalInicializando = null;

// Inicializar MSAL
//let myMSALObj = null;

async function initializeMSAL() {
     try {

        // ==========================================
        // Evita reinicializar
        // ==========================================
        if (window.myMSALObj) {

            console.log("⚠️ MSAL ya inicializado");

            return;
        }

        // ==========================================
        // Crear instancia
        // ==========================================
        window.myMSALObj =
            new msal.PublicClientApplication(msalConfig);

        console.log("✅ MSAL inicializado correctamente");

    } catch (error) {

        console.error(
            "Error al inicializar MSAL:",
            error
        );
    }
    /*try {
        
        myMSALObj = new msal.PublicClientApplication(msalConfig);
        await myMSALObj.initialize();
        console.log("✅ MSAL inicializado correctamente");
    } catch (error) {
        console.error("Error al inicializar MSAL:", error);
    }*/

}

window.jsonData = []; //Variable global para almacenar el JSON del ws

/* window.onload = async function() {
     // Inicializar MSAL primero
     await initializeMSAL();
     sesionCompatibildad();
     loadPage();
     return true;
 };
*/
/*
window.addEventListener("load", async function () {

    // SOLO ejecuta si corresponde al login activo
    if (
        (window.tipoLoginActivo === "normal" && typeof signIn === "function")
        ||
        (window.tipoLoginActivo === "especial" && typeof signInEspecial === "function")
    ) {

        await initializeMSAL();

        sesionCompatibildad();

        loadPage();
    }
*/

window.addEventListener("load", async function () {

    // SOLO ejecuta si corresponde al login activo
    if (
        (window.tipoLoginActivo === "normal" && typeof signIn === "function")
        ||
        (window.tipoLoginActivo === "especial" && typeof signInEspecial === "function")
    ) {

         if (window.tipoLoginActivo !== "especial") {
            console.log("⛔ Login especial no activo");
         } else {
            console.log("✅ Login especial activo");
         }

        if (!MODO_LOCAL) {

            console.log("🌐 Inicializando Azure MSAL...");
            //await initializeMSAL(); ***

        } else {
            console.log("🔧 Ejecutando en MODO LOCAL");
            console.log("👤 Usuario local:", USUARIO_PRUEBA);
        }

        sesionCompatibildad();

        loadPage();
    }

});

// =======================================================
// INICIALIZACIÓN CONTROLADA DEL LOGIN
// =======================================================
/*
window.addEventListener("load", async function () {

    console.log("🚀 Iniciando formulario_login_e.js");

    // ===================================================
    // SOLO CONTINÚA SI EL LOGIN ACTIVO ES ESPECIAL
    // ===================================================

    if (window.tipoLoginActivo !== "especial") {

        console.log("⛔ Login especial no activo");

        return;
    }

    console.log("✅ Login especial activo");

    // ===================================================
    // MODO LOCAL
    // ===================================================

    if (!MODO_LOCAL) {

        console.log("🌐 Inicializando Azure MSAL...");

        await initializeMSAL();

    } else {

        console.log("🔧 Ejecutando en MODO LOCAL");
        console.log("👤 Usuario local:", USUARIO_PRUEBA);

    }

    // ===================================================
    // VALIDAR COMPATIBILIDAD
    // ===================================================

    sesionCompatibildad();

    // ===================================================
    // CARGAR LOGIN
    // ===================================================

    loadPage();

});*/
// =======================================================
// INICIALIZACIÓN PRINCIPAL
// =======================================================
/*
window.onload = async function () {

    // Si NO es localhost usa Azure
    if (!MODO_LOCAL) {

        await initializeMSAL();

    } else {

        console.log("🔧 Ejecutando en MODO LOCAL con usuario:", USUARIO_PRUEBA);

    }

    sesionCompatibildad();

    loadPage();

    return true;
}; */

function loadPage() {
    
    if (window.tipoLoginActivo !== "especial") { //no ejecuta si el login es normal o no especial
        return;
    }

    // ==========================================
    // MODO LOCAL
    // ==========================================
    if (MODO_LOCAL) {

        console.log("✅ Login automático LOCAL");

        login(USUARIO_PRUEBA);

        return;
    }


    // VERIFICACIÓN DE INICIALIZACIÓN AGREGADA
    if (!window.myMSALObj) {
        console.warn("MSAL no inicializado, reintentando...");
        setTimeout(loadPage, 100);
        return;
    }

    const currentAccounts = window.myMSALObj.getAllAccounts();

    // ==========================================
    // SI NO HAY CUENTAS EN MSAL
    // ==========================================
    if (!currentAccounts || currentAccounts.length === 0) {

        console.warn("⚠️ No hay sesión Microsoft activa");

        // ==========================================
        // FORZAR LOGIN POPUP
        // ==========================================
        signInEspecial();

        return;
    }

    // ==========================================
    // SI HAY MÁS DE UNA CUENTA
    // ==========================================
    if (currentAccounts.length > 1) {

        console.warn("⚠️ Múltiples cuentas detectadas");

        let username = currentAccounts[0].username;

        login(username);

        return;
    }

    // ==========================================
    // UNA SOLA CUENTA
    // ==========================================
    if (currentAccounts.length === 1) {

        let username = currentAccounts[0].username;

        console.log("✅ Cuenta detectada:", username);

        login(username);

        return;
    } 
   /* 
    if (currentAccounts === null) {
        console.warn("No accounts detected.");
        return;
    } else if (currentAccounts.length > 1) {
        // Add choose account code here
        console.warn("Multiple accounts detected.");
    } else if (currentAccounts.length === 1) {
        let username = currentAccounts[0].username;
        login(username);           
    }
*/
}

function handleResponse(resp) {
    /*if (resp !== null) {
        let username = resp.account?.username || myMSALObj.getAllAccounts()[0]?.username;
        login(username);           
    } else {
        loadPage();
    }*/
   if (resp !== null) {

        let username =
            resp.account?.username
            || window.myMSALObj.getAllAccounts()[0]?.username;

        console.log("✅ Login exitoso:", username);

        login(username);

    } else {

        loadPage();
    }
}

window.loginEnProceso = false;

function signInEspecial() {
   /* myMSALObj.loginPopup(loginRequest).then(handleResponse).catch(error => {
        console.error(error);
    }); */
    // ==========================================
    // MODO LOCAL
    // ==========================================
    
    if (MODO_LOCAL) {

        console.log("🔧 Login simulado con:", USUARIO_PRUEBA);
        console.log("🌐 Iniciando login sin Azure...en modo ESPECIAL");
        login(USUARIO_PRUEBA);

        return;
    }

    // ==========================================
    // AZURE LOGIN
    // ==========================================
    if (window.loginEnProceso) {
        return;
    }

    window.loginEnProceso = true;

    console.log("🌐 Iniciando login con Azure MSAL...en modo ESPECIAL");
    window.myMSALObj.loginPopup(loginRequest)
        .then(handleResponse)
        .catch(error => {

            console.error(error);

        })
        .finally(() => {
        window.loginEnProceso = false;
    });
}

function sesionCompatibildad() {
    if (typeof(Storage) !== 'undefined') {
        // Código cuando Storage es compatible    
    } else {
        // Código cuando Storage NO es compatible
        let contenedorError = document.getElementById("contenedorError");
        contenedorError.innerHTML='<div class="alert alert-danger">' +
        '<strong>Error! </strong>' +
            'No es compatible con el objeto Storage' +
        '</div>';        
    }

    if (typeof(window.FormData) !== 'undefined') {
        // Código cuando Storage es compatible    
    } else {
        // Código cuando Storage NO es compatible
        let contenedorError = document.getElementById("contenedorError1");
        contenedorError.innerHTML='<div class="alert alert-danger">' +
        '<strong>Error! </strong>' +
            'No es compatible con el objeto window.FormData' +
        '</div>';        
    }
}

$(document).ready(function() {    
    $("#fila").on("click", "a", function(event) {
        event.preventDefault();        
        let indice = $(this).data('id');
        inicioSesion(indice);       
        return false;
    });
});    
/*
function inicioSesion(indice) {
    const json = JSON.stringify(jsonData[indice]);
    window.sessionStorage.setItem('sesion',json);
    window.location.replace('formulario_menu_principal.html');
    return false;
}
*/
//**CODIGO ERICK */
async function inicioSesion(indice) {
    console.log("✅ Enviando sesión al servidor...");

    const json = JSON.stringify(jsonData[indice]);

    try {
        const formData = new FormData();
        formData.append('data', json);

        const response = await fetch('sql/sesionCargaSW.php', {
            method: 'POST',
            body: formData,
            credentials: 'include' // 🔥 CRÍTICO para sesiones PHP
        });

        //const result = await response.json();
        const text = await response.text(); //Leer como texto

        console.log("Respuestas RAW: ", text);

        //Intenta Parsear
        const result = JSON.parse(text);

        console.log("Respuesta servidor:", result);

        if (result.ok) {
            // Guarda también en sessionStorage (opcional, para frontend)
            sessionStorage.setItem('sesion', json);

            console.log("✅ Sesión PHP creada:", result.session_id);

            // Redirigir
            // window.location.replace('formulario_menu_principal.html');
            window.location.replace('formulario_menu_principal.php');
        } else {
            console.error("Error creando sesión:", result);
        }

    } catch (error) {
        console.error("Error en inicioSesion:", error);
    }
}
// FIN CODIGO ERICK

// ==============================================
// FUNCIONES PARA TOKEN DE GRAPH API - CORREGIDAS
// ==============================================

/**
 * Obtiene token de acceso para Microsoft Graph
 */
async function obtenerTokenAcceso() {
    try {
        // Verificar que MSAL esté inicializado
        if (!window.myMSALObj) {
            console.warn("MSAL no inicializado. Inicializando...");
            //await initializeMSAL(); ***
        }

        const currentAccounts = window.myMSALObj.getAllAccounts();
        
        if (currentAccounts.length === 0) {
            console.warn("No hay cuentas activas para obtener token");
            return null;
        }

        const account = currentAccounts[0];
        
        // INTENTAR OBTENER TOKEN CON SCOPES COMPLETOS
        const tokenResponse = await window.myMSALObj.acquireTokenSilent({
            scopes: graphRequest.scopes,  // USA TODOS LOS SCOPES INCLUYENDO openid y profile
            account: account
        });

        console.log("✅ Token obtenido exitosamente");
        return tokenResponse.accessToken;
        
    } catch (error) {
        console.warn("Error obteniendo token silencioso:", error);
        
        // Si falla, intentar con popup
        if (error.name === "InteractionRequiredAuthError") {
            try {
                console.log("Intentando obtener token via popup...");
                const tokenResponse = await window.myMSALObj.acquireTokenPopup({
                    scopes: graphRequest.scopes  // USA LOS MISMOS SCOPES
                });
                return tokenResponse.accessToken;
            } catch (popupError) {
                console.error("Error en popup de token:", popupError);
                return null;
            }
        }
        return null;
    }
}

/**
 * Refresca el token de acceso cuando está por expirar
 */
async function refrescarTokenSiNecesario() {
    if (MODO_LOCAL) return "TOKEN_FAKE_LOCAL";

    const tokenAlmacenado = window.sessionStorage.getItem('graphAccessToken');
    
    if (!tokenAlmacenado) {
        return await obtenerTokenAcceso();
    }

    try {
        const nuevoToken = await obtenerTokenAcceso();
        if (nuevoToken) {
            window.sessionStorage.setItem('graphAccessToken', nuevoToken);
            console.log("Token refrescado exitosamente");
        }
        return nuevoToken;
    } catch (error) {
        console.error("Error refrescando token:", error);
        return null;
    }
}

/**
 * Obtiene token válido para operaciones de calendario/Teams
 */
async function obtenerTokenParaCalendario() {
    if (MODO_LOCAL) return "TOKEN_FAKE_LOCAL";

    let token = window.sessionStorage.getItem('graphAccessToken');
    
    if (!token) {
        console.log("No hay token almacenado, obteniendo nuevo...");
        token = await refrescarTokenSiNecesario();
    } else {
        console.log("Token encontrado en almacenamiento");
        token = await refrescarTokenSiNecesario();
    }
    
    return token;
}

// ==============================================
// FUNCIÓN LOGIN PRINCIPAL (CON TOKEN GRAPH) - SIN CAMBIOS ESTRUCTURALES
// ==============================================

async function login(username) {      
    
    let btnIngresar = document.getElementById("btnIngresar");
    btnIngresar.disabled = true;
    let contenedorError = document.getElementById("contenedorError");
    btnIngresar.innerHTML = '<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    let spinner = document.getElementById("spinner");   

    const formDataLogin = new FormData();    
    formDataLogin.append('correo', username);
    formDataLogin.append('pass', '');

    try {
        const response = await fetch('sql/selectLoginGestor.php', {
            method: 'POST', 
            body: formDataLogin,     
        });

        if (!response.ok) {
            throw new Error('Error en respuesta del servidor');
        }

        const data = await response.json();  
        console.log("Lo que viene de integra:", data);
        jsonData = data;

        // ✅ OBTENER TOKEN PARA GRAPH API (CORREGIDO)
        console.log("Obteniendo token de acceso para Graph API...");
        const accessToken = await obtenerTokenAcceso();
        if (accessToken) {
             if (!MODO_LOCAL) {
                window.sessionStorage.setItem('graphAccessToken', accessToken);
                console.log("✅ Token de Graph API almacenado correctamente");
                window.sessionStorage.setItem('graphUser', username);
             } else {
                window.sessionStorage.setItem('graphAccessToken', 'TOKEN_FAKE_LOCAL');
                console.log("✅ Token de Graph API almacenado correctamente");
                window.sessionStorage.setItem('graphUser', username);
             }
                
        } else {
            console.warn("No se pudo obtener token de Graph API, pero el login continúa");
        }

        // ==========================================
        // MODO LOCAL = ENTRAR DIRECTO SIN MODAL
        // ==========================================
        if (MODO_LOCAL) {

            console.log("🔧 MODO LOCAL -> ingreso automático");

            // Toma el primer registro automáticamente
            inicioSesion(0);

            return;
        }

        // ==========================================
        // PRODUCCIÓN = COMPORTAMIENTO NORMAL
        // ==========================================
        // Lógica existente para códigos presupuestarios (SIN CAMBIOS)
        if (Object.keys(data).length === 1) {
            inicioSesion(0);
        } else if (Object.keys(data).length > 1) {
            cargaModal();
        } else if (Object.keys(data).length === 0) {
            spinner.style.visibility = 'hidden';
            btnIngresar.innerText="Ingresar";
            btnIngresar.disabled = false;
            contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong> Atención: </strong>' +
                                        'Estimado funcionario(a) MEP:'+
                                        'Recuerde que debe ingresar con correo de FUNCIONARIO ' +
                                        'NO con correo de institucion; ' + 
                                        'Si el problema persiste ' +
                                        'escribanos a <strong>tecnopresta@mep.go.cr</strong>' +
                                    '</div>';                        
        }

    } catch (error) {
        console.error("Error en login:", error);
        spinner.style.visibility = 'hidden';
        btnIngresar.innerText="Ingresar";
        btnIngresar.disabled = false;
        contenedorError.innerHTML='<div class="alert alert-danger">' +
                                '<strong>Error! Intente de nuevo </strong>' +
                                'Hubo un problema con la conexión al Servidor: ' + error.message +
                                '</div>';        
    }

    return true;    
}

// ==============================================
// FUNCIONES PARA CALENDARIOS Y TEAMS - SIN CAMBIOS
// ==============================================

/**
 * Crear evento en calendario
 */
async function crearEventoEnCalendario(eventoData) {
    
    if (MODO_LOCAL) {
        console.log("🔧 Simulación creación de evento:", eventoData);
        return { id: "EVENTO_LOCAL_FAKE" };
    }

    try {
        const token = await obtenerTokenParaCalendario();
        
        if (!token) {
            throw new Error("No se pudo obtener token de acceso para calendario");
        }

        const response = await fetch(graphConfig.graphEventsEndpoint, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(eventoData)
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error("Error creando evento:", error);
        throw error;
    }
}

/**
 * Crear reunión de Teams
 */
async function crearReunionTeams(meetingData) {
    
    if (MODO_LOCAL) {
        console.log("🔧 Simulación reunión Teams:", meetingData);
        return { joinUrl: "https://teams.local/fake" };
    }

    try {
        const token = await obtenerTokenParaCalendario();
        
        if (!token) {
            throw new Error("No se pudo obtener token de acceso para Teams");
        }

        const response = await fetch(graphConfig.graphOnlineMeetingsEndpoint, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(meetingData)
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error("Error creando reunión Teams:", error);
        throw error;
    }
}

/**
 * ===============================================
 * ESTA FUNCIÓN ES PARA OBTENER LA FOTO DEL PERSIL 
 * ===============================================
 */
// Función Erick para obtener la FOTO DEL PERFIL (MICROSOFT GRAPH)*/
async function obtenerFotoPerfil() {

    const avatarDefault = "assets/img/avatarH.svg"; 
    // 1. Buscar si ya existe en sessionStorage
    const fotoCache = sessionStorage.getItem("fotoPerfil");
    if (fotoCache) {
        return fotoCache;
    }
    // 2. Si está en modo local
    if (typeof MODO_LOCAL !== "undefined" && MODO_LOCAL) {
        sessionStorage.setItem("fotoPerfil", avatarDefault);
        return avatarDefault;
    }

    try {
        const token = await obtenerTokenAcceso();
        if (MODO_LOCAL) {
            console.log("🔧 MODO LOCAL: usando token simulado");
            return "TOKEN_FAKE_LOCAL";
        }

        const response = await fetch(graphConfig.graphPhotoEndpoint, {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        if (!response.ok) {
            sessionStorage.setItem("fotoPerfil", avatarDefault);
            await guardarFotoEnPHP(avatarDefault); // Guarda la foto por defecto en PHP
            return avatarDefault;
        }

        const blob = await response.blob();

        if (!blob || blob.size === 0) {
            sessionStorage.setItem("fotoPerfil", avatarDefault);
            return avatarDefault;
        }

        // Convertir Blob a Base64
        const base64 = await convertirBlobBase64(blob);

        // Guardar en cache
        sessionStorage.setItem("fotoPerfil", base64);

        await guardarFotoEnPHP(base64);

        return base64;

    } catch (error) {

        console.warn("No fue posible obtener foto:", error);

        sessionStorage.setItem("fotoPerfil", avatarDefault);

        return avatarDefault;
    }
}
    // Hace la función accesible desde otras páginas (como formulario_menu_principal.html)
    window.obtenerFotoPerfil = obtenerFotoPerfil;

function convertirBlobBase64(blob) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(blob);
    });
}
    
async function guardarFotoEnPHP(foto) {

    try {

        await fetch("fotoAzure.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                fotoPerfil: foto
            })
        });

    } catch (error) {
        console.warn("No se pudo guardar foto en PHP");
    }
}

//***FIN OBTENER FOTO DE PERFIL */


// ==============================================
// FUNCIÓN cargaModal (SIN CAMBIOS)
// ==============================================

function cargaModal() {
    $('.list-group-flush').remove();
    let i=0;

    jsonData.forEach(obj => {
        
        let list = document.createElement('div');
        list.className = "list-group-flush";
        
        let linkList = document.createElement('a');      
        linkList.setAttribute("data-id", i);
        linkList.setAttribute('href', "formulario_menu_principal.html");
        linkList.className = "list-group-item list-group-item-action";
        
        let contenedorCodPre = document.createElement('div');
        contenedorCodPre.className = "d-flex w-100 justify-content-between-group-flush";
        
        let columnaCodigo = document.createElement('div');
        columnaCodigo.className = "col-sm-2";
        
        let h5codigo = document.createElement('h5');
        h5codigo.className = "mb-1";

        let codPre = obj.Codigo_Presupuestario;             
        let createATextCodigo = document.createTextNode(codPre.substr(-4));
        h5codigo.appendChild(createATextCodigo);
        
        columnaCodigo.appendChild(h5codigo);
            
        let columnaNombre = document.createElement('div');
        columnaNombre.className = "col-sm-10";
        
        let h5nombre = document.createElement('h5');
        h5nombre.className = "mb-1";
        let createATextNombre = document.createTextNode(obj.Dependencia);
        h5nombre.appendChild(createATextNombre);
        
        columnaNombre.appendChild(h5nombre); 

        contenedorCodPre.appendChild(columnaCodigo);
        contenedorCodPre.appendChild(columnaNombre);
        linkList.appendChild(contenedorCodPre);
        
        list.appendChild(linkList);
        document.getElementById('fila').appendChild(list);
        
        i=i+1;             
    
    });

    $("#loginModal").modal(); //si hay varios se seleccionan uno

    return false;
}
