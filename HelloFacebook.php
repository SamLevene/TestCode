<?php 
session_start();

require_once( 'Facebook/HttpClients/FacebookHttpable.php' );
require_once( 'Facebook/HttpClients/FacebookCurl.php' );
require_once( 'Facebook/HttpClients/FacebookCurlHttpClient.php' );

require_once( 'Facebook/Entities/AccessToken.php' );
require_once( 'Facebook/Entities/SignedRequest.php' );

require_once( 'Facebook/FacebookSession.php' );
require_once( 'Facebook/FacebookRedirectLoginHelper.php' );
require_once( 'Facebook/FacebookRequest.php' );
require_once( 'Facebook/FacebookResponse.php' );
require_once( 'Facebook/FacebookSDKException.php' );
require_once( 'Facebook/FacebookRequestException.php' );
require_once( 'Facebook/FacebookAuthorizationException.php' );
require_once( 'Facebook/GraphObject.php' );
require_once( 'Facebook/GraphSessionInfo.php' );

use Facebook\HttpClients\FacebookHttpable;
use Facebook\HttpClients\FacebookCurl;
use Facebook\HttpClients\FacebookCurlHttpClient;

use Facebook\Entities\AccessToken;
use Facebook\Entities\SignedRequest;

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;
 
$appid = '1524849001085052'; // your AppID
$secret = 'cbbe5f21dbf6ab222ef47eb0aa81cd0a'; // your secret
 
// Initialize app with app id (APPID) and secret (SECRET)
FacebookSession::setDefaultApplication($appid ,$secret);
 
// login helper with redirect_uri
$helper = new FacebookRedirectLoginHelper( 'http://localhost/HelloFacebook.php' );
 
// see if a existing session exists
if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
  // create new session from saved access_token
  $session = new FacebookSession( $_SESSION['fb_token'] );
  
  // validate the access_token to make sure it's still valid
  try {
    if ( !$session->validate() ) {
      $session = null;
    }
  } catch ( Exception $e ) {
    // catch any exceptions
    $session = null;
  }
}  
 
if ( !isset( $session ) || $session === null ) {
  // no session exists
  
  try {
    $session = $helper->getSessionFromRedirect();
  } catch( FacebookRequestException $ex ) {
    // When Facebook returns an error
    // handle this better in production code
    print_r( $ex );

  } catch( Exception $ex ) {
    // When validation fails or other local issues
    // handle this better in production code
    print_r( $ex );
  }
  
}
 
// see if we have a session
if ( isset( $session ) ) {
  
  // save the session
  $_SESSION['fb_token'] = $session->getToken();

  // create a session using saved token or the new one we generated at login
  $session = new FacebookSession( $session->getToken() );

  // Get likes
  $likes = file_get_contents("http://graph.facebook.com/?ids=152453534809035");
  $likes = json_decode($likes, true);
  $likes2 = file_get_contents("http://graph.facebook.com/?ids=237693566402775");
  $likes2 = json_decode($likes2, true);

  // print data
  echo "ePoints Likes: " . $likes[152453534809035][likes] . "<br />";
  echo "bigDL Likes: " . $likes2[237693566402775][likes] . "<br />";

  echo '<a href="' . $helper->getLogoutUrl( $session, 'http://localhost/HelloFacebook.php?link=logout' ) . '">Logout</a>';

} else {
  // show login url
  echo '<a href="' . $helper->getLoginUrl( array( 'email', 'user_friends' ) ) . '">Login</a>';
}

if(isset($_GET["link"]))
{
  $link = $_GET["link"];
  if($link == "logout")
  {
     session_destroy();
  }
}
?>
