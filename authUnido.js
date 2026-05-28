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

// Scopes para login inicial de Mau no lo toco
const loginRequest = {
    scopes: ["openid", "profile", "User.Read"]
};

// Scopes optimizados para citas (sin Mail.Send)
const graphRequest = {
    scopes: [
        "User.Read", 
        "Calendars.ReadWrite",
        "OnlineMeetings.ReadWrite"
        // usamos PHPMailer no necesitamos Mail.Send
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