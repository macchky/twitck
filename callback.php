<?php
// example
require_once './Twitter/twitteroauth.php';
require_once './config.php';


 
 if (!isset($_REQUEST['oauth_token'])) {
  
  header('Location: ./clear.php');
}


 $oauth_token = $_REQUEST['oauth_token'];
 
 $connection = new TwitterOAuth($conf['consumerKey'], $conf['consumerSecret'], $_SESSION['request_token'], $_SESSION['request_token_secret']);
 $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
 $token = (object) $access_token;
 $_SESSION['TWITTER_ACCESS_TOKEN'] = serialize($token);
 $token = unserialize($_SESSION['TWITTER_ACCESS_TOKEN']);
 
 header('Location: ./PATHtoindex.php');

 
