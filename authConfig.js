

        const msalConfig = {
            auth: {
              clientId: "be0e9b41-718d-4d2a-8c2f-d26eba67d767",              
              authority: "https://login.microsoftonline.com/mep.go.cr",
              redirectUri: "https://tecnopresta.mep.go.cr/index.html"
            },
            cache: {
              cacheLocation: "sessionStorage", // This configures where your cache will be stored
              storeAuthStateInCookie: false, // Set this to "true" if you are having issues on IE11 or Edge
            }
          };
          
          // Add scopes here for ID token to be used at Microsoft identity platform endpoints.
          const loginRequest = {
           scopes: ["openid", "profile", "User.Read"]
          };
          
          // Add scopes here for access token to be used at Microsoft Graph API endpoints.
          const tokenRequest = {
           scopes: ["User.Read", "Mail.Read"]
          };