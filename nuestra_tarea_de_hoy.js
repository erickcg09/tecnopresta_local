/* diagnostico_msal_graph_silent.js
   Variante A - Consola solamente, silent-only (no popup).
   Incluir *después* de formulario_login.js.
*/

(function () {
  'use strict';

  const CONFIG = {
    pollIntervalMs: 180,        // frecuencia de comprobación (pequeña para intentar capturar antes de redirecciones)
    maxAttempts: 80,           // 80*180ms ≈ 14.4s antes de rendirse
    // Scopes por defecto si no existe window.graphRequest
    defaultScopes: ['User.Read', 'Calendars.Read', 'OnlineMeeting.Read.All'],
    // Endpoints por defecto si no existe window.graphConfig
    endpoints: {
      events: 'https://graph.microsoft.com/v1.0/me/events',
      onlineMeetings: 'https://graph.microsoft.com/v1.0/me/onlineMeetings'
    }
  };

  // Util: decodifica JWT payload (sin dependencias)
  function decodeJwt(token) {
    try {
      const parts = (token || '').split('.');
      if (parts.length < 2) return null;
      const payload = parts[1].replace(/-/g, '+').replace(/_/g, '/');
      const json = decodeURIComponent(atob(payload).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
      }).join(''));
      return JSON.parse(json);
    } catch (e) {
      return null;
    }
  }

  // Llamada GET simple con fetch
  async function callGraph(endpoint, accessToken) {
    try {
      const resp = await fetch(endpoint, {
        method: 'GET',
        headers: {
          Authorization: `Bearer ${accessToken}`,
          Accept: 'application/json'
        }
      });
      const text = await resp.text();
      let body;
      try { body = JSON.parse(text); } catch (e) { body = text; }
      return { ok: resp.ok, status: resp.status, body };
    } catch (err) {
      return { ok: false, status: 0, body: err && err.message ? err.message : String(err) };
    }
  }

  // Obtiene access token silent-only. Usa window.graphRequest si existe.
  async function acquireSilentToken(account) {
    if (!window.myMSALObj) throw new Error('myMSALObj no disponible');
    const request = (window.graphRequest && typeof window.graphRequest === 'object')
      ? Object.assign({}, window.graphRequest)
      : { scopes: CONFIG.defaultScopes };
    request.account = account;

    // Intentamos acquireTokenSilent (sin popup)
    return myMSALObj.acquireTokenSilent(request);
  }

  // Proceso principal: obtiene cuenta, token y llama endpoints
  async function performCheck() {
    console.groupCollapsed('[diagnostico_msal_graph_silent] Inicio de comprobación');
    try {
      if (!window.myMSALObj || !myMSALObj.getAllAccounts) {
        console.warn('[diagnostico] myMSALObj no detectado en window.');
        console.groupEnd();
        return;
      }

      const accounts = myMSALObj.getAllAccounts();
      if (!accounts || accounts.length === 0) {
        console.warn('[diagnostico] No hay cuentas MSAL (usuario no logeado).');
        console.groupEnd();
        return;
      }

      const account = accounts[0];
      console.log('[diagnostico] Cuenta detectada:', account);

      // Intentar silent token
      let tokenResponse;
      try {
        console.log('[diagnostico] Intentando acquireTokenSilent (modo SILENT-only)...');
        tokenResponse = await acquireSilentToken(account);
      } catch (err) {
        console.error('[diagnostico] acquireTokenSilent FALLÓ (silent-only), no se abrirá popup. Error:', err && (err.errorMessage || err.message) ? (err.errorMessage || err.message) : err);
        console.groupEnd();
        return;
      }

      if (!tokenResponse || !tokenResponse.accessToken) {
        console.error('[diagnostico] No se recibió accessToken en la respuesta silent.');
        console.groupEnd();
        return;
      }

      const accessToken = tokenResponse.accessToken;
      console.log('[diagnostico] Access token OBTENIDO (solo consola).');

      // Decodificar y mostrar claims relevantes
      const payload = decodeJwt(accessToken);
      if (payload) {
        console.log('[diagnostico] JWT payload:', payload);
        console.log('[diagnostico] subject:', payload.sub || payload.oid || payload.upn || '(sin sub)');
        console.log('[diagnostico] scopes/roles:', payload.scp || payload.roles || '(no scp/roles)');
        console.log('[diagnostico] exp (epoch):', payload.exp ? payload.exp + ' -> ' + new Date(payload.exp * 1000).toString() : '(no exp)');
      } else {
        console.warn('[diagnostico] No se pudo decodificar JWT payload.');
      }

      // Endpoints (usar graphConfig si está)
      const eventsEndpoint = (window.graphConfig && graphConfig.graphEventsEndpoint) ? graphConfig.graphEventsEndpoint : CONFIG.endpoints.events;
      const meetingsEndpoint = (window.graphConfig && graphConfig.graphOnlineMeetingsEndpoint) ? graphConfig.graphOnlineMeetingsEndpoint : CONFIG.endpoints.onlineMeetings;

      console.log('[diagnostico] Llamando a Graph:', eventsEndpoint);
      const resEvents = await callGraph(eventsEndpoint, accessToken);
      console.log('[diagnostico] /me/events → status', resEvents.status, 'ok:', resEvents.ok);
      if (resEvents.ok) {
        const count = Array.isArray(resEvents.body && resEvents.body.value) ? resEvents.body.value.length : null;
        console.log('[diagnostico] Eventos recibidos:', count !== null ? `count=${count}` : resEvents.body);
      } else {
        console.warn('[diagnostico] Respuesta /me/events:', resEvents.body);
      }

      console.log('[diagnostico] Llamando a Graph:', meetingsEndpoint);
      const resMeet = await callGraph(meetingsEndpoint, accessToken);
      console.log('[diagnostico] /me/onlineMeetings → status', resMeet.status, 'ok:', resMeet.ok);
      if (resMeet.ok) {
        const count = Array.isArray(resMeet.body && resMeet.body.value) ? resMeet.body.value.length : null;
        console.log('[diagnostico] OnlineMeetings recibidas:', count !== null ? `count=${count}` : resMeet.body);
      } else {
        console.warn('[diagnostico] Respuesta /me/onlineMeetings:', resMeet.body);
      }

      console.log('[diagnostico] Comprobación finalizada con éxito.');
    } catch (err) {
      console.error('[diagnostico] Error inesperado:', err && (err.message || err.errorMessage) ? (err.message || err.errorMessage) : err);
    } finally {
      console.groupEnd();
    }
  }

  // ---- Watcher no invasivo ----
  (function startWatcher() {
    let attempts = 0;
    const id = setInterval(() => {
      attempts += 1;
      try {
        // Condición para ejecutar: myMSALObj existe y hay al menos una cuenta
        if (window.myMSALObj && typeof myMSALObj.getAllAccounts === 'function') {
          const accounts = myMSALObj.getAllAccounts();
          if (accounts && accounts.length >= 1) {
            // Ejecuta la comprobación asincrónica (no bloqueante)
            performCheck().catch(e => console.error('[diagnostico] performCheck error:', e));
            clearInterval(id);
            // Auto-limpieza: eliminamos referencia para no dejar objetos colgando
            try { delete window.__diagnostico_msal_graph_silent; } catch (e) {}
            return;
          }
        }
      } catch (e) {
        // No queremos molestar con logs repetidos en cada intento
      }

      if (attempts >= CONFIG.maxAttempts) {
        clearInterval(id);
        // Intento final: avisar en consola que se rindió
        console.info('[diagnostico] No se detectó cuenta MSAL en el tiempo esperado. Abortando comprobación silent-only.');
      }
    }, CONFIG.pollIntervalMs);

    // Exponer una referencia mínima por si querés detenerlo desde consola manualmente
    window.__diagnostico_msal_graph_silent = {
      stop: () => { clearInterval(id); console.info('[diagnostico] Watcher detenido manualmente.'); }
    };
  })();

})();
