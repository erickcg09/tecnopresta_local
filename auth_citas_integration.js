// auth_citas_integration.js
// ------------------------
// Requiere que globalmente existan: myMSALObj, loginRequest, graphRequest
// Debe cargarse después de formulario_login.js en el index por que Mau debe hacer login de primero

(function () {
  // Cuenta actual (no tocamos myMSALObj ni la lógica de login central)
  let _currentAccount = null;

  // Intentar restaurar cuenta a partir de MSAL (si el login ya se hizo)
  function restoreAccount() {
    try {
      const accounts = myMSALObj.getAllAccounts();
      if (accounts && accounts.length > 0) {
        _currentAccount = accounts[0];
        console.log("auth_citas_integration: cuenta restaurada:", _currentAccount.username);
      } else {
        _currentAccount = null;
        console.log("auth_citas_integration: no hay cuenta activa.");
      }
    } catch (err) {
      console.warn("auth_citas_integration: error al obtener cuentas MSAL", err);
      _currentAccount = null;
    }
  }

  // Obtener token de Graph en silencio, si falla intenta popup (mínima interacción esperemos no usarlo porque no creo que no funciona)
  async function acquireGraphToken() {
    restoreAccount();

    if (!_currentAccount) {
      throw new Error("No hay cuenta MSAL activa (acquireGraphToken).");
    }

    try {
      const tokenResp = await myMSALObj.acquireTokenSilent({
        ...graphRequest,
        account: _currentAccount
      });
      // tokenResp.accessToken es lo que necesitamos para poder colocar las citas
      return tokenResp.accessToken;
    } catch (err) {
      console.warn("acquireTokenSilent falló:", err);

      // Detectar si se requiere interacción (MSAL lanza InteractionRequiredAuthError)
      const needsInteraction = (err && (err instanceof msal.InteractionRequiredAuthError))
                              || (err && typeof err.errorCode === 'string' && err.errorCode === 'interaction_required')
                              || (err && typeof err.errorMessage === 'string' && err.errorMessage.indexOf('interaction_required') >= 0);

      if (needsInteraction) {
        // Solo aquí pedimos popup (esto puede mostrar UI)
        try {
          const popupResp = await myMSALObj.acquireTokenPopup(graphRequest);
          return popupResp.accessToken;
        } catch (popupErr) {
          console.error("acquireTokenPopup falló:", popupErr);
          throw popupErr;
        }
      } else {
        // Otro tipo de error (network, etc) — propagar para que el llamador lo maneje
        throw err;
      }
    }
  }

  // Muy importante: enviar token al servidor de forma segura (HTTPS). ya lo tendria en php.
  async function pushTokenToServer(accessToken) {
    try {
      await fetch('guardar_token.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ access_token: accessToken })
      });
      console.log("auth_citas_integration: token enviado al servidor (guardar_token.php).");
    } catch (err) {
      console.warn("auth_citas_integration: no se pudo enviar token al servidor:", err);
    }
  }

  // Función pública: obtener token listo para usar (renueva si hace falta)
  window.getTokenForCitas = async function({ pushToServer = false } = {}) {
    try {
      const token = await acquireGraphToken();
      if (pushToServer) {
        // opcional: enviar al servidor para uso backend
        await pushTokenToServer(token);
      }
      return token;
    } catch (err) {
      console.error("getTokenForCitas: no se pudo obtener token:", err);
      return null; // el llamador debe manejar null
    }
  };

  // Renovación automática periódica (cada 45 minutos por defecto muy importante ya que no puedo estar haciendo login desde otro punto del tecnopresta)
  const RENEW_INTERVAL_MS = 45 * 60 * 1000;
  let renewalTimer = null;

  function startAutoRenewal() {
    // Limpiar anterior si existiera
    if (renewalTimer) clearInterval(renewalTimer);

    // Ejecutar primero inmediatamente si hay cuenta activa
    (async () => {
      restoreAccount();
      if (_currentAccount) {
        try {
          await acquireGraphToken();
          console.log("auth_citas_integration: token inicial renovado OK.");
        } catch (err) {
          console.warn("auth_citas_integration: fallo renovar token inicial:", err);
        }
      }
    })();

    renewalTimer = setInterval(async () => {
      restoreAccount();
      if (!_currentAccount) return; // nada que renovar
      try {
        await acquireGraphToken();
        console.log("auth_citas_integration: token renovado automáticamente");
      } catch (err) {
        console.warn("auth_citas_integration: falla en renovación automática:", err);
      }
    }, RENEW_INTERVAL_MS);
  }

  // Iniciar cuando cargue la página (pero sin forzar login)
  window.addEventListener('load', function () {
    restoreAccount();
    startAutoRenewal();
  });

  // También escuchar cambios básicos: si alguien hace loginPopup en la página,
  // MSAL actualiza internamente; nos aseguramos de restaurar cuenta.
  // (No interferimos con la lógica de formulario_login.js)
  window.addEventListener('msal:login', function () {
    console.log("auth_citas_integration: evento msal:login recibido, restaurando cuenta.");
    restoreAccount();
  });

})();
