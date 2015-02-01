<?php

########## Google Settings.. Client ID, Client Secret from https://cloud.google.com/console #############
$google_client_id       = '394993466159-smtdupnd8tnkhjfcp72uq1mvfo81b6fn.apps.googleusercontent.com';
$google_client_secret   = 'AOt--W8da1YUSAYHsN2bH3Qz';
$google_redirect_url    = 'http://localhost/login'; //path to your script

$client = new Google_Client();
$client->setClientId($google_client_id);
$client->setClientSecret($google_client_secret);
$client->setRedirectUri($google_redirect_url);
$client->setScopes(array('openid','profile','email'));
$oAuth2 = new Google_Service_Oauth2($client);

/************************************************
  If we have a code back from the OAuth 2.0 flow,
  we need to exchange that with the authenticate()
  function. We store the resultant access token
  bundle in the session, and redirect to ourself.
 ************************************************/
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
//  $app->redirect("http://"  . $_SERVER['HTTP_HOST']);
}

/************************************************
  If we have an access token, we can make
  requests, else we generate an authentication URL.
 ************************************************/
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $authUrl = $client->createAuthUrl();
}

/************************************************
  If we're signed in we can go ahead and retrieve
  the ID token, which is part of the bundle of
  data that is exchange in the authenticate step
  - we only need to do a network call if we have
  to retrieve the Google certificate to verify it,
  and that can be cached.
 ************************************************/
if ($client->getAccessToken()) {
  $_SESSION['access_token'] = $client->getAccessToken();
  $token_data = $client->verifyIdToken()->getAttributes();
  $user = $oAuth2->userinfo->get();
}

if (isset($authUrl)) {
	makePage("login.twig",array("authUrl"=>$authUrl));
} 
if (isset($user)) {
	$stored_user = R::find('user','google_id = ?',array($user["id"]));
	if($stored_user){
		$stored_user = array_shift($stored_user);
		$id = $stored_user->id;
	}else{
		$stored_user = R::dispense( 'user' );
		$stored_user->fname = $user["givenName"];
		$stored_user->lname = $user["familyName"];
		$stored_user->google_id = $user["id"];
		$stored_user->email = $user["email"];
		$stored_user->picture = $user["picture"];
		$stored_user->username = $user["email"];
		$stored_user->role = "member";
		$id = R::store($stored_user);
	}
	$app->setCookie("_hopsauth", $id,"2 days","/");
	$app->redirect('http://' . $_SERVER['HTTP_HOST']);
}
?>