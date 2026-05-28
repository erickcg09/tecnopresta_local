<?php

$usuariomicro = $_SESSION['correomep'];

?>

    <script
      type="text/javascript"
      src="//cdn.jsdelivr.net/npm/bluebird@3.7.2/js/browser/bluebird.min.js"
    ></script>

    <script
      type="text/javascript"
      src="https://alcdn.msauth.net/browser/2.0.0-beta.4/js/msal-browser.js"
      integrity="sha384-7sxY2tN3GMVE5jXH2RL9AdbO6s46vUh9lUid4yNCHJMUzDoj+0N4ve6rLOmR88yN"
      crossorigin="anonymous"
    ></script> 
    
    <script type="text/javascript" src="./authConfig.js?version=1"></script>
    <script type="text/javascript" src="./graphConfig.js"></script> 
    
<script>
const myMSALObj = new msal.PublicClientApplication(msalConfig);

let accessToken;
let username = "<?php echo $usuariomicro; ?>";

function signOut() {
    const logoutRequest = {
        account: myMSALObj.getAccountByUsername(username)
    };

    myMSALObj.logout(logoutRequest);
}

signOut();
</script>


