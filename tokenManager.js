// tokenManager.js - Gestión de tokens para Microsoft Graph
class TokenManager {
    constructor() {
        this.tokenAcquired = false;
    }

    // Obtener token de acceso para Graph API
    async getGraphToken() {
        try {
            const currentAccounts = myMSALObj.getAllAccounts();
            
            if (currentAccounts.length === 0) {
                console.warn("No hay cuentas activas");
                return null;
            }

            const account = currentAccounts[0];
            const tokenResponse = await myMSALObj.acquireTokenSilent({
                ...graphRequest,
                account: account
            });

            if (tokenResponse && tokenResponse.accessToken) {
                await this.storeTokenInServer(tokenResponse.accessToken);
                return tokenResponse.accessToken;
            }
            
            return null;
        } catch (error) {
            console.error("Error obteniendo token:", error);
            
            // Fallback: intentar con popup si el silent falla
            if (error.name === "InteractionRequiredAuthError") {
                try {
                    const tokenResponse = await myMSALObj.acquireTokenPopup(graphRequest);
                    if (tokenResponse && tokenResponse.accessToken) {
                        await this.storeTokenInServer(tokenResponse.accessToken);
                        return tokenResponse.accessToken;
                    }
                } catch (popupError) {
                    console.error("Error en popup:", popupError);
                }
            }
            
            return null;
        }
    }

    // Almacenar token en el servidor mediante PHP
    async storeTokenInServer(token) {
        if (this.tokenAcquired) return; // Evitar múltiples llamadas
        
        try {
            const formData = new FormData();
            formData.append('access_token', token);
            formData.append('action', 'store_token');

            const response = await fetch('tokenHandler.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin' // Importante para mantener la sesión
            });

            if (response.ok) {
                this.tokenAcquired = true;
                console.log("Token almacenado en servidor correctamente");
            } else {
                console.error("Error almacenando token en servidor");
            }
        } catch (error) {
            console.error("Error en storeTokenInServer:", error);
        }
    }

    // Verificar si ya tenemos token en sesión
    async checkStoredToken() {
        try {
            const response = await fetch('tokenHandler.php?action=check_token', {
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                const result = await response.json();
                return result.hasToken || false;
            }
        } catch (error) {
            console.error("Error verificando token:", error);
        }
        return false;
    }
}

// Instancia global del manager
window.tokenManager = new TokenManager();