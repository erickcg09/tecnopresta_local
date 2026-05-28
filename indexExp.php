<?php

require_once __DIR__ . '/vendor/autoload.php';
 
// Include the Microsoft Graph classes
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
 
// This is taken from the apps.dev.microsoft.com portal an is the "Application ID"
const CLIENT_ID     = '96244292-0baa-42da-bfa4-d1472fa5023f';
 
// This is the secret ITS generates for you
const CLIENT_SECRET = '0fa1fe2a-d55b-4665-95e7-53a56927d833';
 
// This should be one of the reply URLs set in your application
const REDIRECT_URI           = 'https://tecnopresta.mep.go.cr';
 
// This is the object id of the user we want to use
const USER = '8e95bfd6-6629-4a0a-86a0-3c0ba6fc0b4c';
 
// Set up a new Oauth2 client that will be talking to Azure
$provider = new TheNetworg\OAuth2\Client\Provider\Azure([
    'clientId'                => CLIENT_ID,
    'clientSecret'            => CLIENT_SECRET,
    'redirectUri'             => REDIRECT_URI,
]);
 
// Change the URLs of our Oauth2 request to the correct endpoint and tenant
$provider->urlAPI = "https://graph.microsoft.com/v1.0/";
$provider->resource = "https://graph.microsoft.com/";
$provider->tenant = 'tecnopresta.mep.go.cr';
 
// Try to get an access token using the client credentials grant.
$accessToken = $provider->getAccessToken( 'client_credentials', $options );
 
// Start a new Guzzle client
$client = new \GuzzleHttp\Client();
 
// Set up headers
$headers = [
    'Authorization' => 'Bearer ' . $accessToken->getToken(),
];
 
// Wrap our HTTP request in a try/catch block so we can decode problems
try {
 
	// Set up our request to the API
	$response = $client->request(
		'GET',
		'https://graph.microsoft.com/v1.0/users/' . USER . '/events',
		array( 'headers' => $headers )
	);
 
	// Store the result as an object
	$result = json_decode( $response->getBody() );
 
// Decode any exceptions Guzzle throws
} catch (GuzzleHttp\Exception\ClientException $e) {
    $response = $e->getResponse();
    $responseBodyAsString = $response->getBody()->getContents();
    echo $responseBodyAsString;
    exit();
}
?>
<HTML> 	  	 
  	<HEAD> 	 
  	  	<TITLE> Logeo a office 365 </TITLE>
  	  	<style>
	h3 {
		margin-bottom: 0;
	}
 
	.event {
		margin-bottom: 3rem;
	}
 
	.dates {
		font-style: italic;
	}
 
	.location {
		font-size: .8rem;
	}
</style>
  	</HEAD> 	 
  	<BODY> 	 

  	</BODY> 	 
</HTML>