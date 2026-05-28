<?php

include('vendor/autoload.php');

session_start();  

error_reporting(-1);  
ini_set("display_errors", "on");  

$client_id = "be0e9b41-718d-4d2a-8c2f-d26eba67d767";  //Application (client) ID
$ad_tenant = "0fa1fe2a-d55b-4665-95e7-53a56927d833";  //Azure Active Directory Tenant ID, with Multitenant apps you can use "common" as Tenant ID, but using specific endpoint is recommended when possible
$client_secret = "8a56096e-345f-4163-a119-34b591a99513";  //Client Secret, remember that this expires someday unless you haven't set it not to do so
$redirect_uri = "https://tecnopresta.mep.go.cr/authorization1.php";  //This needs to match 100% what is set in Azure
$error_email = "mauricio.bermudez.vargas@mep.go.cr";  //If your php.ini doesn't contain sendmail_from, use: ini_set("sendmail_from", "user@example.com");

// Initialize the OAuth client
$oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
  'clientId'                => $client_id,
  'clientSecret'            => $client_secret,
  'redirectUri'             => $redirect_uri,
  'urlAuthorize'            => "https://login.microsoftonline.com/common/oauth2/v2.0/authorize",
  'urlAccessToken'          => "https://login.microsoftonline.com/common/oauth2/v2.0/token",
  'urlResourceOwnerDetails' => '',
  'scopes'                  => 'openid profile offline_access user.read'
]);

if (!isset($_GET["code"]) {  

  $authUrl = $oauthClient->getAuthorizationUrl();  

  header('Location:'. $authUrl);
  exit;
  
} 

echo "\n<a href=\"" . $redirect_uri . "\">Intentar de nuevo...</a>";  //Only to ease up your tests

?>