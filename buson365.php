<?php

include('vendor/autoload.php');
session_start();  

error_reporting(-1);  
ini_set("display_errors", "on");  

$client_id = "96244292-0baa-42da-bfa4-d1472fa5023f";  //Application (client) ID
$ad_tenant = "0fa1fe2a-d55b-4665-95e7-53a56927d833";  //Azure Active Directory Tenant ID, with Multitenant apps you can use "common" as Tenant ID, but using specific endpoint is recommended when possible
$client_secret = "d9251740-96fa-4c59-9420-2f25c477dcf2";  //Client Secret, remember that this expires someday unless you haven't set it not to do so
$redirect_uri = "https://tecnopresta.mep.go.cr/buson365.php";  //This needs to match 100% what is set in Azure
$error_email = "mauricio.bermudez.vargas@mep.go.cr";  //If your php.ini doesn't contain sendmail_from, use: ini_set("sendmail_from", "user@example.com");

$oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
  'clientId'                => $client_id,
  'clientSecret'            => $client_secret,
  'redirectUri'             => $redirect_uri,
  'urlAuthorize'            => "https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/authorize",
  'urlAccessToken'          => "https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/token",
  'urlResourceOwnerDetails' => '',
  'scopes'                  => 'openid profile offline_access user.read'
]);

if (!isset($_GET["code"])) 
{  
 
  $authorizationUrl = $oauthClient->getAuthorizationUrl();

  $_SESSION['oauth2state'] = $oauthClient->getState();

  header('Location: ' . $authorizationUrl);
  exit;
  
} elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

  if (isset($_SESSION['oauth2state'])) {
      unset($_SESSION['oauth2state']);
  }

  exit('Invalid state');

} else {

  try {    
      
    //var_dump($oauthClient);
      // Try to get an access token using the authorization code grant.
      $accessToken = $oauthClient->getAccessToken('authorization_code', [
          'code' => $_GET['code']
      ]);

      // We have an access token, which we may use in authenticated
      // requests against the service provider's API.
      echo 'Access Token: ' . $accessToken->getToken() . "<br>";
      

  } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

      // Failed to get the access token or user details.
      exit($e->getMessage());

  }

}

echo "\n<a href=\"" . $redirect_uri . "\">Intentar de nuevo...</a>";  //Only to ease up your tests

?>