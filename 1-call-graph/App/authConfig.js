

        const msalConfig = {
            auth: {
              clientId: "96244292-0baa-42da-bfa4-d1472fa5023f",
              authority: "https://login.microsoftonline.com/0fa1fe2a-d55b-4665-95e7-53a56927d833",
              redirectUri: "https://tecnopresta.mep.go.cr/1-call-graph/App/index.html",
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